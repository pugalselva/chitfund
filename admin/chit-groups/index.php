<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$result = $conn->query('SELECT * FROM chit_groups ORDER BY created_at DESC');
$count = $result->num_rows;
?>


<!DOCTYPE html>
<html>

<head>
    <title>Chit Groups</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">

        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Chit Groups</div>
                    <div class="page-subtitle">Manage all chit fund groups</div>
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
                <!-- REDIRECT BUTTON -->
                <a href="create.php">
                    <button class="btn-primary">＋ Create Group</button>
                </a>
<a href="../auctions/index.php?group_id=<?= $g['id'] ?>">
    Auctions
</a>

                <div class="table-box">
                    <h3>All Groups (<?= $count ?>)</h3>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Group ID</th>
                                <th>Group Name</th>
                                <th>Total Value</th>
                                <th>Members</th>
                                <th>Contribution</th>
                                <th>Duration</th>
                                <th>Progress</th>
                                <th>Auction Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while($g = $result->fetch_assoc()): 
                                $percent = round(($g['completed_months'] / $g['duration_months']) * 100);
                            ?>
                            <tr>
                                <td><?= $g['group_code'] ?></td>
                                <td>
                                    <strong><?= $g['group_name'] ?></strong><br>
                                    <small>Commission: <?= $g['commission'] ?>%</small>
                                </td>
                                <td>₹<?= number_format($g['total_value']) ?></td>
                                <td><?= $g['total_members'] ?></td>
                                <td>₹<?= number_format($g['monthly_contribution']) ?></td>
                                <td><?= $g['duration_months'] ?> months</td>
                                <td>
                                    <?= $g['completed_months'] ?> / <?= $g['duration_months'] ?><br>
                                    <small><?= $percent ?>%</small>
                                </td>
                                <td><?= $g['auction_type'] ?></td>
                                <td>
                                    <span class="badge <?= $g['status'] ?>">
                                        <?= ucfirst($g['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="assign-members.php?group_id=<?= $g['id'] ?>">
                                        Assign Members
                                    </a><br>
                                    <a href="edit.php?id=<?= $g['id'] ?>">Edit</a><br>
                                    <a href="view.php?id=<?= $g['id'] ?>">👁 View</a>
                                    <a href="../auctions/index.php?group_id=<?= $g['id'] ?>">
    Auctions
</a>

                                </td>
                            </tr>
                            <?php endwhile; ?>

                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

</body>

</html>
