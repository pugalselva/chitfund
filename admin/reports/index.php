<?php
// session_start();
include '../../config/database.php';
include '../auth.php';

// if ($_SESSION['role'] !== 'admin') {
//     header('Location: ../../index.php');
//     exit();
// }

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Reports</div>
                    <div class="page-subtitle">Generate and download comprehensive reports</div>
                    
                </div>
                <?php include '../layout/header.php'; ?>
            </div>

            <div class="content">

                <!-- SUMMARY CARDS -->
                <div class="report-cards">

                    <div class="report-card">
                        <small>Total Collections</small>
                        <h2>₹<?= number_format($paymentSummary['collected'] / 100000, 2) ?>L</h2>
                        <small>All time</small>
                    </div>

                    <div class="report-card">
                        <small>Pending Amount</small>
                        <h2 style="color:#ea580c;">
                            ₹<?= number_format($paymentSummary['pending'] / 100000, 2) ?>L
                        </h2>
                        <small>To be collected</small>
                    </div>

                    <div class="report-card">
                        <small>Total Discounts</small>
                        <h2 style="color:#16a34a;">
                            ₹<?= number_format($paymentSummary['discounts'] / 100000, 2) ?>L
                        </h2>
                        <small>From auctions</small>
                    </div>

                    <div class="report-card">
                        <small>Active Members</small>
                        <h2><?= $activeMembers ?></h2>
                        <small>Currently enrolled</small>
                    </div>

                </div>


                <!-- REPORT TYPES -->
                <div class="report-grid">

                    <div class="report-box">
                        <div class="report-left">
                            <div class="report-icon icon-blue">👥</div>
                            <div>
                                <b>Member Report</b><br>
                                <small>Complete list of all members with their details</small><br><br>
                                <small><?= $totalMembers ?> members</small>
                            </div>
                        </div>
                       <!-- <button class="export-btn" onclick="location.href='export_members.php'">⬇ Export</button> -->
                    </div>

                    <div class="report-box">
                        <div class="report-left">
                            <div class="report-icon icon-purple">📈</div>
                            <div>
                                <b>Chit Group Report</b><br>
                                <small>All chit groups with status and progress</small><br><br>
                                <small><?= $totalGroups ?> groups</small>
                            </div>
                        </div>
                        <!-- <button class="export-btn" onclick="location.href='export_groups.php'">⬇ Export</button> -->
                    </div>

                    <div class="report-box">
                        <div class="report-left">
                            <div class="report-icon icon-green">💲</div>
                            <div>
                                <b>Payment Collection Report</b><br>
                                <small>Detailed payment transactions and collections</small><br><br>
                                <small>₹<?= number_format($paymentSummary['collected']) ?> collected</small>
                            </div>
                        </div>
                        <!-- <button class="export-btn" onclick="location.href='export_payments.php'">⬇ Export</button> -->
                    </div>

                    <div class="report-box">
                        <div class="report-left">
                            <div class="report-icon icon-orange">🔨</div>
                            <div>
                                <b>Auction Report</b><br>
                                <small>Auction history with winners and bid amounts</small><br><br>
                                <small><?= $completedAuctions ?> auctions completed</small>
                            </div>
                        </div>
                        <!-- <button class="export-btn" onclick="location.href='export_auctions.php'">⬇ Export</button> -->
                    </div>

                </div>

                <!-- CUSTOM REPORT -->
                <!-- <div class="custom-box">
                    <b>Custom Report Generation</b><br>
                    <small>Generate custom reports based on date range, chit groups, or specific members.</small>

                    <div class="custom-actions">
                        <button>Monthly Summary</button>
                        <button>Defaulters List</button>
                        <button>Performance Analytics</button>
                    </div>
                </div> -->

            </div>
        </div>
    </div>

</body>

</html>
