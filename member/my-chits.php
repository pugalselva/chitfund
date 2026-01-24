<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$memberId = $_SESSION['member_id'];

/* Fetch member chit groups with completed months */
// $stmt = $conn->prepare("
// SELECT 
//     g.*,
//     COUNT(a.id) AS completed_months
// FROM chit_groups g
// JOIN chit_group_members gm ON gm.group_id = g.id
// LEFT JOIN auctions a 
//     ON a.chit_group_id = g.id 
//     AND a.status = 'completed'
// WHERE gm.member_id = ?
// GROUP BY g.id
// ORDER BY g.created_at DESC
// ");
$stmt = $conn->prepare("
SELECT 
    g.*,
    COUNT(a.id) AS completed_months,

    /* Last completed auction pool amount */
    (
        SELECT a2.starting_bid_amount
        FROM auctions a2
        WHERE a2.chit_group_id = g.id
          AND a2.status = 'completed'
        ORDER BY a2.auction_month DESC
        LIMIT 1
    ) AS last_pool_amount,

    /* Last winning bid */
    (
        SELECT a3.winning_bid_amount
        FROM auctions a3
        WHERE a3.chit_group_id = g.id
          AND a3.status = 'completed'
        ORDER BY a3.auction_month DESC
        LIMIT 1
    ) AS last_winning_amount

FROM chit_groups g
JOIN chit_group_members gm ON gm.group_id = g.id
LEFT JOIN auctions a 
    ON a.chit_group_id = g.id 
   AND a.status = 'completed'
WHERE gm.member_id = ?
GROUP BY g.id
ORDER BY g.created_at DESC
");


$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();

// $members = (int)$g['total_members'];
// $monthlyContribution = 0;

// if ($poolAmount > 0 && $members > 0) {
//     $monthlyContribution = round($poolAmount / $members);
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Chit Groups</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .chit-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            background: #fff;
            height: 100%;
        }

        .chit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .chit-header {
            padding: 1.5rem;
            background: linear-gradient(to right, #f8fafc, #fff);
            border-bottom: 1px solid #f1f5f9;
        }

        .chit-body {
            padding: 1.5rem;
        }

        .chit-footer {
            padding: 1rem 1.5rem;
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }

        .badge-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-soft-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
        }

        .progress-thin {
            height: 6px;
            border-radius: 3px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex" id="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            <!-- Navbar -->
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-light text-primary me-3 d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">My Chit Groups</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">Manage your chit memberships</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($name) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($email) ?></small>
                        </div>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <?php if ($result->num_rows === 0): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-folder-open fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">No chit groups assigned to you yet.</h5>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php while ($g = $result->fetch_assoc()):
                        $completed = (int) $g['completed_months'];
                        $duration = (int) $g['duration_months'];
                        $members = (int) $g['total_members'];
                        $winningAmount = (float) $g['last_winning_amount'];
                        $poolAmount = (float) ($g['last_pool_amount'] ?? 0);

                        $monthlyContribution = 0;
                        if ($members > 0 && $winningAmount > 0) {
                            $monthlyContribution = round($winningAmount / $members);
                        }

                        $percent = $duration > 0 ? ($completed / $duration) * 100 : 0;
                        ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card chit-card h-100">
                                <div class="chit-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($g['group_name']) ?></h5>
                                        <span
                                            class="badge bg-light text-secondary border"><?= htmlspecialchars($g['group_code']) ?></span>
                                    </div>
                                    <span
                                        class="badge <?= $g['status'] == 'active' ? 'badge-soft-success' : 'badge-soft-secondary' ?> px-3 py-2 rounded-pill">
                                        <?= ucfirst($g['status']) ?>
                                    </span>
                                </div>
                                <div class="chit-body">
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="stat-label"><i class="fas fa-coins text-warning me-1"></i> Pool
                                                Amount</div>
                                            <div class="stat-value text-primary">
                                                ₹<?= $poolAmount > 0 ? number_format($poolAmount) : '—' ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-label"><i
                                                    class="fas fa-hand-holding-usd text-success me-1"></i> Monthly Pay</div>
                                            <div class="stat-value text-success">
                                                ₹<?= $monthlyContribution > 0 ? number_format($monthlyContribution) : '—' ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-label"><i class="fas fa-users text-info me-1"></i> Members
                                            </div>
                                            <div class="stat-value"><?= $g['total_members'] ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-label"><i class="fas fa-calendar-alt text-secondary me-1"></i>
                                                Duration</div>
                                            <div class="stat-value"><?= $duration ?> Months</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted fw-semibold">Progress</small>
                                        <small class="text-primary fw-bold"><?= $completed ?>/<?= $duration ?></small>
                                    </div>
                                    <div class="progress progress-thin bg-light mb-2">
                                        <div class="progress-bar bg-primary rounded-pill" role="progressbar"
                                            style="width: <?= $percent ?>%"></div>
                                    </div>
                                    <?php if ($monthlyContribution > 0): ?>
                                        <small class="text-muted fst-italic" style="font-size: 0.75rem;">
                                            * Contribution based on last winning bid (₹<?= number_format($winningAmount) ?>)
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="chit-footer d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small">Total Value</div>
                                        <div class="fw-bold text-dark">₹<?= number_format($g['total_value']) ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Type</div>
                                        <div class="fw-bold text-dark"><?= ucfirst($g['auction_type']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <!-- Custom Scripts -->
        <script src="../assets/js/scripts.js"></script>
</body>

</html>