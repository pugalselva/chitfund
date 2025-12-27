<?php
session_start();
include '../../config/database.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$g = $stmt->get_result()->fetch_assoc();
// update.php will handle the update logic
session_start();
include '../../config/database.php';

$id = $_POST['id'];
$status = $_POST['status'];
$isActive = isset($_POST['is_active']) ? 1 : 0;

$stmt = $conn->prepare("
    UPDATE chit_groups 
    SET status=?, is_active=?
    WHERE id=?
");
$stmt->bind_param("sii", $status, $isActive, $id);
$stmt->execute();

header("Location: view.php?id=$id");
exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">

<input type="hidden" name="id" value="<?= $g['id'] ?>">

<label>Group Name</label>
<input name="group_name" value="<?= $g['group_name'] ?>">

<label>Status</label>
<select name="status">
    <option value="upcoming" <?= $g['status']=='upcoming'?'selected':'' ?>>Upcoming</option>
    <option value="active" <?= $g['status']=='active'?'selected':'' ?>>Active</option>
    <option value="completed" <?= $g['status']=='completed'?'selected':'' ?>>Completed</option>
</select>

<label>Active</label>
<input type="checkbox" name="is_active" value="1" <?= $g['is_active']?'checked':'' ?>>

<button>Update</button>
</form>

</body>
</html>