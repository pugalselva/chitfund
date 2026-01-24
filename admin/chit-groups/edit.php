<?php
session_name('chitfund_admin');
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Edit Group</h4>
                        <small class="text-secondary">Update group details and assign members</small>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="row g-4">
                    <!-- LEFT : GROUP EDIT -->
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold text-primary">
                                    <i class="fas fa-edit me-2"></i>Edit Details
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="post" action="update.php">
                                    <input type="hidden" name="id" value="<?= $g['id'] ?>">

                                    <div class="form-floating mb-3">
                                        <input type="text" name="group_name" id="group_name" class="form-control"
                                            value="<?= htmlspecialchars($g['group_name']) ?>" placeholder="Group Name"
                                            required>
                                        <label for="group_name">Group Name</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="status" id="status" class="form-select">
                                            <option value="upcoming" <?= $g['status'] == 'upcoming' ? 'selected' : '' ?>>
                                                Upcoming</option>
                                            <option value="active" <?= $g['status'] == 'active' ? 'selected' : '' ?>>Active
                                            </option>
                                            <option value="completed" <?= $g['status'] == 'completed' ? 'selected' : '' ?>>
                                                Completed</option>
                                        </select>
                                        <label for="status">Group Status</label>
                                    </div>

                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_active"
                                            id="is_active" <?= $g['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">Active Group</label>
                                    </div>

                                    <button class="btn btn-primary w-100 text-uppercase fw-bold"
                                        style="letter-spacing: 0.5px;">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT : ADD MEMBERS -->
                    <div class="col-12 col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div
                                class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                                <h5 class="card-title fw-bold text-success mb-0">
                                    <i class="fas fa-user-plus me-2"></i>Add Members
                                </h5>
                            </div>
                            <div class="card-body p-4">

                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="search" class="form-control border-start-0 ps-0"
                                        placeholder="Search available members..." onkeyup="filterMembers()">
                                </div>

                                <form id="addMembersForm">
                                    <input type="hidden" name="group_id" value="<?= $groupId ?>">

                                    <div class="table-responsive border rounded"
                                        style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="memberTable">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="40" class="text-center">
                                                        <!-- <input type="checkbox" class="form-check-input" id="selectAll"> -->
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($m = $members->fetch_assoc()): ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input" name="members[]"
                                                                value="<?= $m['member_id'] ?>">
                                                        </td>
                                                        <td><small class="text-muted">#<?= $m['member_id'] ?></small></td>
                                                        <td class="fw-medium"><?= htmlspecialchars($m['full_name']) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="submit" class="btn btn-success mt-3 w-100 fw-bold">
                                        <i class="fas fa-plus-circle me-1"></i> Add Selected Members
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
    <script>
        function filterMembers() {
            let q = document.getElementById('search').value.toLowerCase();
            document.querySelectorAll('#memberTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        document.getElementById('addMembersForm').addEventListener('submit', e => {
            e.preventDefault();

            // Check if any member is selected
            const checked = document.querySelectorAll('input[name="members[]"]:checked');
            if (checked.length === 0) {
                alert('Please select at least one member to add.');
                return;
            }

            fetch('add-members.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
                .then(r => r.text())
                .then(res => {
                    if (res.trim() === 'success') {
                        alert('Members added successfully');
                        location.reload();
                    } else {
                        alert(res);
                    }
                });
        });
    </script>

</body>

</html>