<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$auctions = $conn->query("
    SELECT a.*, g.group_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    ORDER BY a.created_at DESC
");
?>



<!DOCTYPE html>
<html>

<head>
    <title>Upcoming Auctions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Upcoming Auctions</div>
                    <div class="page-subtitle">Scheduled auctions for chit groups</div>
                </div>
                <div class="topbar">
                    <div>
                        <b>Admin User</b><br>
                        sandy@gmail.com
                        <a href="../../logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content">

                <a href="create.php" class="btn-primary">＋ Create Auction</a>

                <div class="table-box">
                    <table>
                        <tr>
                            <th>Group</th>
                            <th>Month</th>
                            <th>Date & Time</th>
                            <th>Starting Bid</th>
                            <th>Status</th>
                        </tr>

                        <?php while ($a = $auctions->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['group_name']) ?></td>
                            <td><?= $a['auction_month'] ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($a['auction_datetime'])) ?></td>
                            <td>₹<?= number_format($a['starting_bid_amount']) ?></td>
                            <td><?= ucfirst($a['status']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
