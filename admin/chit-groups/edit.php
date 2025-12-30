<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

if (!isset($_GET['id'])) {
    die('Group ID missing');
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$g = $stmt->get_result()->fetch_assoc();

if (!$g) {
    die("Group not found");
}
/* 🔒 Lock completed group */
if ($g['status'] === 'completed') {
    die("Completed groups cannot be edited");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Chit Group</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    
</head>
<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Edit Member</div>
                    <div class="page-subtitle">Update member details</div>
                </div>
            </div>

            <div class="content">

                <!-- STEPPER -->
                <div class="step-tabs">

                    
                </div>

                <form method="post" action="update.php" enctype="multipart/form-data">

                    <input type="hidden" name="id" value="<?= $g['id'] ?>">

                    <!-- ================= PERSONAL ================= -->
                    <div id="personal" class="step-content active form-box">
                        <h3>Edit Group: <?= htmlspecialchars($g['group_name']) ?></h3><br>

                        <div class="form-group">
                            <label>Group Name</label>
                                <input name="group_name" value="<?= $g['group_name'] ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Status *</label>
                                <select class="form-control" name="status" required>
                                     <option value="upcoming" <?= $g['status']=='upcoming'?'selected':'' ?>>Upcoming</option>
        <option value="active" <?= $g['status']=='active'?'selected':'' ?>>Active</option>
        <option value="completed" <?= $g['status']=='completed'?'selected':'' ?>>Completed</option>
    </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Active Status</label><br><br>
                            <label class="switch">
                                <input type="checkbox" name="is_active" value="1"
                                    <?= $g['is_active'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <br>
                        <button type="submit" class="btn-primary">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- <h3>Edit Group: <?= htmlspecialchars($g['group_name']) ?></h3>

<form method="post" action="update.php">

    <input type="hidden" name="id" value="<?= $g['id'] ?>">

    <label>Group Name</label>
    <input name="group_name" value="<?= $g['group_name'] ?>" required>

    <label>Status</label>
    <select name="status">
        <option value="upcoming" <?= $g['status']=='upcoming'?'selected':'' ?>>Upcoming</option>
        <option value="active" <?= $g['status']=='active'?'selected':'' ?>>Active</option>
        <option value="completed" <?= $g['status']=='completed'?'selected':'' ?>>Completed</option>
    </select>

    <label>
        <input type="checkbox" name="is_active" value="1" <?= $g['is_active'] ? 'checked' : '' ?>>
        Active
    </label>

    <br><br>
    <button type="submit">Update Group</button>
</form> -->

</body>
</html>
