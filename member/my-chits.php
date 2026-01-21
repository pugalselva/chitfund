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

/* Fetch member chit groups with completed months */
// $stmt = $conn->prepare("
// SELECT 
//     g.*,
//     COUNT(a.id) AS completed_months
// FROM chit_groups g
// JOIN chit_group_members gm ON gm.group_id = g.id
// LEFT JOIN auctions a 
//     ON a.chit_group_id = g.id 
//     AND a.status = 'completed'
// WHERE gm.member_id = ?
// GROUP BY g.id
// ORDER BY g.created_at DESC
// ");
$stmt = $conn->prepare("
SELECT 
    g.*,
    COUNT(a.id) AS completed_months,

    /* Last completed auction pool amount */
    (
        SELECT a2.starting_bid_amount
        FROM auctions a2
        WHERE a2.chit_group_id = g.id
          AND a2.status = 'completed'
        ORDER BY a2.auction_month DESC
        LIMIT 1
    ) AS last_pool_amount,

    /* Last winning bid */
    (
        SELECT a3.winning_bid_amount
        FROM auctions a3
        WHERE a3.chit_group_id = g.id
          AND a3.status = 'completed'
        ORDER BY a3.auction_month DESC
        LIMIT 1
    ) AS last_winning_amount

FROM chit_groups g
JOIN chit_group_members gm ON gm.group_id = g.id
LEFT JOIN auctions a 
    ON a.chit_group_id = g.id 
   AND a.status = 'completed'
WHERE gm.member_id = ?
GROUP BY g.id
ORDER BY g.created_at DESC
");


$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();

// $members = (int)$g['total_members'];
// $monthlyContribution = 0;

// if ($poolAmount > 0 && $members > 0) {
//     $monthlyContribution = round($poolAmount / $members);
// }

?>
<!DOCTYPE html>
<html>

<head>
    <title>My Chits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main">

            
            <div class="topbar">
               <div>
                    <div class="page-title">My Chit Groups</div>
                    <div class="page-subtitle">View and manage your chit memberships</div>
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

                <div class="chit-grid">

                    <?php if ($result->num_rows === 0): ?>
                    <p>No chit groups assigned to you.</p>
                    <?php endif; ?>

                    <?php while ($g = $result->fetch_assoc()): 
                        $completed = (int)$g['completed_months'];
                        $duration  = (int)$g['duration_months'];
                        $members   = (int)$g['total_members'];

                        $winningAmount = (float)$g['last_winning_amount'];

                        /* Monthly Contribution */
                        $monthlyContribution = 0;

                        if ($members > 0 && $winningAmount > 0) {
                            $monthlyContribution = round($winningAmount / $members);
                        }
                    ?>

                    <div class="chit-card">

                        <!-- HEADER -->
                        <div class="chit-header">
                            <div>
                                <div class="chit-title"><?= htmlspecialchars($g['group_name']) ?></div>
                                <small><?= htmlspecialchars($g['group_code']) ?></small>
                            </div>
                            <span class="badge <?= $g['status'] ?>">
                                <?= ucfirst($g['status']) ?>
                            </span>
                        </div>

                        <!-- STATS -->
                        <div class="chit-stats">

                            <div class="stat">
                                <div class="icon icon-blue">₹</div>
                                <div>
                                    Pool Amount<br>
                                    <?php
                                    $poolAmount = (float)($g['last_pool_amount'] ?? 0);
                                    ?>

                                    <b>
                                        <?= $poolAmount > 0 
                                            ? '₹' . number_format($poolAmount)
                                            : '—' ?>
                                    </b>

                                </div>
                            </div>

                            <div class="stat">
                                <div class="icon icon-green">👥</div>
                                <div>
                                    Total Members<br>
                                    <b><?= $g['total_members'] ?></b>
                                </div>
                            </div>

                            <div class="stat">
                                <div class="icon icon-orange">📅</div>
                                <div>
                                    Duration<br>
                                    <b><?= $duration ?> months</b>
                                </div>
                            </div>

                            <div class="stat">
                                <div class="icon icon-green">₹</div>
                                <div>
                                    Monthly Contribution<br>
                                    <b>
                                        <?= $monthlyContribution > 0 
                                            ? '₹' . number_format($monthlyContribution) 
                                            : '—' ?>
                                    </b>
                                </div>
                            </div>


                        </div>

                        <!-- PROGRESS -->
                        <small>Progress</small>
                        <div class="progress-bar">
                            <div class="progress" style="width:<?= $percent ?>%"></div>
                        </div>
                        <small><?= $completed ?> / <?= $duration ?> months</small>
                        <?php if ($monthlyContribution > 0): ?>
                        <small style="color:#6b7280">
                            Based on last winning bid ₹<?= number_format($winningAmount) ?> 
                            shared among <?= $members ?> members
                        </small>
                        <?php endif; ?>

                        <!-- FOOTER -->
                        <div class="chit-footer">
                            <div>
                                Completed Months<br>
                                <b><?= $completed ?></b>
                            </div>
                            <!-- <div>
                                Remaining Months<br>
                                <b><?= $remaining ?></b>
                            </div> -->
                            <div>
                                Auction Type<br>
                                <b><?= $g['auction_type'] ?></b>
                            </div>
                            <div>
                                Foreman Commission<br>
                                <b><?= $g['commission'] ?>%</b>
                            </div>
                        </div>

                        <!-- TOTAL -->
                        <div class="chit-total">
                            Total Chit Value<br>
                            <b>₹<?= number_format($g['total_value']) ?></b>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>