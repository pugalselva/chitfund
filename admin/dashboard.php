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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="wrapper">

        <?php include 'layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Dashboard</div>
                </div>

                <?php include 'layout/header.php'; ?>
            </div>



            <div class="content">
                <p style="color:#6b7280;">Welcome back! Here's your overview.</p>

                <div class="cards">
                    <div class="card">
                        <i class="fa-solid fa-layer-group fa-bounce"></i>
                        <h4>Active Groups</h4>
                        <h2><?= $activeGroups ?></h2>
                    </div>

                    <div class="card">
                        <i class="fa-solid fa-users fa-beat"></i>
                        <h4>Total Members</h4>
                        <h2><?= $totalMembers ?></h2>
                    </div>

                    <div class="card">
                        <i class="fa-solid fa-gavel fa-spin-pulse"></i>
                        <h4>Live Auctions</h4>
                        <h2><?= $liveAuctions ?></h2>
                        <small><?= $upcomingAuctions ?> upcoming</small>
                    </div>

                    <div class="card">
                        <i class="fa-solid fa-indian-rupee-sign fa-fade"></i>
                        <h4>Total Collections</h4>
                        <h2>₹<?= number_format($totalCollections) ?></h2>
                    </div>
                </div>


                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div class="box">
                        <h4>Recent Auctions</h4><br>

                        <?php if ($recentAuctions->num_rows === 0): ?>
                        <small>No completed auctions</small>
                        <?php endif; ?>

                        <?php while($a = $recentAuctions->fetch_assoc()): ?>
                        <b><?= htmlspecialchars($a['group_name']) ?> – Month <?= $a['auction_month'] ?></b><br>
                        <small>
                            Winner: <?= htmlspecialchars($a['full_name'] ?? '—') ?>
                            ₹<?= number_format($a['winning_bid_amount'] ?? 0) ?>
                        </small>
                        <hr style="margin:10px 0;">
                        <?php endwhile; ?>
                    </div>


                    <div class="box">
                        <h4>Recent Payments</h4><br>

                        <?php if ($recentPayments->num_rows === 0): ?>
                        <small>No payments yet</small>
                        <?php endif; ?>

                        <?php while($p = $recentPayments->fetch_assoc()): ?>
                        <b><?= htmlspecialchars($p['full_name']) ?></b> – ₹<?= number_format($p['final_amount']) ?><br>
                        <small><?= date('d M Y', strtotime($p['payment_date'])) ?></small>
                        <hr style="margin:10px 0;">
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php if ($pendingPayments > 0): ?>
                <div class="alert">
                    <i class="fa-solid fa-triangle-exclamation fa-shake"></i>
                    💰 <b>You have <?= $pendingPayments ?> pending payment(s)</b><br>
                    Please follow up with members
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
