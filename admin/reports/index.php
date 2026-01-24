<?php
// session_start();
include '../../config/database.php';
include '../auth.php';

/* ---- PAYMENTS SUMMARY ---- */
$paymentSummary = $conn
    ->query(
        "
    SELECT
        SUM(CASE WHEN status='paid' THEN final_amount ELSE 0 END) AS collected,
        SUM(CASE WHEN status IN ('pending','overdue') THEN final_amount ELSE 0 END) AS pending,
        SUM(discount_amount) AS discounts
    FROM payments
",
    )
    ->fetch_assoc();

/* ---- ACTIVE MEMBERS ---- */
$activeMembers = $conn
    ->query(
        "
    SELECT COUNT(*) AS total
    FROM members
    WHERE is_active=1
",
    )
    ->fetch_assoc()['total'];

/* ---- TOTAL MEMBERS ---- */
$totalMembers = $conn
    ->query(
        "
    SELECT COUNT(*) AS total FROM members
",
    )
    ->fetch_assoc()['total'];

/* ---- CHIT GROUPS ---- */
$totalGroups = $conn
    ->query(
        "
    SELECT COUNT(*) AS total FROM chit_groups
",
    )
    ->fetch_assoc()['total'];

/* ---- AUCTIONS ---- */
$completedAuctions = $conn
    ->query(
        "
    SELECT COUNT(*) AS total
    FROM auctions
    WHERE status='completed'
",
    )
    ->fetch_assoc()['total'];
?>


<!DOCTYPE html>
<html>

<head>
    <title>Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Reports</h4>
                        <small class="text-secondary">Generate and view system analytics</small>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="row g-4 mb-5">

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total
                                    Collections</small>
                                <h3 class="fw-bold mt-2 text-primary">
                                    ₹<?= number_format($paymentSummary['collected'] / 100000, 2) ?>L</h3>
                                <small class="text-secondary"><i class="fas fa-check-circle me-1"></i> Lifetime</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Pending
                                    Amount</small>
                                <h3 class="fw-bold mt-2 text-warning">
                                    ₹<?= number_format($paymentSummary['pending'] / 100000, 2) ?>L</h3>
                                <small class="text-secondary"><i class="fas fa-clock me-1"></i> To be collected</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total
                                    Discounts</small>
                                <h3 class="fw-bold mt-2 text-success">
                                    ₹<?= number_format($paymentSummary['discounts'] / 100000, 2) ?>L</h3>
                                <small class="text-secondary"><i class="fas fa-percent me-1"></i> From auctions</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Active
                                    Members</small>
                                <h3 class="fw-bold mt-2"><?= $activeMembers ?></h3>
                                <small class="text-secondary"><i class="fas fa-users me-1"></i> Currently
                                    enrolled</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REPORT MODULES -->
                <h5 class="fw-bold mb-3">Available Reports</h5>
                <div class="row g-4">

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-users text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Member Report</h6>
                                    <p class="text-secondary small mb-1">Complete list of all registered members and
                                        their details.</p>
                                    <span class="badge bg-light text-dark order"><?= $totalMembers ?> records</span>
                                </div>
                                <!-- <a href="#" class="btn btn-outline-primary ms-auto btn-sm"><i class="fas fa-download"></i></a> -->
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-layer-group text-info fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Chit Group Report</h6>
                                    <p class="text-secondary small mb-1">Overview of all chit groups, status, and
                                        progress.</p>
                                    <span class="badge bg-light text-dark order"><?= $totalGroups ?> groups</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-coins text-success fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Payment Collection</h6>
                                    <p class="text-secondary small mb-1">Detailed transaction history of all
                                        collections.</p>
                                    <span
                                        class="badge bg-light text-dark order">₹<?= number_format($paymentSummary['collected']) ?>
                                        collected</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-gavel text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Auction History</h6>
                                    <p class="text-secondary small mb-1">Archive of all completed auctions and winning
                                        bids.</p>
                                    <span class="badge bg-light text-dark order"><?= $completedAuctions ?>
                                        auctions</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
</body>

</html>