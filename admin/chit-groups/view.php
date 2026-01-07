<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
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

$id = (int) $_GET['id'];

$stmt = $conn->prepare('SELECT * FROM chit_groups WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();

if (!$group) {
    die('Group not found');
}
$assigned = $conn->query("
    SELECT m.member_id, m.full_name
    FROM chit_group_members cgm
    JOIN members m ON cgm.member_id = m.member_id
    WHERE cgm.group_id = $id
");

$completed = $group['completed_months'];
$total = $group['duration_months'];
$remaining = $total - $completed;
$progress = $total > 0 ? round(($completed / $total) * 100) : 0;
?>


<!DOCTYPE html>
<html>

<head>
    <title>Chit Group Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* ===============================
        PAGE STRUCTURE
        ================================ */
        .wrapper {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
        }

        .main {
            flex: 1;
            padding: 24px 28px;
        }

        /* ===============================
        TOPBAR
        ================================ */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
        }

        .page-subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ===============================
        CONTENT
        ================================ */
        .content {
            max-width: 1100px;
        }

        /* ===============================
        GROUP DETAILS CARD
        ================================ */
        .form-box {
            background: #ffffff;
            border-radius: 14px;
            padding: 26px 28px;
            border: 1px solid #e5e7eb;
        }

        .form-box h3 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 20px;
        }

        /* Group info rows */
        .form-box p {
            font-size: 14px;
            color: #374151;
            margin-bottom: 10px;
            display: flex;
            gap: 8px;
        }

        .form-box p b {
            color: #111827;
            min-width: 170px;
        }

        /* ===============================
        STATUS BADGES
        ================================ */
        .badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        /* Badge colors */
        .badge.upcoming {
            background: #eef2ff;
            color: #4338ca;
        }

        .badge.active {
            background: #ecfdf5;
            color: #047857;
        }

        .badge.completed {
            background: #f3f4f6;
            color: #374151;
        }

        /* ===============================
        MEMBERS TABLE CARD
        ================================ */
        .table-box {
            margin-top: 28px;
            background: #ffffff;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #e5e7eb;
        }

        .table-box h3 {
            font-size: 17px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 14px;
        }

        /* ===============================
        TABLE
        ================================ */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .table thead {
            background: #f9fafb;
        }

        .table th {
            text-align: left;
            font-weight: 600;
            color: #374151;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* ===============================
        BACK BUTTON
        ================================ */
        .btn-secondary {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #4338ca;
            text-decoration: none;
        }

        .btn-secondary:hover {
            text-decoration: underline;
        }

        /* ===============================
        RESPONSIVE
        ================================ */
        @media (max-width: 900px) {
            .main {
                padding: 18px;
            }

            .form-box p {
                flex-direction: column;
                gap: 2px;
            }

            .form-box p b {
                min-width: auto;
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
                    <div class="page-title"><?= $group['group_name'] ?></div>
                    <div class="page-subtitle">Chit group details and information</div>
                </div>
            </div>
            <a href="../auctions/index.php?group_id=<?= $group['id'] ?>" class="btn btn-primary mb-3">
                <i class="fa fa-gavel me-1"></i> View Auctions
            </a>


            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <!-- GROUP INFO -->
                <div class="info-box">
                    <h4>Group Information</h4><br>

                    <div class="info-row">
                        <span>Status</span>
                        <span class="status"><?= ucfirst($group['status']) ?></span>
                    </div>

                    <div class="info-row">
                        <span>Total Members</span>
                        <span><?= $group['total_members'] ?></span>
                    </div>

                    <div class="info-row">
                        <span>Pool Amount</span>
                        <span>₹<?= number_format($group['total_value']) ?></span>
                    </div>

                    <div class="info-row">
                        <span>Duration</span>
                        <span><?= $group['duration_months'] ?> months</span>
                    </div>

                    <div class="info-row">
                        <span>Start Date</span>
                        <span><?= date('d-m-Y', strtotime($group['start_date'])) ?></span>
                    </div>
                </div>

                <!-- PROGRESS -->
                <div class="info-box">
                    <h4>Progress</h4><br>

                    <div style="display:flex;justify-content:space-between;">
                        <span>Current Month</span>
                        <span><?= $completed ?> / <?= $total ?></span>
                    </div>

                    <div class="progress-bar">
                        <div class="progress" style="width:<?= $progress ?>%"></div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
                        <div class="stat-box green">
                            <h2><?= $completed ?></h2>
                            <small>Completed</small>
                        </div>

                        <div class="stat-box blue">
                            <h2><?= $remaining ?></h2>
                            <small>Remaining</small>
                        </div>
                    </div>
                </div>
                <!-- 🔹 GROUP DETAILS -->
                <div class="form-box">
                    <h3><?= htmlspecialchars($g['group_name']) ?></h3>

                    <p><b>Group Code:</b> <?= $g['group_code'] ?></p>
                    <p><b>Auction Type:</b> <?= $g['auction_type'] ?></p>
                    <p><b>Duration:</b> <?= $g['duration_months'] ?> months</p>
                    <p><b>Status:</b>
                        <span class="badge <?= $g['status'] ?>">
                            <?= ucfirst($g['status']) ?>
                        </span>
                    </p>

                    <p><b>Members:</b> <?= $assignedCount ?> / <?= $maxMembers ?></p>
                </div>

                <!-- 🔹 MEMBERS LIST -->

                <div  class="form-box">
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
            </div>
            <a href="index.php" class="btn-secondary">← Back</a>
        </div>
    </div>
    </div>
</body>
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

</html>
