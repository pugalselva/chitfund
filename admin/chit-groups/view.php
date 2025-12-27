<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare('SELECT * FROM chit_groups WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();

if (!$group) {
    die('Group not found');
}
$assigned = $conn->query("
    SELECT m.member_id, m.full_name
    FROM chit_group_members cgm
    JOIN members m ON cgm.member_id = m.member_id
    WHERE cgm.group_id = $id
");

$completed = $group['completed_months'];
$total = $group['duration_months'];
$remaining = $total - $completed;
$progress = $total > 0 ? round(($completed / $total) * 100) : 0;
?>


<!DOCTYPE html>
<html>

<head>
    <title>Chit Group Details</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">

        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title"><?= $group['group_name'] ?></div>
                    <div class="page-subtitle">Chit group details and information</div>
                </div>
            </div>

            <div class="content">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                    <!-- GROUP INFO -->
                    <div class="info-box">
                        <h4>Group Information</h4><br>

                        <div class="info-row">
                            <span>Status</span>
                            <span class="status"><?= ucfirst($group['status']) ?></span>
                        </div>

                        <div class="info-row">
                            <span>Total Members</span>
                            <span><?= $group['total_members'] ?></span>
                        </div>

                        <div class="info-row">
                            <span>Monthly Contribution</span>
                            <span>₹<?= number_format($group['monthly_contribution']) ?></span>
                        </div>

                        <div class="info-row">
                            <span>Pool Amount</span>
                            <span>₹<?= number_format($group['total_value']) ?></span>
                        </div>

                        <div class="info-row">
                            <span>Duration</span>
                            <span><?= $group['duration_months'] ?> months</span>
                        </div>

                        <div class="info-row">
                            <span>Start Date</span>
                            <span><?= date('d-m-Y', strtotime($group['start_date'])) ?></span>
                        </div>
                    </div>

                    <!-- PROGRESS -->
                    <div class="info-box">
                        <h4>Progress</h4><br>

                        <div style="display:flex;justify-content:space-between;">
                            <span>Current Month</span>
                            <span><?= $completed ?> / <?= $total ?></span>
                        </div>

                        <div class="progress-bar">
                            <div class="progress" style="width:<?= $progress ?>%"></div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
                            <div class="stat-box green">
                                <h2><?= $completed ?></h2>
                                <small>Completed</small>
                            </div>

                            <div class="stat-box blue">
                                <h2><?= $remaining ?></h2>
                                <small>Remaining</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
</body>

</html>
