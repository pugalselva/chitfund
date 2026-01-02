<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

if (!isset($_GET['id'])) {
    die('Group ID missing');
}

$groupId = (int)$_GET['id'];

/* 🔹 Fetch group */
$gstmt = $conn->prepare("SELECT * FROM chit_groups WHERE id=?");
$gstmt->bind_param("i", $groupId);
$gstmt->execute();
$group = $gstmt->get_result()->fetch_assoc();

if (!$group) {
    die('Group not found');
}

/* 🔹 Fetch assigned members */
$mstmt = $conn->prepare("
    SELECT 
        m.member_id,
        m.full_name,
        m.mobile,
        m.email,
        gm.joined_at
    FROM chit_group_members gm
    JOIN members m ON m.member_id = gm.member_id
    WHERE gm.group_id = ?
    ORDER BY gm.joined_at DESC
");
$mstmt->bind_param("i", $groupId);
$mstmt->execute();
$members = $mstmt->get_result();

$memberCount = $members->num_rows;
$maxMembers  = $group['total_members'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Chit Group</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .badge { padding:4px 8px; border-radius:6px; color:#fff; font-size:12px }
        .active { background:#16a34a }
        .upcoming { background:#2563eb }
        .completed { background:#6b7280 }
        .search-box { margin-bottom:10px }
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
    <h3><?= htmlspecialchars($group['group_name']) ?></h3>

    <p><b>Group Code:</b> <?= $group['group_code'] ?></p>
    <p><b>Auction Type:</b> <?= $group['auction_type'] ?></p>
    <p><b>Monthly Contribution:</b> ₹<?= number_format($group['monthly_contribution']) ?></p>
    <p><b>Duration:</b> <?= $group['duration_months'] ?> months</p>
    <p><b>Status:</b>
        <span class="badge <?= $group['status'] ?>">
            <?= ucfirst($group['status']) ?>
        </span>
    </p>

    <p><b>Members:</b> <?= $memberCount ?> / <?= $maxMembers ?></p>
</div>

<!-- 🔹 MEMBERS LIST -->
<div class="table-box">
    <h3>Assigned Members</h3>

    <input type="text" id="search" class="search-box"
           placeholder="Search by ID or name...">

    <table id="memberTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Member ID</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Joined At</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; while ($m = $members->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($m['member_id']) ?></td>
                <td><?= htmlspecialchars($m['full_name']) ?></td>
                <td><?= htmlspecialchars($m['mobile']) ?></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?= date('d M Y', strtotime($m['joined_at'])) ?></td>
            </tr>
        <?php endwhile; ?>

        <?php if ($memberCount === 0): ?>
            <tr>
                <td colspan="6" style="text-align:center;color:#777">
                    No members assigned yet
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="index.php" class="btn-secondary">← Back</a>

</div>
</div>
</div>

<script>
document.getElementById('search').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll('#memberTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ''
            : 'none';
    });
});
</script>

</body>
</html>
