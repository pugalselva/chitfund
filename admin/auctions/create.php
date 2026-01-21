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
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .member-box {
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 8px;
        }

        .member {
            padding: 6px 0;
            border-bottom: 1px dashed #ddd;
        }
        /* Member Panel */
.member-panel {
    background: #fff;
    border-radius: 10px;
    padding: 16px;
    height: 100%;
}

.member-panel h4 {
    font-size: 16px;
    margin-bottom: 12px;
}

/* Member List */
.member-list {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 280px;
    overflow-y: auto;
}

/* Member Row */
.member-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    background: #f8fafc;
    font-size: 14px;
}

/* Name */
.member-name {
    font-weight: 500;
}

/* Member ID */
.member-id {
    color: #64748b;
    font-size: 12px;
}

/* Status badge */
.member-badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 999px;
}

.member-badge.eligible {
    background: #dcfce7;
    color: #166534;
}

.member-badge.selected {
    background: #dbeafe;
    color: #1e40af;
}

/* Placeholder */
.member-list .placeholder {
    color: #94a3b8;
    font-style: italic;
    text-align: center;
    padding: 20px;
}

    </style>
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Create Auction</div>
                    <div class="page-subtitle">Schedule a new auction for chit group</div>
                </div>
                <?php include '../layout/header.php'; ?>

            </div>

            <div class="content">
                <div class="grid">
                    <div class="form-box" style="max-width:600px;">
                        <h4>Create Auction</h4>

                        <form method="post" action="store.php">

                            <!-- ✅ CHIT GROUP DROPDOWN -->
                            <div class="form-group">
                                <label>Chit Group *</label>
                                <select class="form-control" name="chit_group_id" id="groupSelect" required>
                                    <option value="">Select chit group</option>
                                    <?php while ($g = $groups->fetch_assoc()): ?>
                                    <option value="<?= $g['id'] ?>">
                                        <?= htmlspecialchars($g['group_name']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <!-- AUCTION TYPE -->
                            <div class="form-group">
                                <label>Auction Type *</label>
                                <select name="auction_type" id="auctionType" class="form-control" required>
                                    <option value="">Select auction type</option>
                                    <option value="Reverse">Reverse (Lowest Bid Wins)</option>
                                    <option value="Open">Open (Kulukal)</option>
                                </select>
                            </div>
                            <!-- AUCTION DATE -->
                            <div class="form-group">
                                <label>Auction Date & Time *</label>
                                <input type="datetime-local" name="auction_datetime" class="form-control" required>
                            </div>
                            <!-- AUCTION END -->
                            <div class="form-group">
                                <label>Auction End Date & Time *</label>
                                <input type="datetime-local" name="auction_end_datetime" class="form-control" required>
                            </div>
                            <!-- STARTING BID -->
                            <div class="form-group">
                                <label>Starting Bid Amount *</label>

                                <small id="lastBidInfo" style="display:block;color:#6b7280;margin-bottom:6px;">
                                    Last month bid: —
                                </small>

                                <input type="number" name="starting_bid_amount" id="startingBid" class="form-control"
                                    required>
                            </div>


                            <!-- STATUS -->
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" class="form-control">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>

                            <button class="btn-primary">Create Auction</button>
                            <a href="index.php" class="btn-secondary">Cancel</a>
                        </form>
                    </div>
                    <!-- MEMBER PANEL -->
                    <!-- MEMBER PANEL -->
                    <div class="form-box member-panel">
                        <h4>Member List</h4>

                        <ul id="memberList" class="member-list">
                            <li class="placeholder">Select chit group to view members</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- script -->
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

            memberList.innerHTML = '<li>Loading...</li>';
            lastBidInfo.innerText = 'Last month bid: —';
            startingBid.value = '';

            if (!groupId) return;

            // fetch members
            fetch('get-group-members.php?group_id=' + groupId)
                .then(res => res.json())
                .then(data => {
                    memberList.innerHTML = '';
                    data.forEach(m => {
                        memberList.innerHTML += `<li>${m.member_id} - ${m.full_name}</li>`;
                    });
                });

            // fetch last bid
            fetch('get-last-bid.php?group_id=' + groupId)
                .then(res => res.text())
                .then(amount => {
                    if (amount) {
                        lastBidInfo.innerText = 'Last month bid: ₹' + amount;
                        startingBid.value = amount;
                    }
                });
        });
    </script>
</body>

</html>
