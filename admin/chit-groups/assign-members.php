<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groupId = $_GET['group_id'] ?? null;
if (!$groupId) die('Group ID missing');
$groupId = (int)$groupId;

/* Fetch group */
$gstmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
$gstmt->bind_param("i", $groupId);
$gstmt->execute();
$group = $gstmt->get_result()->fetch_assoc();
if (!$group) die('Group not found');

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
$percent = $maxMembers > 0 ? round(($assignedCount / $maxMembers) * 100) : 0;

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
<html>
<head>
<title>Assign Members</title>
</head>

<body>

<h2>Assign Members to: <?= htmlspecialchars($group['group_name']) ?></h2>

<p><b>Members:</b> <?= $assignedCount ?> / <?= $maxMembers ?></p>
<div style="background:#ddd;height:8px;width:300px;">
    <div style="background:#000;height:8px;width:<?= $percent ?>%"></div>
</div>
<p><?= $remaining ?> slots remaining</p>

<hr>

<h3>Assigned Members</h3>
<table border="1" cellpadding="6">
<tr>
    <th>Member ID</th>
    <th>Name</th>
    <th>Action</th>
</tr>

<?php while($a = $assigned->fetch_assoc()): ?>
<tr>
    <td><?= $a['member_id'] ?></td>
    <td><?= $a['full_name'] ?></td>
    <td>
        <a href="remove-member.php?group_id=<?= $groupId ?>&member_id=<?= $a['member_id'] ?>"
           onclick="return confirm('Remove this member?')">
           Remove
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<hr>

<h3>Add Members</h3>

<?php if ($remaining <= 0): ?>
    <p style="color:red;">Member limit reached</p>
<?php else: ?>

<form id="assignForm">
<input type="hidden" name="group_id" value="<?= $groupId ?>">

<?php while($m = $available->fetch_assoc()): ?>
<label>
    <input type="checkbox" name="members[]" value="<?= $m['member_id'] ?>">
    <?= $m['member_id'] ?> - <?= $m['full_name'] ?>
</label><br>
<?php endwhile; ?>

<br>
<button type="submit">Assign Selected</button>
</form>

<?php endif; ?>

<script>
document.getElementById('assignForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    fetch('assign-store.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(res => res.text())
    .then(result => {
        if (result === 'success') {
            location.reload();
        } else {
            alert(result);
        }
    });
});
</script>

</body>
</html>
