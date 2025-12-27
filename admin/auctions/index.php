<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groupId = (int) ($_GET['group_id'] ?? 0);
if (!$groupId) {
    die('Group ID missing');
}

$auctions = $conn->query("
    SELECT * FROM auctions
    WHERE chit_group_id = $groupId
    ORDER BY auction_month ASC
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

                <a href="create.php?group_id=<?= $groupId ?>">+ Create Auction</a>

                <div class="table-box">
                    <b>Scheduled Auctions (1)</b><br><br>

                    <h2>Auctions</h2>
                    <table border="1">
                        <tr>
                            <th>Month</th>
                            <th>Date</th>
                            <th>Starting Bid</th>
                            <th>Status</th>
                        </tr>

                        <?php while($a = $auctions->fetch_assoc()): ?>
                        <tr>
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
