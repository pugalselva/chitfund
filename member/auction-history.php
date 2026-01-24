<?php
include 'auth.php';
include '../config/database.php';
// Auth handled by auth.php

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$memberId = $_SESSION['member_id'];

/* Get completed auctions where member belongs to group */
$sql = "
SELECT 
    a.id AS auction_id,
    a.auction_month,
    a.auction_datetime,
    a.starting_bid_amount AS pool_amount,
    a.winning_bid_amount,
    a.winner_member_id,
    cg.group_name,
    wm.full_name AS winner_name,
    (
        SELECT COUNT(*) 
        FROM chit_group_members 
        WHERE group_id = a.chit_group_id
    ) AS total_members
FROM auctions a
JOIN chit_groups cg 
    ON cg.id = a.chit_group_id
JOIN chit_group_members cgm 
    ON cgm.group_id = a.chit_group_id   -- ✅ FIX HERE
LEFT JOIN members wm 
    ON wm.member_id = a.winner_member_id
WHERE 
    a.status = 'completed'
    AND cgm.member_id = ?
ORDER BY a.auction_datetime DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('SQL ERROR: ' . $conn->error);
}

$stmt->bind_param('s', $memberId);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction History</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .history-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background: #fff;
            margin-bottom: 2rem;
            transition: transform 0.2s;
        }

        .history-card:hover {
            transform: translateY(-2px);
        }

        .history-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .history-body {
            padding: 1.5rem;
        }

        .history-footer {
            background-color: #f0fdf4;
            border-top: 1px solid #dcfce7;
            padding: 1rem 1.5rem;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            color: #166534;
            font-size: 0.9em;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .icon-blue {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .icon-purple {
            background-color: #f3e8ff;
            color: #9333ea;
        }

        .icon-green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.25rem;
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
                            <h4 class="mb-0 fw-bold text-dark">Auction History</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">View past results & dividends</p>
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
                <?php if ($result->num_rows == 0): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-history fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">No auction history available yet.</h5>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php while ($row = $result->fetch_assoc()):
                        $totalDiscount = $row['pool_amount'] - $row['winning_bid_amount'];
                        $memberShare = round($totalDiscount / $row['total_members']);
                        ?>
                        <div class="col-12 col-xl-10 offset-xl-1">
                            <div class="card history-card">
                                <div
                                    class="history-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">
                                            <?= htmlspecialchars($row['group_name']) ?> <span
                                                class="text-muted fw-normal mx-2">|</span> Month
                                            <?= $row['auction_month'] ?>
                                        </h5>
                                        <small
                                            class="text-muted">AUC<?= str_pad($row['auction_id'], 3, '0', STR_PAD_LEFT) ?></small>
                                    </div>
                                    <span class="badge bg-secondary">Completed</span>
                                </div>

                                <div class="history-body">
                                    <div class="row g-4">
                                        <div class="col-12 col-md-4">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box icon-blue">
                                                    <i class="far fa-calendar-alt"></i>
                                                </div>
                                                <div>
                                                    <div class="stat-label">Auction Date</div>
                                                    <div class="fw-bold text-dark">
                                                        <?= date('d M Y', strtotime($row['auction_datetime'])) ?>
                                                    </div>
                                                    <small
                                                        class="text-muted"><?= date('h:i A', strtotime($row['auction_datetime'])) ?></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box icon-purple">
                                                    <i class="fas fa-trophy"></i>
                                                </div>
                                                <div>
                                                    <div class="stat-label">Winner</div>
                                                    <div class="fw-bold text-dark">
                                                        <?= htmlspecialchars($row['winner_name'] ?? '—') ?>
                                                    </div>
                                                    <small class="text-muted">ID: <?= $row['winner_member_id'] ?></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box icon-green">
                                                    <i class="fas fa-hand-holding-usd"></i>
                                                </div>
                                                <div>
                                                    <div class="stat-label">Your Share</div>
                                                    <div class="fw-bold text-success fs-5">
                                                        ₹<?= number_format($memberShare) ?></div>
                                                    <small
                                                        class="text-muted"><?= number_format(($totalDiscount / $row['pool_amount']) * 100, 2) ?>%
                                                        total discount</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 text-muted opacity-25">

                                    <div class="row text-center g-3">
                                        <div class="col-4 border-end">
                                            <div class="stat-label">Pool Amount</div>
                                            <div class="fw-bold text-dark fs-5">₹<?= number_format($row['pool_amount']) ?>
                                            </div>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="stat-label">Winning Bid</div>
                                            <div class="fw-bold text-dark fs-5">
                                                ₹<?= number_format($row['winning_bid_amount']) ?></div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-label">Total Discount</div>
                                            <div class="fw-bold text-success fs-5">₹<?= number_format($totalDiscount) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="history-footer">
                                    <i class="fas fa-info-circle me-2"></i>
                                    The discount of <b>₹<?= number_format($totalDiscount) ?></b> was distributed equally.
                                    Your monthly contribution was reduced by <b>₹<?= number_format($memberShare) ?></b>.
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
    <script src="../assets/js/scripts.js"></script>
</body>

</html>