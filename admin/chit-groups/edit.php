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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
    /* ===============================
   LAYOUT
================================ */
    .content {
        display: flex;
        gap: 28px;
        margin-top: 20px;
    }

    /* ===== FORM CARD ===== */
    .form-box {
        background: #ffffff;
        border-radius: 14px;
        padding: 28px;
        border: 1px solid #e5e7eb;
    }

    /* ===== HEADING ===== */
    .form-box h3 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
    }

    /* ===== LABELS ===== */
    .form-box label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    /* Highlight group name label */
    .grp_name {
        color: #4f46e5;
    }

    /* ===== INPUTS ===== */
    .form-box input[type="text"],
    .form-box select {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        font-size: 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        margin-bottom: 20px;
        background: #ffffff;
    }

    /* Focus */
    .form-box input:focus,
    .form-box select:focus {
        border-color: #000000ff;
        outline: none;
    }

    /* ===== GROUP NAME SPECIAL ===== */
    .group-name-input {
        width: 100%;
        height: 48px;
        padding: 0 16px;

        font-size: 15px;
        font-weight: 500;
        color: #111827;

        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;

        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    /* Focus state (very clean) */
    .group-name-input:focus {
        outline: none;
        border-color: #111827;
        box-shadow: 0 0 0 2px rgba(17, 24, 39, 0.08);
    }

    /* Placeholder (if any) */
    .group-name-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }


    /* ===== CHECKBOX ALIGN ===== */
    .form-box label:last-of-type {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-weight: 600;
    }

    /* ===== BUTTON ===== */
    .btn-primary {
        width: 100%;
        background: #000000ff;
        color: #ffffff;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #000000ff;
    }


    /* ===============================
   SEARCH
================================ */
    #search {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        margin-bottom: 14px;
    }

    /* ===============================
   TABLE
================================ */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    thead {
        background: #f3f4f6;
    }

    th {
        padding: 10px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    /* ===============================
   BUTTON
================================ */
    .btn-primary {
        background: #000000ff;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #000000ff;
    }

    /* ===============================
   RESPONSIVE
================================ */
    @media (max-width: 900px) {
        .content {
            flex-direction: column;
        }

        .form-box {
            width: 100% !important;
        }
    }
    </style>

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
                <?php include '../layout/header.php'; ?>

            </div>

            <div class="content" style="display:flex; gap:30px">

                <!-- LEFT : GROUP EDIT -->
                <div class="form-box" style="width:40%">
                    <h3>Edit Group: <?= htmlspecialchars($g['group_name']) ?></h3>

                    <form method="post" action="update.php">
                        <input type="hidden" name="id" value="<?= $g['id'] ?>">

                        <label class="grp_name">Group Name</label>
                        <input name="group_name" class="group-name-input" value="<?= $g['group_name'] ?>" required>

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