<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$settings = $conn->query("SELECT * FROM settings ORDER BY id ASC");
?>


<!DOCTYPE html>
<html>
<head>
<title>Settings</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Settings</div>
        <div class="page-subtitle">Configure system settings and preferences</div>
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

<div class="settings-box">
    <h4>System Configuration</h4><br>

    <table class="settings-table">
        <thead>
             <tr>
            <th>Setting</th>
            <th>Value</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
            <?php while ($s = $settings->fetch_assoc()): ?>
        <tr>
            <form method="post" action="update.php">
                <td><b><?= $s['setting_key'] ?></b></td>

                <td>
                    <input type="text"
                           name="setting_value"
                           class="form-control"
                           value="<?= $s['setting_value'] ?>">
                </td>

                <td><?= $s['description'] ?></td>

                <td>
                    <label class="switch">
                        <input type="checkbox" name="is_active"
                               <?= $s['is_active'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>

                <td>
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button class="btn-primary">Save</button>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
</div>
</div>

<script>
function saveSetting(key){
    alert("Setting saved: " + key);
    // Later:
    // fetch('save_settings.php', {method:'POST', body:...})
}
</script>

</body>
</html>
