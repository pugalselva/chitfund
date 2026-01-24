<?php
// session_start();
include '../../config/database.php';

include '../auth.php';

$result = $conn->query("
    SELECT 
        g.*,
        COUNT(a.id) AS completed_months
    FROM chit_groups g
    LEFT JOIN auctions a 
        ON a.chit_group_id = g.id 
        AND a.status = 'completed'
    WHERE g.is_active = 1
    GROUP BY g.id
    ORDER BY g.created_at DESC
");

$count = $result->num_rows;
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = max(5, (int) ($_GET['perPage'] ?? 10));
$offset = ($page - 1) * $perPage;

$total = $conn->query("SELECT COUNT(*) c FROM chit_groups WHERE is_active=1")
    ->fetch_assoc()['c'];

$result = $conn->query("
    SELECT g.*, COUNT(a.id) completed_months
    FROM chit_groups g
    LEFT JOIN auctions a ON a.chit_group_id=g.id AND a.status='completed'
    WHERE g.is_active=1
    GROUP BY g.id
    ORDER BY g.created_at DESC
    LIMIT $perPage OFFSET $offset
");

$totalPages = ceil($total / $perPage);

?>


<!DOCTYPE html>
<html>

<head>
    <title>Chit Groups</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/pagination.js"></script>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">

            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0">Chit Groups</h4>
                        <small class="text-secondary">Manage all chit fund groups</small>
                    </div>
                </div>

                <!-- REDIRECT BUTTON -->
                <div class="mb-4">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Group
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">All Groups (<?= $count ?>)</h5>

                            <div class="d-flex gap-2">
                                <select id="groupFilter" class="form-select w-auto">
                                    <option value="">All Groups</option>
                                    <?php
                                    $groups = $conn->query("SELECT DISTINCT group_name FROM chit_groups");
                                    while ($g = $groups->fetch_assoc()):
                                        ?>
                                        <option value="<?= htmlspecialchars($g['group_name']) ?>">
                                            <?= htmlspecialchars($g['group_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="text" id="searchBox" class="form-control" placeholder="Search..." />
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Group ID</th>
                                        <th>Group Name</th>
                                        <th>Total Value</th>
                                        <th>Members</th>
                                        <th>Duration</th>
                                        <th>Progress</th>
                                        <th>Auction Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="groupTable">
                                    <?php while ($g = $result->fetch_assoc()): ?>

                                        <?php
                                        /* ---------- AUTO COMPLETE GROUP ---------- */
                                        if ($g['completed_months'] >= $g['duration_months'] && $g['status'] !== 'completed') {
                                            $update = $conn->prepare("
                                                                        UPDATE chit_groups
                                                                        SET status='completed'
                                                                        WHERE id=?
                                                                    ");
                                            $update->bind_param('i', $g['id']);
                                            $update->execute();

                                            // Update local variable so UI shows correct status
                                            $g['status'] = 'completed';
                                        }

                                        /* ---------- PROGRESS ---------- */
                                        $percent = $g['duration_months'] > 0 ? round(($g['completed_months'] / $g['duration_months']) * 100) : 0;
                                        ?>

                                        <tr>
                                            <td><span class="badge bg-secondary"><?= $g['group_code'] ?></span></td>
                                            <td>
                                                <strong><?= $g['group_name'] ?></strong><br>
                                                <small class="text-muted">Comm: <?= $g['commission'] ?>%</small>
                                            </td>
                                            <td>₹<?= number_format($g['total_value']) ?></td>
                                            <td><?= $g['total_members'] ?></td>
                                            <td><?= $g['duration_months'] ?> <small>mo</small></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: <?= $percent ?>%"></div>
                                                    </div>
                                                    <small><?= $percent ?>%</small>
                                                </div>
                                                <small
                                                    class="text-muted"><?= $g['completed_months'] ?>/<?= $g['duration_months'] ?></small>
                                            </td>

                                            <td><?= $g['auction_type'] ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match ($g['status']) {
                                                    'active' => 'bg-success',
                                                    'completed' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= ucfirst($g['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <?php if ($g['status'] !== 'completed'): ?>
                                                        <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-pen"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete(<?= (int) $g['id'] ?>)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="view.php?id=<?= $g['id'] ?>"
                                                        class="btn btn-sm btn-info text-white">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <small>Show per page:</small>
                                <select id="perPage" class="form-select form-select-sm w-auto">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                </select>
                            </div>
                            <div id="pagination" class="pagination"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
    <script>
        function confirmDelete(groupId) {
            if (!groupId) {
                alert('Invalid group ID');
                return;
            }

            if (!confirm("Are you sure you want to delete this group?")) {
                return;
            }

            fetch('delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id: groupId
                })
            })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
                        alert('Group deleted successfully');
                        location.reload();
                    } else {
                        alert(result);
                    }
                });
        }
    </script>
</body>

</html>