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

    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .action-buttons {
    display: flex;
    gap: 8px;
}

.btn {
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #fff;
    transition: 0.2s ease;
}

.btn-view {
    background-color: #3498db;
}

.btn-view:hover {
    background-color: #2980b9;
}

.btn-edit {
    background-color: #f39c12;
}

.btn-edit:hover {
    background-color: #d68910;
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
    <a href="view.php?id=<?= $m['member_id'] ?>" class="btn btn-view">👁 View</a>
    <a href="edit.php?id=<?= $m['member_id'] ?>" class="btn btn-edit">✏ Edit</a>
</td>

                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                        <!-- <tbody>
                            <tr>
                                <td>M001</td>
                                <td>John Doe</td>
                                <td>+91 98765 43210</td>
                                <td>john.doe@email.com</td>
                                <td>2</td>
                                <td>₹2,40,000</td>
                                <td><span class="badge active">Active</span></td>
                                <td><a href="view.php?id=M001">👁 View</a></td>
                            </tr>
                        </tbody> -->
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
