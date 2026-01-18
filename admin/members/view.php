<?php
// session_start();
include '../../config/database.php';

include '../auth.php';

if (!isset($_GET['id'])) {
    die('Member ID missing');
}

$member_id = $_GET['id'];

/* ================= MEMBER + BANK ================= */
$stmt = $conn->prepare("
    SELECT 
        m.*,
        b.account_name,
        b.account_no,
        b.bank_name,
        b.ifsc,
        b.upi_id,
        b.cheque_photo
    FROM members m
    LEFT JOIN member_bank_details b 
        ON b.member_id = m.member_id
    WHERE m.member_id = ?
");

if (!$stmt) {
    die("SQL Prepare Failed: " . $conn->error);
}

$stmt->bind_param("s", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$m = $result->fetch_assoc();


/* ================= AGE ================= */
$dob = new DateTime($m['dob']);
$today = new DateTime();
$age = $today->diff($dob)->y;

/* ================= ACTIVITY ================= */

// Active groups
// $gStmt = $conn->prepare("
//     SELECT COUNT(*) total 
//     FROM group_members 
//     WHERE member_id = ? AND is_active = 1
// ");
// $gStmt->bind_param("s", $member_id);
// $gStmt->execute();
// $groups = $gStmt->get_result()->fetch_assoc()['total'] ?? 0;

// // Total payments
// $pStmt = $conn->prepare("
//     SELECT IFNULL(SUM(amount),0) total 
//     FROM payments 
//     WHERE member_id = ?
// ");
// $pStmt->bind_param("s", $member_id);
// $pStmt->execute();
// $totalAmount = $pStmt->get_result()->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Member Profile</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <h2><?= htmlspecialchars($m['full_name']) ?></h2>
                    <small>Member ID: <?= htmlspecialchars($m['member_id']) ?></small>
                </div>
            </div>
            <?php include '../layout/header.php'; ?>

            <div class="content">

                <div class="tabs">
                    <a href="#" id="tabPersonal" class="active" onclick="showTab('personal')">👤 Personal</a>
                    <a href="#" id="tabBank" onclick="showTab('bank')">🏦 Bank</a>
                    <a href="#" id="tabActivity" onclick="showTab('activity')">📊 Activity</a>
                </div>

                <!-- PERSONAL -->
                <div id="personal" class="tab-content profile-box">
                    <h4>Personal Information</h4><br>
                    <div class="info-grid">
                        <div>
                            <b>Name</b><br><?= htmlspecialchars($m['full_name']) ?><br><br>
                            <b>DOB</b><br><?= date('d/m/Y', strtotime($m['dob'])) ?><br><br>
                            <b>Address</b><br><?= nl2br(htmlspecialchars($m['address'])) ?>
                        </div>
                        <div>
                            <b>Gender</b><br><?= htmlspecialchars($m['gender']) ?><br><br>
                            <b>Age</b><br><?= $age ?> years<br><br>
                            <b>Mobile</b><br><?= htmlspecialchars($m['mobile']) ?>
                        </div>
                    </div>
                </div>

                <!-- BANK -->
                <div id="bank" class="tab-content profile-box" style="display:none;">
                    <h4>Bank Details</h4><br>

                    <?php if (!empty($m['account_no'])): ?>
                    <div class="info-grid">
                        <div>
                            <b>Account Holder</b><br><?= htmlspecialchars($m['account_name']) ?><br><br>
                            <b>Account No</b><br><?= htmlspecialchars($m['account_no']) ?><br><br>
                            <b>Bank</b><br><?= htmlspecialchars($m['bank_name']) ?>
                        </div>
                        <div>
                            <b>IFSC</b><br><?= htmlspecialchars($m['ifsc']) ?><br><br>
                            <b>UPI</b><br><?= htmlspecialchars($m['upi_id'] ?: '-') ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <p style="color:#888;">No bank details available</p>
                    <?php endif; ?>
                </div>

                <!-- ACTIVITY -->
                <!-- <div id="activity" class="tab-content profile-box" style="display:none;">
<h4>Member Activity</h4><br>

<div class="info-grid">
    <div class="stat-card stat-blue">
        <h2><?= $groups ?></h2>
        <small>Active Groups</small>
    </div>

    <div class="stat-card stat-green">
        <h2>₹<?= number_format($totalAmount) ?></h2>
        <small>Total Contributions</small>
    </div>

    <div class="stat-card stat-purple">
        <h2><?= (int)((time() - strtotime($m['joining_date'])) / (30*24*60*60)) ?></h2>
        <small>Member Since (Months)</small>
    </div>
</div>
</div> -->

                <br>
                <span class="badge <?= $m['is_active'] ? 'active' : 'inactive' ?>">
                    <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
                </span>

            </div>
        </div>
    </div>

    <script>
    function showTab(tab) {
        ['personal', 'bank', 'activity'].forEach(t => {
            document.getElementById(t).style.display = 'none';
            document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1))
                .classList.remove('active');
        });

        document.getElementById(tab).style.display = 'block';
        document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1))
            .classList.add('active');
    }
    </script>

</body>

</html>