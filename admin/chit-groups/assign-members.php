<?php
// session_start();
// include '../../config/database.php';

// if ($_SESSION['role'] !== 'admin') {
//     header("Location: ../../index.php");
//     exit;
// }

// $groupId = $_GET['group_id'] ?? null;
// if (!$groupId) {
//     die("Group ID missing");
// }

// $groupId = (int)$groupId;

// /* Fetch group */
// $gstmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
// $gstmt->bind_param("i", $groupId);
// $gstmt->execute();
// $group = $gstmt->get_result()->fetch_assoc();

// if (!$group) {
//     die("Group not found");
// }

// /* Fetch members NOT already assigned */
// $members = $conn->query("
//     SELECT m.member_id, m.full_name
//     FROM members m
//     WHERE m.is_active=1
//     AND m.member_id NOT IN (
//         SELECT member_id FROM chit_group_members WHERE group_id=$groupId
//     )
// ");
?>
<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$groupId = $_GET['group_id'] ?? null;
if (!$groupId) die("Group ID missing");

$groupId = (int)$groupId;

/* Fetch group */
$gstmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
$gstmt->bind_param("i", $groupId);
$gstmt->execute();
$group = $gstmt->get_result()->fetch_assoc();
if (!$group) die("Group not found");

/* Assigned members */
$assigned = $conn->query("
    SELECT m.member_id, m.full_name
    FROM chit_group_members cgm
    JOIN members m ON m.member_id = cgm.member_id
    WHERE cgm.group_id = $groupId
");

/* Count */
$assignedCount = $assigned->num_rows;
$maxMembers = $group['total_members'];
$remaining = $maxMembers - $assignedCount;
$percent = round(($assignedCount / $maxMembers) * 100);

/* Available members */
$available = $conn->query("
    SELECT m.member_id, m.full_name
    FROM members m
    WHERE m.is_active=1
    AND m.member_id NOT IN (
        SELECT member_id FROM chit_group_members WHERE group_id=$groupId
    )
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>Assign Members to: <?= $group['group_name'] ?></h3>

<form id="assignForm">

<input type="hidden" name="group_id" value="<?= $groupId ?>">

<div class="member-list">
<?php while($m = $members->fetch_assoc()): ?>
    <label class="member-row">
        <input type="checkbox" name="members[]" value="<?= $m['member_id'] ?>">
        <?= $m['member_id'] ?> - <?= $m['full_name'] ?>
    </label>
<?php endwhile; ?>
</div>

<br>
<button class="btn-primary">Assign Selected Members</button>

</form>
<script>
document.getElementById('assignForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);

    fetch('assign-store.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(result => {
        if(result === 'success'){
            alert('Members assigned successfully');
            window.location.reload();
        } else {
            alert(result);
        }
    });
});
</script>

</body>
</html>