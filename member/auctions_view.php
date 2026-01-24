<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$memberId = $_SESSION['member_id'];

/* Auctions for member's groups */
$stmt = $conn->prepare("
SELECT 
    a.id,
    a.auction_month,
    a.auction_datetime,
    a.auction_end_datetime,
    a.status,
    g.group_name,
    g.auction_type
FROM auctions a
JOIN chit_groups g ON g.id = a.chit_group_id
JOIN chit_group_members gm ON gm.group_id = g.id
WHERE gm.member_id = ?
AND a.status IN ('upcoming','active')
ORDER BY a.auction_datetime ASC
");
$stmt->bind_param('s', $memberId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Auctions</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-custom tbody tr {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .table-custom tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            z-index: 1;
            position: relative;
        }

        .table-custom td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .table-custom td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .table-custom td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .countdown {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #d63384;
            background: rgba(214, 51, 132, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
        }

        .auction-card-mobile {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex" id="wrapper" data-server-time="<?= time() * 1000 ?>">
        <?php include 'layout/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            <!-- Navbar -->
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-light text-primary me-3 d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">My Auctions</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">Upcoming & Live auctions</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($name) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($email) ?></small>
                        </div>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">

                <?php if ($result->num_rows === 0): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-gavel fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">No upcoming auctions found.</h5>
                    </div>
                <?php else: ?>

                    <!-- Desktop Table View -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover">
                                <thead class="text-muted text-uppercase small fw-bold">
                                    <tr>
                                        <th>Chit Group</th>
                                        <th>Month</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Countdown</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Reset pointer to reuse result set if needed or fetch into array first
                                    // Ideally fetch all to avoid pointer issues if reusing
                                    $auctions = [];
                                    while ($row = $result->fetch_assoc()) {
                                        $auctions[] = $row;
                                    }

                                    foreach ($auctions as $a):
                                        ?>
                                        <tr data-start="<?= strtotime($a['auction_datetime']) ?>"
                                            data-end="<?= strtotime($a['auction_end_datetime']) ?>">
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($a['group_name']) ?></div>
                                                <span class="badge bg-light text-secondary border">
                                                    <?= $a['auction_type'] == 'Open' ? 'Kulukkal' : 'Reverse' ?>
                                                </span>
                                            </td>
                                            <td>Month <?= $a['auction_month'] ?></td>
                                            <td>
                                                <div class="fw-medium"><?= date('d M Y', strtotime($a['auction_datetime'])) ?>
                                                </div>
                                                <small
                                                    class="text-muted"><?= date('H:i', strtotime($a['auction_datetime'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="fw-medium">
                                                    <?= date('d M Y', strtotime($a['auction_end_datetime'])) ?>
                                                </div>
                                                <small
                                                    class="text-muted"><?= date('H:i', strtotime($a['auction_end_datetime'])) ?></small>
                                            </td>
                                            <td><span class="countdown">—</span></td>
                                            <td>
                                                <span
                                                    class="badge rounded-pill bg-<?= $a['status'] === 'active' ? 'success' : ($a['status'] === 'completed' ? 'secondary' : 'warning') ?> bg-opacity-25 text-<?= $a['status'] === 'active' ? 'success' : ($a['status'] === 'completed' ? 'secondary' : 'dark') ?> border-<?= $a['status'] === 'active' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($a['status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php
                                                $now = time();
                                                $start = strtotime($a['auction_datetime']);
                                                $end = strtotime($a['auction_end_datetime']);

                                                if ($a['auction_type'] === 'Open') {
                                                    if ($a['status'] === 'active' && $now >= $start && $now <= $end) {
                                                        echo '<a href="kulukkal-view.php?auction_id=' . $a['id'] . '" class="btn btn-primary btn-sm"><i class="fas fa-ticket-alt me-1"></i>View Kulukkal</a>';
                                                    } elseif ($now < $start) {
                                                        echo '<button class="btn btn-light btn-sm text-muted" disabled><i class="far fa-clock me-1"></i>Upcoming</button>';
                                                    } elseif ($a['status'] === 'completed') {
                                                        echo '<a href="kulukkal-view.php?auction_id=' . $a['id'] . '" class="btn btn-outline-secondary btn-sm"><i class="far fa-eye me-1"></i>Result</a>';
                                                    } else {
                                                        echo '<span class="text-muted small">Ended</span>';
                                                    }
                                                } else {
                                                    if ($a['status'] === 'active' && $now >= $start && $now <= $end) {
                                                        echo '<a href="live-auction.php?auction_id=' . $a['id'] . '" class="btn btn-danger btn-sm pulse-button"><i class="fas fa-gavel me-1"></i>Join Live</a>';
                                                    } elseif ($now < $start) {
                                                        echo '<button class="btn btn-light btn-sm text-muted" disabled><i class="far fa-clock me-1"></i>Upcoming</button>';
                                                    } elseif ($a['status'] === 'completed') {
                                                        echo '<a href="live-auction.php?auction_id=' . $a['id'] . '" class="btn btn-outline-secondary btn-sm"><i class="far fa-eye me-1"></i>Result</a>';
                                                    } else {
                                                        echo '<span class="text-muted small">Ended</span>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-lg-none">
                        <?php foreach ($auctions as $a): ?>
                            <div class="auction-card-mobile" data-start="<?= strtotime($a['auction_datetime']) ?>"
                                data-end="<?= strtotime($a['auction_end_datetime']) ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($a['group_name']) ?></h6>
                                        <small class="text-muted">Month <?= $a['auction_month'] ?> ·
                                            <?= $a['auction_type'] ?></small>
                                    </div>
                                    <span
                                        class="badge rounded-pill bg-<?= $a['status'] === 'active' ? 'success' : ($a['status'] === 'completed' ? 'secondary' : 'warning') ?> bg-opacity-25 text-<?= $a['status'] === 'active' ? 'success' : ($a['status'] === 'completed' ? 'secondary' : 'dark') ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                                    <div><i
                                            class="far fa-clock me-1"></i><?= date('d M h:i A', strtotime($a['auction_datetime'])) ?>
                                    </div>
                                    <div class="countdown fw-bold text-primary">—</div>
                                </div>

                                <div class="d-grid">
                                    <?php
                                    $now = time();
                                    $start = strtotime($a['auction_datetime']);
                                    $end = strtotime($a['auction_end_datetime']);

                                    if ($a['auction_type'] === 'Open') {
                                        if ($a['status'] === 'active' && $now >= $start && $now <= $end) {
                                            echo '<a href="kulukkal-view.php?auction_id=' . $a['id'] . '" class="btn btn-primary btn-sm"><i class="fas fa-ticket-alt me-1"></i>View Kulukkal</a>';
                                        } elseif ($now < $start) {
                                            echo '<button class="btn btn-light btn-sm text-muted" disabled>Upcoming</button>';
                                        } elseif ($a['status'] === 'completed') {
                                            echo '<a href="kulukkal-view.php?auction_id=' . $a['id'] . '" class="btn btn-outline-secondary btn-sm">View Result</a>';
                                        }
                                    } else {
                                        if ($a['status'] === 'active' && $now >= $start && $now <= $end) {
                                            echo '<a href="live-auction.php?auction_id=' . $a['id'] . '" class="btn btn-danger btn-sm"><i class="fas fa-gavel me-1"></i>Join Live Auction</a>';
                                        } elseif ($now < $start) {
                                            echo '<button class="btn btn-light btn-sm text-muted" disabled>Upcoming</button>';
                                        } elseif ($a['status'] === 'completed') {
                                            echo '<a href="live-auction.php?auction_id=' . $a['id'] . '" class="btn btn-outline-secondary btn-sm">View Result</a>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Server time sync
        const wrapper = document.getElementById('wrapper');
        const serverTimeStr = wrapper ? wrapper.dataset.serverTime : Date.now();
        const serverTimeInitial = parseInt(serverTimeStr);
        const performanceStart = performance.now();

        function getCurrentServerTime() {
            const elapsed = performance.now() - performanceStart;
            return serverTimeInitial + elapsed;
        }

        function format(ms) {
            let s = Math.floor(ms / 1000);
            if (s < 0) return "0s";

            let d = Math.floor(s / 86400);
            s %= 86400;
            let h = Math.floor(s / 3600);
            s %= 3600;
            let m = Math.floor(s / 60);
            s %= 60;

            if (d > 0) return `${d}d ${h}h ${m}m`;
            return `${h}h ${m}m ${s}s`;
        }

        function updateCountdowns() {
            const now = getCurrentServerTime();

            // Select all rows (desktop) and cards (mobile)
            const elements = document.querySelectorAll('tr[data-start], .auction-card-mobile[data-start]');

            elements.forEach(el => {
                const start = parseInt(el.dataset.start) * 1000;
                const end = parseInt(el.dataset.end) * 1000;
                const cell = el.querySelector('.countdown');

                if (!cell) return;

                if (now < start) {
                    cell.innerText = 'Starts in ' + format(start - now);
                    cell.className = 'countdown text-info';
                } else if (now >= start && now <= end) {
                    cell.innerText = 'Ends in ' + format(end - now);
                    cell.className = 'countdown text-danger';
                } else {
                    cell.innerText = 'Ended';
                    cell.className = 'countdown text-muted';
                }
            });
        }

        // Init
        updateCountdowns();
        setInterval(updateCountdowns, 1000);

        // Auto-refresh for status updates
        setInterval(() => {
            // Only reload if user is not interacting (optional, simpler to just reload)
            // location.reload();
            // For now, let's keep the reload as per original logic but maybe less frequent
            location.reload();
        }, 5000); // Changed to 5s to be less jarring

        // Toggle Sidebar for Mobile
    </script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>