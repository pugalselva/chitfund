<?php
include '../auth.php';
include '../../config/database.php';

$auctionId = (int) ($_GET['auction_id'] ?? 0);
if (!$auctionId)
    die('Auction ID missing');

/* Auction details with winner name */
$stmt = $conn->prepare("
    SELECT a.*, g.group_name, m.full_name as winner_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    LEFT JOIN members m ON m.member_id = a.winner_member_id
    WHERE a.id = ?
");
$stmt->bind_param("i", $auctionId);
$stmt->execute();
$auction = $stmt->get_result()->fetch_assoc();

if (!$auction)
    die('Auction not found');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Live Bidding View</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .bid-card {
            background: #1e293b;
            color: white;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
        }
        .bid-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            color: #4ade80;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
             <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold"><?= htmlspecialchars($auction['group_name']) ?> <span class="badge bg-light text-dark border ms-2">Month <?= $auction['auction_month'] ?></span></h4>
                        <small class="text-secondary">Auction ID #<?= $auctionId ?></small>
                    </div>
                     <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="row g-4 mb-4">
                    
                    <!-- LEFT COLUMN: STATUS CARDS -->
                    <div class="col-12 col-lg-5">
                         <?php if ($auction['auction_type'] === 'Open'): ?>
                            
                            <!-- OPEN AUCTION (KULUKKAL) -->
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-5">
                                    <div class="mb-3">
                                        <i class="fas fa-dice fa-3x text-primary"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">Kulukkal Lottery</h4>
                                    
                                    <?php if ($auction['status'] === 'active'): ?>
                                        <span class="badge bg-success fs-6 mb-3 px-3 py-2">Active</span>
                                        <p class="text-muted">Ready to spin the wheel.</p>
                                        <a href="kulukkal_spin.php?auction_id=<?= $auctionId ?>" class="btn btn-primary btn-lg w-100 mt-2">
                                            <i class="fas fa-play-circle me-2"></i> Go to Spin Page
                                        </a>
                                    <?php elseif ($auction['status'] === 'completed'): ?>
                                        <span class="badge bg-secondary fs-6 mb-3 px-3 py-2">Completed</span>
                                        <p class="text-success fw-bold">Winner Announced</p>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark fs-6 mb-3 px-3 py-2"><?= ucfirst($auction['status']) ?></span>
                                    <?php endif; ?>

                                    <?php if ($auction['status'] === 'completed'): ?>
                                        <div class="mt-4 p-3 bg-light rounded border border-success">
                                            <small class="text-uppercase text-muted fw-bold">Winner</small>
                                            <h4 class="fw-bold text-dark mt-1 mb-1"><?= htmlspecialchars($auction['winner_name'] ?? $auction['winner_member_id']) ?></h4>
                                            <small class="text-success fw-bold">₹<?= number_format($auction['winning_bid_amount']) ?> Won</small>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php else: ?>
                            
                            <!-- REVERSE AUCTION -->
                            <!-- Status Card -->
                            <div class="bid-card shadow mb-3">
                                <small class="text-uppercase text-white-50 fw-bold">
                                    <?= $auction['status'] === 'completed' ? 'Winning Details' : 'Current Lowest Bid' ?>
                                </small>
                                
                                <?php if ($auction['status'] === 'completed'): ?>
                                     <h1 class="text-success">₹<?= number_format($auction['winning_bid_amount']) ?></h1>
                                     <div class="mt-2 text-white">
                                        <i class="fas fa-trophy text-warning me-2"></i>
                                        <?= htmlspecialchars($auction['winner_name'] ?? $auction['winner_member_id']) ?>
                                     </div>
                                <?php else: ?>
                                    <h1 id="lowestBid">₹ —</h1>
                                    <div id="lowestBidBy" class="mt-2 text-white-50 fst-italic">Waiting for bids...</div>
                                <?php endif; ?>
                            </div>

                             <!-- Live Winner Box (Ajax) -->
                            <div id="winnerBox" class="card border-success shadow-sm" style="display:none;">
                                <div class="card-body text-center bg-success text-white rounded">
                                    <h5 class="fw-bold mb-1"><i class="fas fa-trophy me-2 text-warning"></i>Winner Declared!</h5>
                                    <h3 class="fw-bold my-2" id="winnerName"></h3>
                                    <p class="mb-0" id="winnerAmount"></p>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>

                    <!-- RIGHT COLUMN: BID HISTORY (Only for Reverse) -->
                    <div class="col-12 col-lg-7">
                         <?php if ($auction['auction_type'] !== 'Open'): ?>
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Live Bid History</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0 text-center">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>Member</th>
                                                    <th>Bid Amount</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bidTable">
                                                <tr><td colspan="3" class="text-muted py-4">Connecting to feed...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Placeholder for Open Auction -->
                             <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center p-5 text-center text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p>Open Auctions do not have a bid history.<br>Use the Kulukkal Spin Page.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
    
    <?php include '../layout/scripts.php'; ?>
    <script>
        const auctionId = <?= $auctionId ?>;
        const type = "<?= $auction['auction_type'] ?>";

        if (type !== 'Open') {
            /* Only load bids for Reverse Auction */

            function loadBids() {
                fetch(`ajax/get_all_bids.php?auction_id=${auctionId}`)
                    .then(r => r.json())
                    .then(bids => {
                        let html = '';
                        if (bids.length === 0) {
                            html = `<tr><td colspan="3" class="text-muted py-4">No bids placed yet</td></tr>`;
                        } else {
                            bids.forEach(b => {
                                html += `
                            <tr>
                                <td class="fw-medium text-dark">${b.full_name}</td>
                                <td class="fw-bold text-success">₹${b.bid_amount}</td>
                                <td class="text-muted small">${b.created_at}</td>
                            </tr>`;
                            });
                        }
                        const tableBody = document.getElementById('bidTable');
                         // Simple check to avoid unnecessary DOM updates if content is same could be added, 
                         // but for now replacing HTML is fine for simplicity and "live" feel.
                        tableBody.innerHTML = html;
                    })
                    .catch(e => console.error("Error loading bids"));
            }

            /* Lowest bid */
            function loadLowest() {
                const lbEl = document.getElementById('lowestBid');
                if(!lbEl) return; 

                fetch(`ajax/get_lowest_bid.php?auction_id=${auctionId}`)
                    .then(r => r.json())
                    .then(b => {
                        if (b.bid_amount) {
                            document.getElementById('lowestBid').innerText = '₹' + b.bid_amount;
                            document.getElementById('lowestBidBy').innerText = 'Held by ' + b.full_name;
                        }
                    });
            }

            function loadWinner() {
                fetch(`ajax/get_winner.php?auction_id=${auctionId}`)
                    .then(r => r.json())
                    .then(w => {
                        if (w.full_name) {
                            document.getElementById('winnerBox').style.display = 'block';
                            document.getElementById('winnerName').innerText = w.full_name;
                            document.getElementById('winnerAmount').innerText = 'Winning Bid: ₹' + w.winning_bid_amount;
                        }
                    });
            }

            /* Auto refresh */
            setInterval(() => {
                loadBids();
                loadLowest();
                loadWinner();
            }, 3000); // 3 seconds for snappier feel

            loadBids();
            loadLowest();
            loadWinner();
        }
    </script>
</body>
</html>