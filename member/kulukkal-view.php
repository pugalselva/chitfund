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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎰 Kulukkal Lottery - Live View</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .spin-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 500px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background elements */
        .spin-container::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #fcd34d, #f59e0b);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        .spin-container::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        h2 {
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .wheel-wrapper {
            position: relative;
            width: 320px;
            height: 320px;
            margin: 0 auto 2rem;
            padding: 10px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .wheel-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pointer {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 25px solid #ef4444;
            z-index: 10;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .status-badge.waiting {
            background-color: #fef3c7;
            color: #b45309;
        }

        .status-badge.spinning {
            background-color: #dbeafe;
            color: #1e40af;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .status-badge.completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .winner-box {
            background: linear-gradient(to bottom right, #ffffff, #f9fafb);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 1rem;
            border: 2px solid #e5e7eb;
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .winner-box.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
            border-color: #fbbf24;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .trophy-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 6px rgba(245, 158, 11, 0.2));
        }

        .winner-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .winning-amount {
            color: #4f46e5;
            font-size: 1.25rem;
            font-weight: 700;
            background: #e0e7ff;
            padding: 4px 12px;
            border-radius: 8px;
            display: inline-block;
        }

        .btn-back {
            margin-top: 2rem;
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="spin-container">
        <h2>🎰 Kulukkal Lottery</h2>
        <p class="subtitle">
            <?= htmlspecialchars($auction['group_name']) ?> <span class="mx-2">•</span> Month
            <?= $auction['auction_month'] ?>
        </p>

        <div class="status-badge <?= $auction['status'] === 'completed' ? 'completed' : 'waiting' ?>" id="statusBadge">
            <?php if ($auction['status'] === 'completed'): ?>
                <i class="fas fa-check-circle"></i> Winner Announced
            <?php else: ?>
                <i class="fas fa-clock"></i> Waiting for admin to spin...
            <?php endif; ?>
        </div>

        <div class="wheel-wrapper">
            <div class="wheel-container">
                <div class="pointer"></div>
                <canvas id="wheel" width="300" height="300"></canvas>
            </div>
        </div>

        <div class="winner-box <?= $auction['status'] === 'completed' ? 'show' : '' ?>" id="winnerBox">
            <div class="trophy-icon"><i class="fas fa-trophy"></i></div>
            <div class="text-uppercase text-muted fw-bold small mb-2">Congratulations</div>
            <h3 class="winner-name" id="winnerName">
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
            <div class="winning-amount">
                ₹<?= number_format($auction['winning_bid_amount'] ?? $auction['starting_bid_amount']) ?>
            </div>
        </div>

        <div>
            <a href="auctions_view.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Back to Auctions</a>
        </div>
    </div>

    <script>
        const auctionId = <?= $auctionId ?>;
        const canvas = document.getElementById('wheel');
        const ctx = canvas.getContext('2d');

        // Adjust for retina displays if needed, but 300x300 fixed is fine for now
        // const scale = window.devicePixelRatio;
        // canvas.width = 300 * scale;
        // canvas.height = 300 * scale;
        // ctx.scale(scale, scale);

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

            const centerX = 150;
            const centerY = 150;
            const radius = 140;

            const slice = (2 * Math.PI) / members.length;
            const colors = ['#6366f1', '#ec4899', '#8b5cf6', '#10b981', '#f59e0b', '#3b82f6'];

            // Start drawing from top (12 o'clock = -PI/2)
            const startOffset = -Math.PI / 2;

            ctx.clearRect(0, 0, 300, 300);

            members.forEach((m, i) => {
                const startAngle = startOffset + i * slice;
                const endAngle = startOffset + (i + 1) * slice;

                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.fillStyle = colors[i % colors.length];
                ctx.fill();

                // Segment border
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 2;
                ctx.stroke();

                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + slice / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 11px Inter';
                // Move text slightly away from edge
                ctx.fillText(m.full_name.substring(0, 15), radius - 10, 4);
                ctx.restore();
            });

            // Center circle
            ctx.beginPath();
            ctx.arc(centerX, centerY, 25, 0, 2 * Math.PI);
            ctx.fillStyle = '#ffffff';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(centerX, centerY, 20, 0, 2 * Math.PI);
            ctx.fillStyle = '#4f46e5';
            ctx.fill();

            // Inner icon
            ctx.font = '14px FontAwesome';
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            // ctx.fillText('\uf005', centerX, centerY); // Star icon
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
            const badge = document.getElementById('statusBadge');
            badge.className = 'status-badge spinning';
            badge.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Spinning...';

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

            const duration = 5000; // 5 seconds
            const start = performance.now();

            const centerX = 150;
            const centerY = 150;

            function animate(t) {
                const progress = Math.min((t - start) / duration, 1);
                // Ease out cubic
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                angle = easeProgress * totalRotation;

                ctx.clearRect(0, 0, 300, 300);
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(angle);
                ctx.translate(-centerX, -centerY);
                drawWheel();
                ctx.restore();

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    // Show winner
                    badge.className = 'status-badge completed';
                    badge.innerHTML = '<i class="fas fa-check-circle"></i> Winner Announced';

                    document.getElementById('winnerName').innerText = winnerName;
                    document.getElementById('winnerBox').classList.add('show');

                    // Add confetti/celebration here if desired
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