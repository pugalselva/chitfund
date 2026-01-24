<?php
// session_start();
include '../../config/database.php';
include '../auth.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groups = $conn->query("
    SELECT id, group_name, auction_type
    FROM chit_groups
    WHERE is_active = 1
");
?>


<!DOCTYPE html>
<html>

<head>
    <title>Create Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .member-list-card {
            height: 100%;
            background: #f8fafc;
        }

        .member-list-scroll {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Create Auction</h4>
                        <small class="text-secondary">Schedule a new auction event</small>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="row g-4">

                    <!-- Left: Form -->
                    <div class="col-12 col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold text-primary"><i class="fas fa-gavel me-2"></i>Auction
                                    Details</h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="post" action="store.php">

                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="chit_group_id" id="groupSelect" required>
                                            <option value="" selected disabled>Select Group</option>
                                            <?php while ($g = $groups->fetch_assoc()): ?>
                                                <option value="<?= $g['id'] ?>" data-type="<?= $g['auction_type'] ?>">
                                                    <?= htmlspecialchars($g['group_name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <label for="groupSelect">Chit Group *</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="auction_type" id="auctionType" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="Reverse">Reverse (Lowest Bid Wins)</option>
                                            <option value="Open">Open (Kulukal)</option>
                                        </select>
                                        <label for="auctionType">Auction Type *</label>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="datetime-local" name="auction_datetime"
                                                    id="auction_datetime" class="form-control" required>
                                                <label for="auction_datetime">Start Date & Time *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="datetime-local" name="auction_end_datetime"
                                                    id="auction_end_datetime" class="form-control" required>
                                                <label for="auction_end_datetime">End Date & Time *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="number" name="starting_bid_amount" id="startingBid"
                                            class="form-control" placeholder="Amount" required>
                                        <label for="startingBid">Starting Bid Amount (₹) *</label>
                                    </div>

                                    <div class="alert alert-light border d-flex align-items-center mb-3">
                                        <i class="fas fa-history text-muted me-2"></i>
                                        <small class="text-secondary" id="lastBidInfo">Select a group to see last
                                            month's bid.</small>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <select name="status" class="form-select">
                                            <option value="upcoming">Upcoming</option>
                                            <option value="active">Active</option>
                                        </select>
                                        <label>Initial Status *</label>
                                    </div>

                                    <div class="d-grid">
                                        <button class="btn btn-primary fw-bold py-2">
                                            <i class="fas fa-check-circle me-1"></i> Create Auction
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Member List -->
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold text-success"><i class="fas fa-users me-2"></i>Member List
                                </h5>
                                <small class="text-secondary">Members in selected group</small>
                            </div>
                            <div class="card-body p-0 pt-3">
                                <ul id="memberList" class="list-group list-group-flush member-list-scroll px-2 pb-2">
                                    <li class="list-group-item text-center text-muted border-0 py-5">
                                        <i class="fas fa-layer-group fa-2x mb-3 opacity-25"></i>
                                        <p>Select a group to load members.</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
    <script>
        const groupSelect = document.getElementById('groupSelect');
        const auctionType = document.getElementById('auctionType');
        const memberList = document.getElementById('memberList');
        const lastBidInfo = document.getElementById('lastBidInfo');
        const startingBid = document.getElementById('startingBid');

        groupSelect.addEventListener('change', () => {
            const groupId = groupSelect.value;
            const selected = groupSelect.options[groupSelect.selectedIndex];

            // set auction type automatically
            auctionType.value = selected.dataset.type || '';

            memberList.innerHTML = '<li class="list-group-item text-center text-muted border-0 py-4"><div class="spinner-border text-primary spinner-border-sm me-2"></div>Loading members...</li>';
            lastBidInfo.innerText = 'Fetching last bid...';
            startingBid.value = '';

            if (!groupId) return;

            // fetch members
            fetch('get-group-members.php?group_id=' + groupId)
                .then(res => res.json())
                .then(data => {
                    memberList.innerHTML = '';
                    if (data.length === 0) {
                        memberList.innerHTML = '<li class="list-group-item text-center text-muted border-0 py-4">No members in this group.</li>';
                    } else {
                        data.forEach(m => {
                            memberList.innerHTML += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-dark">${m.full_name}</span>
                                        <small class="text-muted" style="font-size:0.75rem">ID: #${m.member_id}</small>
                                    </div>
                                    <span class="badge bg-light text-dark border">Eligible</span>
                                </li>`;
                        });
                    }
                });

            // fetch last bid
            fetch('get-last-bid.php?group_id=' + groupId)
                .then(res => res.text())
                .then(amount => {
                    if (amount) {
                        lastBidInfo.innerHTML = 'Last month bid: <span class="fw-bold text-dark">₹' + amount + '</span>';
                        startingBid.value = amount;
                    } else {
                        lastBidInfo.innerText = 'No previous auction data found.';
                    }
                });
        });
    </script>
</body>

</html>