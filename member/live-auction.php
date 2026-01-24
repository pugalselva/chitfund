<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$auctionId = (int) ($_GET['auction_id'] ?? 0);

if ($auctionId > 0) {
    /* Fetch specific auction */
    $q = $conn->prepare("
        SELECT a.*, cg.group_name, m.full_name as winner_name
        FROM auctions a
        JOIN chit_groups cg ON cg.id = a.chit_group_id
        LEFT JOIN members m ON m.member_id = a.winner_member_id
        WHERE a.id = ?
    ");
    $q->bind_param("i", $auctionId);
    $q->execute();
    $result = $q->get_result();
} else {
    /* Get active auction */
    $result = $conn->query("
        SELECT a.*, cg.group_name
        FROM auctions a
        JOIN chit_groups cg ON cg.id = a.chit_group_id
        WHERE a.status = 'active'
        LIMIT 1
    ");
}

if ($result->num_rows == 0) {
    echo "<div style='padding:20px'>Auction not found</div>";
    exit();
}

$auction = $result->fetch_assoc();
$auctionId = $auction['id'];
$poolAmount = $auction['starting_bid_amount'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Auction</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .auction-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background: #fff;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .auction-header {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            padding: 1.5rem;
            color: white;
            text-align: center;
        }

        .auction-header.completed {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bid-amount-large {
            font-size: 3.5rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .pulse-animation {
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .bid-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .bid-item {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
        }

        .bid-item:hover {
            background-color: #f9fafb;
        }

        .bid-item:first-child {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .form-control-lg {
            border-radius: 12px;
            padding: 1rem;
            font-size: 1.25rem;
        }

        .btn-bid {
            border-radius: 12px;
            padding: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
            background-color: #ef4444;
            border: none;
            color: white;
            transition: all 0.2s;
        }

        .btn-bid:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
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
                            <h4 class="mb-0 fw-bold text-dark">Live Auction</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">Place your bid now</p>
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
                <div class="row g-4 justify-content-center">
                    <!-- Auction Main Panel -->
                    <div class="col-12 col-xl-8">
                        <div class="card auction-card h-100">
                            <div class="auction-header <?= $auction['status'] === 'completed' ? 'completed' : '' ?>">
                                <h3 class="fw-bold mb-1"><?= htmlspecialchars($auction['group_name']) ?></h3>
                                <p class="mb-0 opacity-75">Month <?= $auction['auction_month'] ?> · Pool Amount:
                                    ₹<?= number_format($poolAmount) ?></p>
                            </div>

                            <div class="card-body p-4 p-md-5 text-center">
                                <?php if ($auction['status'] === 'active'): ?>
                                    <!-- ACTIVE AUCTION -->
                                    <h6 class="text-uppercase text-muted fw-bold mb-3">Current Lowest Bid</h6>

                                    <div class="bid-highlight mb-5">
                                        <div class="display-1 fw-bold text-danger mb-2" id="lowestBid">₹ —</div>
                                        <div class="badge bg-light text-secondary rounded-pill px-3 py-2 border"
                                            id="lowestBidBy">
                                            No bids yet
                                        </div>
                                    </div>

                                    <div class="card bg-light border-0 p-4 mx-auto" style="max-width: 500px;">
                                        <h5 class="fw-bold mb-3">Place Your Bid</h5>
                                        <p class="text-muted small mb-4">Enter an amount lower than the current lowest bid
                                            to increase the discount for everyone.</p>

                                        <div class="mb-3">
                                            <input type="number" class="form-control form-control-lg text-center fw-bold"
                                                id="bidAmount" placeholder="Enter amount (e.g. 5000)">
                                            <div class="form-text mt-2">Maximum allowed: ₹<?= number_format($poolAmount) ?>
                                            </div>
                                        </div>

                                        <button class="btn btn-bid w-100" onclick="placeBid()">
                                            <i class="fas fa-gavel me-2"></i>Place Bid
                                        </button>
                                    </div>

                                <?php else: ?>
                                    <!-- COMPLETED AUCTION -->
                                    <div class="py-5">
                                        <div class="mb-4">
                                            <i class="fas fa-trophy text-warning fa-5x mb-4"></i>
                                            <h2 class="fw-bold text-dark">Auction Completed!</h2>
                                            <p class="text-muted">The winner has been declared.</p>
                                        </div>

                                        <div class="alert alert-success d-inline-block px-5 py-4 border-0 shadow-sm">
                                            <h6 class="text-success text-uppercase opacity-75 mb-2">Winner</h6>
                                            <h3 class="fw-bold mb-3"><?= htmlspecialchars($auction['winner_name'] ?? '—') ?>
                                            </h3>
                                            <div class="d-flex justify-content-center gap-4">
                                                <div class="text-center px-4 border-end border-success border-opacity-25">
                                                    <small class="d-block text-success opacity-75">Winning Bid</small>
                                                    <strong
                                                        class="fs-5">₹<?= number_format($auction['winning_bid_amount']) ?></strong>
                                                </div>
                                                <div class="text-center px-4">
                                                    <small class="d-block text-success opacity-75">Total Discount</small>
                                                    <strong
                                                        class="fs-5">₹<?= number_format($poolAmount - $auction['winning_bid_amount']) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bid History Sidebar -->
                    <div class="col-12 col-xl-4">
                        <div class="card auction-card h-100">
                            <div class="card-header bg-white border-bottom p-3">
                                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-history me-2 text-primary"></i>Live
                                    Bid History</h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="bidList" class="bid-list">
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Loading bids...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const auctionId = <?= $auctionId ?>;

        /* Load lowest bid */
        function loadLowestBid() {
            fetch(`ajax/get_lowest_bid.php?auction_id=${auctionId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.bid_amount) {
                        const bidEl = document.getElementById('lowestBid');
                        if (bidEl) {
                            // Simple animation if value changed
                            if (bidEl.innerText !== '₹' + data.bid_amount) {
                                bidEl.classList.add('pulse-animation');
                                setTimeout(() => bidEl.classList.remove('pulse-animation'), 500);
                            }
                            bidEl.innerText = '₹' + data.bid_amount;
                        }

                        const byEl = document.getElementById('lowestBidBy');
                        if (byEl) byEl.innerText = 'by ' + data.full_name;
                    }
                });
        }

        /* Load all bids */
        function loadBidList() {
            fetch(`ajax/get_all_bids.php?auction_id=${auctionId}`)
                .then(res => res.json())
                .then(bids => {
                    let html = '';
                    if (bids.length === 0) {
                        html = '<div class="text-center py-5 text-muted"><p>No bids placed yet</p></div>';
                    } else {
                        bids.forEach((b, index) => {
                            const isLowest = index === 0;
                            html += `<div class="bid-item ${isLowest ? 'bg-light' : ''}">
                                    <div>
                                        <div class="fw-bold text-dark">${b.full_name}</div>
                                        <small class="text-muted">${new Date().toLocaleTimeString()}</small>
                                    </div>
                                    <div class="fw-bold ${isLowest ? 'text-danger fs-5' : 'text-dark'}">₹${b.bid_amount}</div>
                                 </div>`;
                        });
                    }
                    const list = document.getElementById('bidList');
                    if (list) list.innerHTML = html;
                });
        }

        /* Place bid */
        function placeBid() {
            const amountInput = document.getElementById('bidAmount');
            const amount = amountInput.value;

            if (!amount) {
                alert('Enter bid amount');
                return;
            }

            // Disable button
            const btn = document.querySelector('.btn-bid');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Placing...';

            fetch('ajax/place_bid.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `auction_id=${auctionId}&bid_amount=${amount}`
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.error) {
                        alert(resp.error);
                    } else {
                        amountInput.value = '';
                        loadLowestBid();
                        loadBidList();
                    }
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch(err => {
                    console.error(err);
                    alert('Failed to place bid');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }

        /* AUTO REFRESH EVERY 2 SECONDS (More frequent for live) */
        setInterval(() => {
            loadLowestBid();
            loadBidList();
            // Check if auction closed
            fetch(`ajax/close_auction.php?auction_id=${auctionId}`)
                .then(r => r.json())
                .then(status => {
                    if (status && status.completed) location.reload();
                });
        }, 2000);

        /* Initial load */
        loadLowestBid();
        loadBidList();

        // Toggle Sidebar for Mobile
    </script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>