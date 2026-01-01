<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

/* Fetch active chit groups */
$groups = $conn->query("
    SELECT id, group_name
    FROM chit_groups
    WHERE is_active=1
    ORDER BY group_name
");
?>


<!DOCTYPE html>
<html>

<head>
    <title>Create Auction</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Create Auction</div>
                    <div class="page-subtitle">Schedule a new auction for chit group</div>
                </div>
            </div>

            <div class="content">

                <div class="form-box" style="max-width:600px;">
                    <h4>Create Auction</h4>

                    <form method="post" action="store.php">

                        <!-- ✅ CHIT GROUP DROPDOWN -->
                        <div class="form-group">
                            <label>Chit Group *</label>
                            <select class="form-control" name="chit_group_id" required>
                                <option value="">Select chit group</option>
                                <?php while ($g = $groups->fetch_assoc()): ?>
                                <option value="<?= $g['id'] ?>">
                                    <?= htmlspecialchars($g['group_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- AUCTION DATE -->
                        <div class="form-group">
                            <label>Auction Date & Time *</label>
                            <input type="datetime-local" name="auction_datetime" class="form-control" required>
                        </div>

                        <!-- STARTING BID -->
                        <div class="form-group">
                            <label>Starting Bid Amount *</label>
                            <input type="number" name="starting_bid_amount" class="form-control" required>
                        </div>

                        <!-- STATUS -->
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" class="form-control">
                                <option value="upcoming">Upcoming</option>
                                <option value="active">Active</option>
                            </select>
                        </div>

                        <button class="btn-primary">Create Auction</button>
                        <a href="index.php" class="btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
