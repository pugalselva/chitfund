<?php
// session_start();
// include '../../config/database.php';

// if ($_SESSION['role'] !== 'admin') {
//     die('Unauthorized');
// }

// if (!isset($_GET['id'])) {
//     die('Group ID missing');
// }

// $id = (int)$_GET['id'];

// $stmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
// $stmt->bind_param("i", $id);
// $stmt->execute();
// $g = $stmt->get_result()->fetch_assoc();

// if (!$g) {
//     die("Group not found");
// }
// /* 🔒 Lock completed group */
// if ($g['status'] === 'completed') {
//     die("Completed groups cannot be edited");
// }
?>
<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groupId = (int) ($_GET['id'] ?? 0);
if (!$groupId) {
    die('Group ID missing');
}

/* Group */
$g = $conn->query("SELECT * FROM chit_groups WHERE id=$groupId")->fetch_assoc();
if (!$g) {
    die('Group not found');
}

/* Available members (NOT in group) */
$members = $conn->query("
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

            <div class="content" style="display:flex; gap:30px">

                <!-- LEFT : GROUP EDIT -->
                <div class="form-box" style="width:40%">
                    <h3>Edit Group: <?= htmlspecialchars($g['group_name']) ?></h3>

                    <form method="post" action="update.php">
                        <input type="hidden" name="id" value="<?= $g['id'] ?>">

                        <label>Group Name</label>
                        <input name="group_name" value="<?= $g['group_name'] ?>" required>

                        <label>Status</label>
                        <select name="status">
                            <option value="upcoming" <?= $g['status'] == 'upcoming' ? 'selected' : '' ?>>Upcoming
                            </option>
                            <option value="active" <?= $g['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="completed" <?= $g['status'] == 'completed' ? 'selected' : '' ?>>Completed
                            </option>
                        </select>

                        <label>
                            <input type="checkbox" name="is_active" <?= $g['is_active'] ? 'checked' : '' ?>>
                            Active
                        </label>

                        <button class="btn-primary">Save</button>
                    </form>
                </div>

                <!-- RIGHT : ADD MEMBERS -->
                <div class="form-box" style="width:60%">
                    <h3>Add Members to Group</h3>

                    <!-- SEARCH -->
                    <input type="text" id="search" placeholder="Search by ID or Name" onkeyup="filterMembers()">

                    <form id="addMembersForm">
                        <input type="hidden" name="group_id" value="<?= $groupId ?>">

                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Member ID</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody id="memberTable">
                                <?php while ($m = $members->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="members[]" value="<?= $m['member_id'] ?>">
                                    </td>
                                    <td><?= $m['member_id'] ?></td>
                                    <td><?= htmlspecialchars($m['full_name']) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <br>
                        <button type="submit" class="btn-primary">➕ Add Selected</button>
                    </form>
                </div>

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
        <option value="upcoming" <?= $g['status'] == 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
        <option value="active" <?= $g['status'] == 'active' ? 'selected' : '' ?>>Active</option>
        <option value="completed" <?= $g['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <label>
        <input type="checkbox" name="is_active" value="1" <?= $g['is_active'] ? 'checked' : '' ?>>
        Active
    </label>

    <br><br>
    <button type="submit">Update Group</button>
</form> -->
<script>
function filterMembers() {
    let q = document.getElementById('search').value.toLowerCase();
    document.querySelectorAll('#memberTable tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

document.getElementById('addMembersForm').addEventListener('submit', e => {
    e.preventDefault();

    fetch('add-members.php', {
        method: 'POST',
        body: new FormData(e.target)
    })
    .then(r => r.text())
    .then(res => {
        if (res === 'success') {
            alert('Members added');
            location.reload();
        } else {
            alert(res);
        }
    });
});
</script>

</body>

</html>
