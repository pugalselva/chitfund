<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$userId = $_SESSION['user_id'];

/* Fetch member details */
$stmt = $conn->prepare("
    SELECT *
    FROM members
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) {
    die('Member record not found');
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-msg {
            margin-top: 10px;
            font-size: 13px;
        }
    </style>
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

                    <form id="profileForm">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($member['full_name']) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="mobile" value="<?= htmlspecialchars($member['mobile']) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($member['address']) ?>">
                        </div>

                        <button type="submit" class="btn-dark">Save Changes</button>
                        <div class="form-msg" id="profileMsg"></div>
                    </form>
                </div>


                <!-- CHANGE PASSWORD -->
                <div class="profile-card">
                    <h4>Change Password</h4>

                    <form id="passwordForm">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn-dark">Update Password</button>
                        <div class="form-msg" id="passwordMsg"></div>
                    </form>
                </div>


            </div>
        </div>
    </div>
    <!-- script -->
    <script>
        /* =====================
       UPDATE PROFILE
    ===================== */
        document.getElementById('profileForm').addEventListener('submit', e => {
            e.preventDefault();

            fetch('ajax/update-profile.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('profileMsg');
                    msg.innerText = data.message;
                    msg.style.color = data.status ? 'green' : 'red';
                });
        });

        /* =====================
           CHANGE PASSWORD
        ===================== */
        document.getElementById('passwordForm').addEventListener('submit', e => {
            e.preventDefault();

            fetch('ajax/change-password.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('passwordMsg');
                    msg.innerText = data.message;
                    msg.style.color = data.status ? 'green' : 'red';

                    if (data.status) e.target.reset();
                });
        });
    </script>

</body>

</html>