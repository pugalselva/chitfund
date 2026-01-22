<?php
include 'auth.php';
include '../config/database.php';

$auctionId = (int) $_GET['auction_id'];

$stmt = $conn->prepare("
    SELECT a.*, g.group_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    WHERE a.id=?
");
$stmt->bind_param('i', $auctionId);
$stmt->execute();
$auction = $stmt->get_result()->fetch_assoc();

if (!$auction) {
    die('Auction not found');
}

/* Check if member belongs to this group */
$memberId = $_SESSION['member_id'];
$checkMember = $conn->prepare("
    SELECT 1 FROM chit_group_members 
    WHERE group_id = ? AND member_id = ?
");
$checkMember->bind_param('is', $auction['chit_group_id'], $memberId);
$checkMember->execute();
if ($checkMember->get_result()->num_rows === 0) {
    die('You are not a member of this group');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎰 Kulukkal Lottery - Live View</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .spin-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
            margin: 30px auto;
        }

        h2 {
            color: #2d3748;
            font-size: 2em;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #718096;
            font-size: 0.9em;
            margin-bottom: 25px;
        }

        .wheel-container {
            position: relative;
            width: 320px;
            height: 320px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pointer {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 12px solid transparent;
            border-right: 12px solid transparent;
            border-top: 20px solid #667eea;
            z-index: 10;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        canvas {
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.3));
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .status-badge.waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.spinning {
            background: #dbeafe;
            color: #1e40af;
            animation: pulse 1s infinite;
        }

        .status-badge.completed {
            background: #dcfce7;
            color: #166534;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .winner-box {
            margin-top: 25px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 15px;
            border: 2px solid rgba(102, 126, 234, 0.3);
            display: none;
        }

        .winner-box.show {
            display: block;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .winner-box .trophy {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .winner-box h3 {
            color: #2d3748;
            font-size: 1.6em;
            margin-bottom: 5px;
        }

        .winner-box .amount {
            color: #667eea;
            font-size: 1.3em;
            font-weight: 700;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="spin-container">
        <h2>🎰 Kulukkal Lottery</h2>
        <p class="subtitle">
            <?= htmlspecialchars($auction['group_name']) ?> – Month
            <?= $auction['auction_month'] ?>
        </p>

        <div class="status-badge <?= $auction['status'] === 'completed' ? 'completed' : 'waiting' ?>" id="statusBadge">
            <?php if ($auction['status'] === 'completed'): ?>
                ✅ Winner Announced
            <?php else: ?>
                ⏳ Waiting for admin to spin...
            <?php endif; ?>
        </div>

        <div class="wheel-container">
            <div class="pointer"></div>
            <canvas id="wheel" width="320" height="320"></canvas>
        </div>

        <div class="winner-box <?= $auction['status'] === 'completed' ? 'show' : '' ?>" id="winnerBox">
            <div class="trophy">🏆</div>
            <h3 id="winnerName">
                <?php
                if ($auction['status'] === 'completed' && $auction['winner_member_id']) {
                    $winner = $conn->query("
                        SELECT full_name FROM members 
                        WHERE member_id='{$auction['winner_member_id']}'
                    ")->fetch_assoc();
                    echo htmlspecialchars($winner['full_name']);
                }
                ?>
            </h3>
            <p class="amount">₹
                <?= number_format($auction['winning_bid_amount'] ?? $auction['starting_bid_amount']) ?>
            </p>
        </div>

        <a href="auctions_view.php" class="back-link">← Back to Auctions</a>
    </div>

    <script>
        const auctionId = <?= $auctionId ?>;
        const canvas = document.getElementById('wheel');
        const ctx = canvas.getContext('2d');

        let members = [];
        let angle = 0;
        let wasCompleted = <?= $auction['status'] === 'completed' ? 'true' : 'false' ?>;

        // Fetch members for wheel
        fetch(`../admin/auctions/ajax/get_kulukkal_members.php?auction_id=${auctionId}`)
            .then(r => r.json())
            .then(data => {
                members = data;
                drawWheel();
            })
            .catch(err => console.error('Failed to load members:', err));

        function drawWheel() {
            if (members.length === 0) return;

            const slice = (2 * Math.PI) / members.length;
            const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#43e97b'];

            // Start drawing from top (12 o'clock = -PI/2)
            const startOffset = -Math.PI / 2;

            ctx.clearRect(0, 0, 320, 320);

            members.forEach((m, i) => {
                const startAngle = startOffset + i * slice;
                const endAngle = startOffset + (i + 1) * slice;

                ctx.beginPath();
                ctx.moveTo(160, 160);
                ctx.arc(160, 160, 150, startAngle, endAngle);
                ctx.fillStyle = colors[i % colors.length];
                ctx.fill();

                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 2;
                ctx.stroke();

                ctx.save();
                ctx.translate(160, 160);
                ctx.rotate(startAngle + slice / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 12px Segoe UI';
                ctx.fillText(m.full_name, 130, 4);
                ctx.restore();
            });

            // Center circle
            ctx.beginPath();
            ctx.arc(160, 160, 20, 0, 2 * Math.PI);
            ctx.fillStyle = '#fff';
            ctx.fill();
            ctx.strokeStyle = '#667eea';
            ctx.lineWidth = 2;
            ctx.stroke();
        }

        // Poll for winner updates
        function checkForWinner() {
            fetch(`../admin/auctions/ajax/get_winner.php?auction_id=${auctionId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.winner_member_id && !wasCompleted) {
                        // Winner announced! Play animation
                        wasCompleted = true;
                        animateToWinner(data.winner_member_id, data.winner_name, data.winning_amount);
                    }
                });
        }

        function animateToWinner(winnerId, winnerName, amount) {
            document.getElementById('statusBadge').className = 'status-badge spinning';
            document.getElementById('statusBadge').innerText = '🎡 Spinning...';

            // Find winner index
            let winnerIndex = members.findIndex(m => m.member_id === winnerId);
            if (winnerIndex === -1) winnerIndex = 0;

            // Calculate target angle - same logic as admin
            const slice = (2 * Math.PI) / members.length;
            const segmentCenterOffset = winnerIndex * slice + slice / 2;

            // Rotate to land this segment at top
            const numRotations = 5;
            const extraSpins = numRotations * 2 * Math.PI;
            const totalRotation = extraSpins - segmentCenterOffset;

            const duration = 4000;
            const start = performance.now();

            function animate(t) {
                const progress = Math.min((t - start) / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                angle = easeProgress * totalRotation;

                ctx.clearRect(0, 0, 320, 320);
                ctx.save();
                ctx.translate(160, 160);
                ctx.rotate(angle);
                ctx.translate(-160, -160);
                drawWheel();
                ctx.restore();

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    // Show winner
                    document.getElementById('statusBadge').className = 'status-badge completed';
                    document.getElementById('statusBadge').innerText = '✅ Winner Announced';
                    document.getElementById('winnerName').innerText = winnerName;
                    document.getElementById('winnerBox').classList.add('show');
                }
            }
            requestAnimationFrame(animate);
        }

        // Check every 2 seconds for winner
        if (!wasCompleted) {
            setInterval(checkForWinner, 2000);
        }
    </script>
</body>

</html>