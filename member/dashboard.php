<?php
include 'auth.php';
include '../config/database.php';

$memberId = $_SESSION['member_id'];
$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

/* =========================
   ACTIVE CHITS COUNT
========================= */
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT g.id) AS total
    FROM chit_groups g
    JOIN chit_group_members gm ON gm.group_id = g.id
    WHERE gm.member_id = ?
      AND g.is_active = 1
");

$stmt->bind_param("s", $memberId);
$stmt->execute();
$activeChits = (int) $stmt->get_result()->fetch_assoc()['total'];

/* =========================
   TOTAL CONTRIBUTIONS
========================= */
$stmt = $conn->prepare("
    SELECT IFNULL(SUM(final_amount),0) AS total_paid
    FROM payments
    WHERE member_id = ?
      AND status = 'paid'
");
$stmt->bind_param("s", $memberId);
$stmt->execute();
$totalPaid = (int) $stmt->get_result()->fetch_assoc()['total_paid'];

/* =========================
   UPCOMING AUCTION
========================= */
$stmt = $conn->prepare("
    SELECT a.auction_datetime
    FROM auctions a
    JOIN chit_group_members gm ON gm.group_id = a.chit_group_id
    WHERE gm.member_id = ?
      AND a.auction_datetime > NOW()
      AND a.is_active = 1
    ORDER BY a.auction_datetime ASC
    LIMIT 1
");

$stmt->bind_param("s", $memberId);
$stmt->execute();
$nextAuction = $stmt->get_result()->fetch_assoc();

$upcomingText = '—';
if ($nextAuction) {
    $days = floor((strtotime($nextAuction['auction_datetime']) - time()) / 86400);
    $upcomingText = $days > 0 ? "$days days" : 'Today';
}

/* =========================
   SAVINGS PROGRESS
========================= */
$stmt = $conn->prepare("
    SELECT COUNT(a.id) AS completed
    FROM auctions a
    JOIN chit_group_members gm ON gm.group_id = a.chit_group_id
    WHERE gm.member_id = ?
      AND a.auction_end_datetime < NOW()
");

$stmt->bind_param("s", $memberId);
$stmt->execute();
$completedMonths = (int) $stmt->get_result()->fetch_assoc()['completed'];

$stmt = $conn->prepare("
    SELECT IFNULL(SUM(g.duration_months),0) AS total_months
    FROM chit_groups g
    JOIN chit_group_members gm ON gm.group_id = g.id
    WHERE gm.member_id = ?
      AND g.status = 'active'
");
$stmt->bind_param("s", $memberId);
$stmt->execute();
$totalMonths = (int) $stmt->get_result()->fetch_assoc()['total_months'];

$savingsProgress = $totalMonths > 0
    ? round(($completedMonths / $totalMonths) * 100)
    : 0;

/* =========================
   MY ACTIVE GROUPS
========================= */
$stmt = $conn->prepare("
    SELECT 
        g.group_name,
        g.total_value,
        g.duration_months,
        COUNT(a.id) AS completed_months
    FROM chit_groups g
    JOIN chit_group_members gm ON gm.group_id = g.id
    LEFT JOIN auctions a 
        ON a.chit_group_id = g.id 
       AND a.auction_end_datetime < NOW()
    WHERE gm.member_id = ?
      AND g.is_active = 1
    GROUP BY g.id
    LIMIT 3
");

$stmt->bind_param("s", $memberId);
$stmt->execute();
$groups = $stmt->get_result();

/* =========================
   RECENT PAYMENTS
========================= */
$stmt = $conn->prepare("
    SELECT 
        p.final_amount,
        p.status,
        p.month_no,
        g.group_name
    FROM payments p
    JOIN chit_groups g ON g.id = p.chit_group_id
    WHERE p.member_id = ?
    ORDER BY p.created_at DESC
    LIMIT 3
");
$stmt->bind_param("s", $memberId);
$stmt->execute();
$payments = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
        }

        .stat-icon {
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 5rem;
            opacity: 0.1;
            z-index: 1;
            transform: rotate(-15deg);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #fc4a1a, #f7b733);
            color: white;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            color: white;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #f3f4f6;
            padding: 1rem 0;
            transition: background 0.2s;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: #f9fafb;
            padding-left: 0.5rem;
            border-radius: 8px;
        }

        .progress-sm {
            height: 8px;
            border-radius: 4px;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .card-header-custom {
            background: transparent;
            border-bottom: 1px solid #f3f4f6;
            padding: 1.2rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex" id="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-light text-primary me-3 d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">Dashboard</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">Welcome back,
                                <?= htmlspecialchars($name) ?>!
                            </p>
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
                <!-- Stats Row -->
                <div class="row g-4 mb-4">
                    <!-- Active Chits -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card bg-gradient-primary">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase mb-2 text-white-50">Active Chits</h6>
                                <h2 class="display-6 fw-bold mb-0"><?= $activeChits ?></h2>
                                <i class="fas fa-layer-group stat-icon"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Contributions -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card bg-gradient-success">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase mb-2 text-white-50">Total Paid</h6>
                                <h2 class="display-6 fw-bold mb-0">₹<?= number_format($totalPaid) ?></h2>
                                <i class="fas fa-wallet stat-icon"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Auction -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card bg-gradient-warning">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase mb-2 text-white-50">Next Auction</h6>
                                <h2 class="display-6 fw-bold mb-0"><?= $upcomingText ?></h2>
                                <i class="fas fa-gavel stat-icon"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Savings Progress -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card bg-gradient-info">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase mb-2 text-white-50">Savings Progress</h6>
                                <div class="d-flex align-items-center">
                                    <h2 class="display-6 fw-bold mb-0 me-3"><?= $savingsProgress ?>%</h2>
                                </div>
                                <div class="progress mt-2 bg-white bg-opacity-25" style="height: 6px;">
                                    <div class="progress-bar bg-white" style="width: <?= $savingsProgress ?>%"></div>
                                </div>
                                <i class="fas fa-chart-line stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="row g-4">
                    <!-- Active Groups -->
                    <div class="col-12 col-lg-6">
                        <div class="card card-custom h-100 bg-white">
                            <div
                                class="card-header card-header-custom d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>My Active Groups
                                </h6>
                                <a href="my-chits.php"
                                    class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php while ($g = $groups->fetch_assoc()):
                                        $monthly = $g['duration_months'] > 0
                                            ? round($g['total_value'] / $g['duration_months'])
                                            : 0;
                                        $progress = ($g['completed_months'] / $g['duration_months']) * 100;
                                        ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($g['group_name']) ?>
                                                </h6>
                                                <small
                                                    class="fw-bold text-primary">₹<?= number_format($monthly) ?>/mo</small>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted small mb-2">
                                                <span>Month <?= $g['completed_months'] + 1 ?> of
                                                    <?= $g['duration_months'] ?></span>
                                                <span><?= round($progress) ?>% Completed</span>
                                            </div>
                                            <div class="progress progress-sm bg-light">
                                                <div class="progress-bar bg-primary rounded-pill" role="progressbar"
                                                    style="width: <?= $progress ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="col-12 col-lg-6">
                        <div class="card card-custom h-100 bg-white">
                            <div
                                class="card-header card-header-custom d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-success"></i>Recent Payments
                                </h6>
                                <a href="payment-history.php"
                                    class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php if ($payments->num_rows === 0): ?>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-receipt fa-2x mb-2 opacity-50"></i>
                                            <p>No payments recorded yet</p>
                                        </div>
                                    <?php endif; ?>

                                    <?php while ($p = $payments->fetch_assoc()): ?>
                                        <div class="list-group-item px-0 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light p-2 me-3 d-flex align-items-center justify-content-center"
                                                    style="width:40px;height:40px">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">
                                                        <?= htmlspecialchars($p['group_name']) ?>
                                                    </h6>
                                                    <small class="text-muted">Month <?= $p['month_no'] ?></small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <h6 class="mb-0 fw-bold text-dark">₹<?= number_format($p['final_amount']) ?>
                                                </h6>
                                                <span
                                                    class="badge bg-<?= $p['status'] == 'paid' ? 'success' : 'warning' ?> bg-opacity-10 text-<?= $p['status'] == 'paid' ? 'success' : 'warning' ?> rounded-pill px-2">
                                                    <?= ucfirst($p['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <!-- Custom Scripts -->
        <script src="../assets/js/scripts.js"></script>
</body>

</html>