<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../index.php');
    exit();
}

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div class="main">
            <div class="topbar">
               <div>
                    <div class="page-title">Profile</div>
                    <div class="page-subtitle">Manage your account information</div>
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

                <!-- PERSONAL INFO -->
                <div class="profile-card">
                    <h4>Personal Information</h4>

                    <form method="post">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="Member User">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="member@chitfund.com">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" value="+1 234 567 8900">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" value="123 Main St, City, State 12345">
                        </div>

                        <button class="btn-dark">Save Changes</button>
                    </form>
                </div>

                <!-- CHANGE PASSWORD -->
                <div class="profile-card">
                    <h4>Change Password</h4>

                    <form method="post">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password">
                        </div>

                        <button class="btn-dark">Update Password</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>

</html>