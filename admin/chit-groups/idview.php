<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

if (!isset($_GET['id'])) {
    die('Group ID missing');
}

$groupId = (int) ($_GET['id'] ?? 0);

if (!$groupId) {
    die('Group ID missing');
}

$stmt = $conn->prepare('SELECT * FROM chit_groups WHERE id=?');
$stmt->bind_param('i', $groupId);
$stmt->execute();
$g = $stmt->get_result()->fetch_assoc();

if (!$g) {
    die('Group not found');
}

/* 🔹 Fetch assigned members */
/* Fetch assigned members */
$membersStmt = $conn->prepare("
    SELECT 
        m.member_id,
        m.full_name,
        m.mobile,
        cgm.joined_at
    FROM chit_group_members cgm
    JOIN members m ON m.member_id = cgm.member_id
    WHERE cgm.group_id = ?
");
$membersStmt->bind_param('i', $groupId);
$membersStmt->execute();
$members = $membersStmt->get_result();

/* COUNTS */
$assignedCount = $members->num_rows;
$maxMembers = (int) $g['total_members'];

?>
<!DOCTYPE html>
<html>

<head>
    <title>View Chit Group</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            color: #fff;
            font-size: 12px
        }

        .active {
            background: #16a34a
        }

        .upcoming {
            background: #2563eb
        }

        .completed {
            background: #6b7280
        }

        .search-box {
            margin-bottom: 10px
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">View Chit Group</div>
                    <div class="page-subtitle">Group details & assigned members</div>
                </div>
            </div>

            <div class="content">

                <!-- 🔹 GROUP DETAILS -->
                <div class="form-box">
                    <h3><?= htmlspecialchars($g['group_name']) ?></h3>

                    <p><b>Group Code:</b> <?= $g['group_code'] ?></p>
                    <p><b>Auction Type:</b> <?= $g['auction_type'] ?></p>
                    <p><b>Monthly Contribution:</b> ₹<?= number_format($g['monthly_contribution']) ?></p>
                    <p><b>Duration:</b> <?= $g['duration_months'] ?> months</p>
                    <p><b>Status:</b>
                        <span class="badge <?= $g['status'] ?>">
                            <?= ucfirst($g['status']) ?>
                        </span>
                    </p>

                    <p><b>Members:</b> <?= $assignedCount    ?> / <?= $maxMembers ?></p>
                </div>

                <!-- 🔹 MEMBERS LIST -->

                <div class="table-box" style="margin-top:30px;">
                    <h3>
                        Assigned Members (<?= $assignedCount ?> / <?= $maxMembers ?>)
                    </h3>

                    <?php if ($assignedCount === 0): ?>
                    <p style="color:#888;">No members assigned to this group.</p>
                    <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Joined On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($m = $members->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($m['member_id']) ?></td>
                                <td><?= htmlspecialchars($m['full_name']) ?></td>
                                <td><?= htmlspecialchars($m['mobile']) ?></td>
                                <td><?= date('d M Y', strtotime($m['joined_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <a href="index.php" class="btn-secondary">← Back</a>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('search').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('#memberTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ?
                    '' :
                    'none';
            });
        });
    </script>

</body>
</html>
