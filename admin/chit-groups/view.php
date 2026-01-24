<?php
// session_start();
include '../../config/database.php';
include '../auth.php';


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

$completed = $g['completed_months'];
$total = $g['duration_months'];
$remaining = $total - $completed;
$progress = $total > 0 ? round(($completed / $total) * 100) : 0;
?>


<!DOCTYPE html>
<html>

<head>
    <title>Chit Group Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h4 class="mb-0 fw-bold"><?= htmlspecialchars($g['group_name']) ?></h4>
                        <small class="text-secondary">Group Code: <span class="fw-medium text-dark"><?= $g['group_code'] ?></span></small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="../auctions/index.php?group_id=<?= $g['id'] ?>" class="btn btn-primary">
                            <i class="fa fa-gavel me-1"></i> View Auctions
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    
                    <!-- Stats / Info -->
                    <div class="col-12 col-xl-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem;">Status & Progress</h6>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-medium">Completion status</span>
                                            <span class="badge <?= $g['status'] == 'active' ? 'bg-success' : ($g['status'] == 'completed' ? 'bg-secondary' : 'bg-warning text-dark') ?>">
                                                <?= ucfirst($g['status']) ?>
                                            </span>
                                        </div>

                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span>Progress (<?= $completed ?>/<?= $total ?> months)</span>
                                                <span class="fw-bold"><?= $progress ?>%</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $progress ?>%"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4 text-center">
                                            <div class="col-6 border-end">
                                                <h4 class="mb-0 fw-bold text-success"><?= $completed ?></h4>
                                                <small class="text-muted">Completed</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="mb-0 fw-bold text-secondary"><?= $remaining ?></h4>
                                                <small class="text-muted">Remaining</small>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem;">Group Details</h6>
                                        
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span class="text-secondary">Auction Type</span>
                                                <span class="fw-medium"><?= $g['auction_type'] ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span class="text-secondary">Pool Amount</span>
                                                <span class="fw-bold text-dark">₹<?= number_format($g['total_value']) ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span class="text-secondary">Members</span>
                                                <span class="fw-medium"><?= $assignedCount ?> / <?= $maxMembers ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span class="text-secondary">Start Date</span>
                                                <span class="fw-medium"><?= date('d M Y', strtotime($g['start_date'])) ?></span>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Members List -->
                    <div class="col-12 col-xl-8">
                        <div class="card border-0 shadow-sm h-100">
                             <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Assigned Members (<?= $assignedCount ?>)</h6>
                                <input type="text" id="memberSearch" class="form-control form-control-sm w-auto" placeholder="Search member...">
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <?php if ($assignedCount === 0): ?>
                                        <div class="p-5 text-center text-muted">
                                            <i class="fas fa-user-friends fa-3x mb-3 text-light"></i>
                                            <p>No members assigned to this group yet.</p>
                                        </div>
                                    <?php else: ?>
                                        <table class="table table-hover align-middle mb-0" id="membersTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4">#</th>
                                                    <th>Member ID</th>
                                                    <th>Name</th>
                                                    <th>Mobile</th>
                                                    <th>Joined On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i=1; while($m = $members->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="ps-4 text-muted"><?= $i++ ?></td>
                                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($m['member_id']) ?></span></td>
                                                    <td class="fw-medium"><?= htmlspecialchars($m['full_name']) ?></td>
                                                    <td class="text-secondary"><?= htmlspecialchars($m['mobile']) ?></td>
                                                    <td class="text-secondary small"><?= date('d M Y', strtotime($m['joined_at'])) ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <?php include '../layout/scripts.php'; ?>
    <script>
        const searchInput = document.getElementById('memberSearch');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                document.querySelectorAll('#membersTable tbody tr').forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
                });
            });
        }
    </script>

</body>
</html>
