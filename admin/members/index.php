<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$result = $conn->query('SELECT * FROM members ORDER BY created_at DESC');
$count = $result->num_rows;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Members</title>
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
                    <div class="page-title">Members</div>
                    <div class="page-subtitle">View all registered members</div>
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

                <a href="create.php"><button class="btn-primary">＋ Enroll Member</button></a>
                <div class="table-box">
                    <h3>All Members (<?= $count ?>)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Member ID</th>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Joining Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($m = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['member_id']) ?></td>

                                <td>
                                    <strong><?= htmlspecialchars($m['full_name']) ?></strong><br>
                                    <small>DOB: <?= htmlspecialchars($m['dob']) ?></small>
                                </td>

                                <td><?= htmlspecialchars($m['gender']) ?></td>
                                <td><?= htmlspecialchars($m['mobile']) ?></td>
                                <td><?= htmlspecialchars($m['email']) ?></td>
                                <td><?= date('d M Y', strtotime($m['joining_date'])) ?></td>

                                <td>
                                    <span class="badge <?= $m['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>

                                <td class="action-buttons">
                                    <a href="view.php?id=<?= $m['member_id'] ?>" class="btn btn-view"><i
                                            class="fa fa-eye"></i> </a>
                                    <a href="edit.php?id=<?= $m['member_id'] ?>" class="btn btn-edit"> <i
                                            class="fa fa-pen"></i> </a>
                                    <a href="javascript:void(0)" class="btn btn-danger"
                                        onclick="deleteMember('<?= $m['member_id'] ?>')">
                                        <i class="fa fa-trash"></i>
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
    <!-- script -->
    <script>
        function deleteMember(memberId) {
            if (!memberId) {
                alert('Invalid Member ID');
                return;
            }

            if (!confirm('Are you sure you want to delete this member?')) {
                return;
            }

            fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        member_id: memberId
                    })
                })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
                        alert('Member deleted successfully');
                        location.reload();
                    } else {
                        alert(result);
                    }
                });
        }
    </script>

</body>

</html>
