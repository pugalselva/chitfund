<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../index.php');
    exit();
}

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$memberId = $_SESSION['member_id'];

/* Get completed auctions where member belongs to group */
$sql = "
SELECT 
    a.id AS auction_id,
    a.auction_month,
    a.auction_datetime,
    a.starting_bid_amount AS pool_amount,
    a.winning_bid_amount,
    a.winner_member_id,
    cg.group_name,
    wm.full_name AS winner_name,
    (
        SELECT COUNT(*) 
        FROM chit_group_members 
        WHERE group_id = a.chit_group_id
    ) AS total_members
FROM auctions a
JOIN chit_groups cg 
    ON cg.id = a.chit_group_id
JOIN chit_group_members cgm 
    ON cgm.group_id = a.chit_group_id   -- ✅ FIX HERE
LEFT JOIN members wm 
    ON wm.member_id = a.winner_member_id
WHERE 
    a.status = 'completed'
    AND cgm.member_id = ?
ORDER BY a.auction_datetime DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL ERROR: " . $conn->error);
}

$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html>

<head>
    <title>Auction History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Auction History</div>
                    <div class="page-subtitle">
                        View past auction results and your share of discounts
                    </div>
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

                <!-- AUCTION 1 -->


                <!-- AUCTION 2 -->
                <?php if ($result->num_rows == 0): ?>
                <p>No auction history available.</p>
                <?php endif; ?>

                <?php while ($row = $result->fetch_assoc()): 
                    $totalDiscount = $row['pool_amount'] - $row['winning_bid_amount'];
                    $memberShare = round($totalDiscount / $row['total_members']);
                ?>

                <div class="auction-history-card">

                    <div class="auction-header">
                        <div>
                            <div class="auction-title">
                                <?= htmlspecialchars($row['group_name']) ?> - Month <?= $row['auction_month'] ?>
                            </div>
                            <small>AUC<?= str_pad($row['auction_id'], 3, '0', STR_PAD_LEFT) ?></small>
                        </div>
                        <span class="badge completed">Completed</span>
                    </div>

                    <div class="auction-meta">

                        <div class="meta-item">
                            <div class="meta-icon icon-blue">📅</div>
                            <div>
                                Auction Date<br>
                                <b><?= date('d/m/Y', strtotime($row['auction_datetime'])) ?></b><br>
                                <small><?= date('h:i:s a', strtotime($row['auction_datetime'])) ?></small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon icon-purple">🏆</div>
                            <div>
                                Winner<br>
                                <b><?= htmlspecialchars($row['winner_name'] ?? '—') ?></b><br>
                                <small><?= $row['winner_member_id'] ?></small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon icon-green">📉</div>
                            <div>
                                Your Share of Discount<br>
                                <b>₹<?= number_format($memberShare) ?></b><br>
                                <small>
                                    From <?= number_format(($totalDiscount / $row['pool_amount']) * 100, 2) ?>% total
                                    discount
                                </small>
                            </div>
                        </div>

                    </div>

                    <div class="auction-stats">
                        <div>
                            Pool Amount<br>
                            <b>₹<?= number_format($row['pool_amount']) ?></b>
                        </div>
                        <div>
                            Winning Bid<br>
                            <b>₹<?= number_format($row['winning_bid_amount']) ?></b>
                        </div>
                        <div>
                            Total Discount<br>
                            <b style="color:#16a34a;">₹<?= number_format($totalDiscount) ?></b>
                        </div>
                    </div>

                    <div class="auction-note">
                        <b>How it works:</b>
                        The discount of ₹<?= number_format($totalDiscount) ?> is distributed equally among all members.
                        Your monthly contribution was reduced by ₹<?= number_format($memberShare) ?> this month.
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
