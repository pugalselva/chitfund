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
<html>

<head>
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Member Dashboard</div>
                    <div class="page-subtitle">Welcome back, <?= htmlspecialchars($name) ?>!</div>
                </div>
                <div style="text-align:right;">
                    <b><?= htmlspecialchars($name) ?></b><br>
                    <small><?= htmlspecialchars($email) ?></small><br>
                    <a href="../logout.php" class="btn btn-danger" style="margin-top:6px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>

            <div class="content">
                <!-- TOP CARDS -->
                <div class="cards">
                    <div class="card">
                        <h4>Active Chits</h4>
                        <h2><?= $activeChits ?></h2>
                    </div>

                    <div class="card">
                        <h4>Total Contributions</h4>
                        <h2>₹<?= number_format($totalPaid) ?></h2>
                    </div>

                    <div class="card">
                        <h4>Upcoming Auction</h4>
                        <h2><?= $upcomingText ?></h2>
                    </div>

                    <div class="card">
                        <h4>Savings Progress</h4>
                        <!-- <h2><?= $progress ?>%</h2> -->
                        <h2><?= $savingsProgress ?>%</h2>

                    </div>

                </div>
                <!-- CONTENT GRID -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <!-- ACTIVE GROUPS -->
                    <div class="box">
                        <h4>My Active Groups</h4><br>
                        <?php while ($g = $groups->fetch_assoc()):
                            $monthly = $g['duration_months'] > 0
                                ? round($g['total_value'] / $g['duration_months'])
                                : 0;
                            ?>
                            <b><?= htmlspecialchars($g['group_name']) ?></b><br>
                            <small>
                                Month <?= $g['completed_months'] + 1 ?> of <?= $g['duration_months'] ?>
                                · ₹<?= number_format($monthly) ?>/month
                            </small>
                            <hr style="margin:15px 0;">
                        <?php endwhile; ?>

                    </div>

                    <!-- RECENT PAYMENTS -->
                    <div class="box">
                        <h4>Recent Payments</h4><br>

                        <?php if ($payments->num_rows === 0): ?>
                            <small>No payments yet</small>
                        <?php endif; ?>

                        <?php while ($p = $payments->fetch_assoc()): ?>
                            <div style="display:flex;justify-content:space-between;">
                                <div>
                                    <b><?= htmlspecialchars($p['group_name']) ?></b><br>
                                    <small>Month <?= $p['month_no'] ?></small>
                                </div>
                                <div style="text-align:right;">
                                    ₹<?= number_format($p['final_amount']) ?><br>
                                    <small style="color:<?= $p['status'] == 'paid' ? 'green' : '#ea580c' ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </small>
                                </div>
                            </div>
                            <hr style="margin:15px 0;">
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>