<?php
include 'auth.php';
include '../config/database.php';

/* 1. Active Groups */
$activeGroups = $conn
    ->query(
        "
    SELECT COUNT(*) AS total 
    FROM chit_groups 
    WHERE status='active' AND is_active=1
",
    )
    ->fetch_assoc()['total'];

/* 2. Total Members */
$totalMembers = $conn
    ->query(
        "
    SELECT COUNT(*) AS total 
    FROM members 
    WHERE is_active=1
",
    )
    ->fetch_assoc()['total'];

/* 3. Live Auctions */
$liveAuctions = $conn
    ->query(
        "
    SELECT COUNT(*) AS total 
    FROM auctions 
    WHERE status='active'
",
    )
    ->fetch_assoc()['total'];

/* 4. Upcoming Auctions */
$upcomingAuctions = $conn
    ->query(
        "
    SELECT COUNT(*) AS total 
    FROM auctions 
    WHERE status='upcoming'
",
    )
    ->fetch_assoc()['total'];

/* 5. Total Collections */
$totalCollections = $conn
    ->query(
        "
    SELECT IFNULL(SUM(final_amount),0) AS total 
    FROM payments 
    WHERE status='paid'
",
    )
    ->fetch_assoc()['total'];

/* 6. Pending Payments */
$pendingPayments = $conn
    ->query(
        "
    SELECT COUNT(*) AS total 
    FROM payments 
    WHERE status='pending'
",
    )
    ->fetch_assoc()['total'];

/* 7. Recent Auctions */
$recentAuctions = $conn->query("
    SELECT 
        g.group_name,
        a.auction_month,
        m.full_name,
        a.winning_bid_amount
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    LEFT JOIN members m ON m.member_id = a.winner_member_id
    WHERE a.status='completed'
    ORDER BY a.created_at DESC
    LIMIT 5
");

/* 8. Recent Payments */
$recentPayments = $conn->query("
    SELECT 
        m.full_name,
        p.final_amount,
        p.payment_date
    FROM payments p
    JOIN members m ON m.member_id = p.member_id
    WHERE p.status='paid'
    ORDER BY p.payment_date DESC
    LIMIT 5
");
?>

<?php
$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #wrapper {
            overflow-x: hidden;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin 0.25s ease-out;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
        }

        #sidebar-wrapper .list-group {
            width: 15rem;
        }

        #page-content-wrapper {
            width: 100%;
        }

        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">

        <?php include 'layout/sidebar.php'; ?>

        <div id="page-content-wrapper">

            <?php include './layout/header.php'; ?>

            <div class="container-fluid p-4">
                <p class="text-secondary mb-4">Welcome back! Here's your overview.</p>

                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="fa-solid fa-layer-group fa-bounce text-primary mb-3 fs-3"></i>
                                <h6 class="card-subtitle mb-2 text-muted">Active Groups</h6>
                                <h2 class="card-title mb-0"><?= $activeGroups ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="fa-solid fa-users fa-beat text-success mb-3 fs-3"></i>
                                <h6 class="card-subtitle mb-2 text-muted">Total Members</h6>
                                <h2 class="card-title mb-0"><?= $totalMembers ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="fa-solid fa-gavel fa-spin-pulse text-warning mb-3 fs-3"></i>
                                <h6 class="card-subtitle mb-2 text-muted">Live Auctions</h6>
                                <h2 class="card-title mb-0"><?= $liveAuctions ?></h2>
                                <small class="text-secondary"><?= $upcomingAuctions ?> upcoming</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="fa-solid fa-indian-rupee-sign fa-fade text-info mb-3 fs-3"></i>
                                <h6 class="card-subtitle mb-2 text-muted">Total Collections</h6>
                                <h2 class="card-title mb-0">₹<?= number_format($totalCollections) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h4 class="card-title mb-4">Recent Auctions</h4>

                                <?php if ($recentAuctions->num_rows === 0): ?>
                                    <small class="text-muted">No completed auctions</small>
                                <?php endif; ?>

                                <?php while ($a = $recentAuctions->fetch_assoc()): ?>
                                    <div class="mb-3 border-bottom pb-2 last-no-border">
                                        <div class="d-flex justify-content-between">
                                            <b><?= htmlspecialchars($a['group_name']) ?> – Month
                                                <?= $a['auction_month'] ?></b>
                                            <span
                                                class="text-end fw-bold">₹<?= number_format($a['winning_bid_amount'] ?? 0) ?></span>
                                        </div>
                                        <small class="text-secondary">
                                            Winner: <?= htmlspecialchars($a['full_name'] ?? '—') ?>
                                        </small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>


                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h4 class="card-title mb-4">Recent Payments</h4>

                                <?php if ($recentPayments->num_rows === 0): ?>
                                    <small class="text-muted">No payments yet</small>
                                <?php endif; ?>

                                <?php while ($p = $recentPayments->fetch_assoc()): ?>
                                    <div class="mb-3 border-bottom pb-2 last-no-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <b><?= htmlspecialchars($p['full_name']) ?></b>
                                            <span class="fw-bold">₹<?= number_format($p['final_amount']) ?></span>
                                        </div>
                                        <small
                                            class="text-secondary"><?= date('d M Y', strtotime($p['payment_date'])) ?></small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($pendingPayments > 0): ?>
                    <div class="alert alert-warning mt-4 d-flex align-items-center gap-3 shadow-sm" role="alert">
                        <i class="fa-solid fa-triangle-exclamation fa-shake fs-4"></i>
                        <div>
                            <b>You have <?= $pendingPayments ?> pending payment(s)</b><br>
                            Please follow up with members
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'layout/scripts.php'; ?>
</body>

</html>