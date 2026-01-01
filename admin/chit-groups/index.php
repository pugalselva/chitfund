<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$result = $conn->query("
    SELECT 
        g.*,
        COUNT(a.id) AS completed_months
    FROM chit_groups g
    LEFT JOIN auctions a 
        ON a.chit_group_id = g.id 
        AND a.status = 'completed'
    WHERE g.is_active = 1
    GROUP BY g.id
    ORDER BY g.created_at DESC
");

$count = $result->num_rows;
?>


<!DOCTYPE html>
<html>

<head>
    <title>Chit Groups</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s ease;
        }

        /* Edit */
        .btn-edit {
            background-color: #f39c12;
        }

        .btn-edit:hover {
            background-color: #d68910;
        }

        /* View */
        .btn-view {
            background-color: #3498db;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }

        /* Auctions */
        .btn-auction {
            background-color: #27ae60;
        }

        .btn-auction:hover {
            background-color: #1e8449;
        }

        .btn-danger {
            background-color: #e74c3c;
        }


        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
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
                <!-- <a href="../auctions/index.php?group_id=<?= $g['id'] ?>">
                    Auctions
                </a> -->

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
                                <th>Current Month Progress</th>
                                <th>Auction Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($g = $result->fetch_assoc()): ?>

                            <?php
                            /* ---------- AUTO COMPLETE GROUP ---------- */
                            if ($g['completed_months'] >= $g['duration_months'] && $g['status'] !== 'completed') {
                                $update = $conn->prepare("
                                    UPDATE chit_groups
                                    SET status='completed'
                                    WHERE id=?
                                ");
                                $update->bind_param('i', $g['id']);
                                $update->execute();
                            
                                // Update local variable so UI shows correct status
                                $g['status'] = 'completed';
                            }
                            
                            /* ---------- PROGRESS ---------- */
                            $percent = $g['duration_months'] > 0 ? round(($g['completed_months'] / $g['duration_months']) * 100) : 0;
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
                                <td class="action-buttons">
                                    <!-- <a href="edit.php?id=<?= $g['id'] ?>" >
                                        <i class="fa fa-pen"></i> Edit
                                    </a> -->
                                    <!-- <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-edit">  <i class="fa fa-pen"></i>Edit</a> -->
                                    <?php if ($g['status'] !== 'completed'): ?>
                                    <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger"
                                        onclick="confirmDelete(<?= (int) $g['id'] ?>)">
                                        <i class="fa fa-trash"></i>
                                    </a>

                                    <?php endif; ?>

                                    <a href="view.php?id=<?= $g['id'] ?>" class="btn btn-view">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <!-- <a href="../auctions/index.php?group_id=<?= $g['id'] ?>" class="btn btn-auction">
                                        <i class="fa fa-gavel"></i> Auctions
                                    </a> -->
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(groupId) {
            if (!groupId) {
                alert('Invalid group ID');
                return;
            }

            if (!confirm("Are you sure you want to delete this group?")) {
                return;
            }

            fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        id: groupId
                    })
                })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
                        alert('Group deleted successfully');
                        location.reload();
                    } else {
                        alert(result);
                    }
                });
        }
    </script>


</body>

</html>
