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

/* Fetch eligible members for spin wheel (who haven't won) */
$membersForWheel = $conn->query("
    SELECT m.member_id, m.full_name
    FROM chit_group_members gm
    JOIN members m ON m.member_id = gm.member_id
    WHERE gm.group_id = {$auction['chit_group_id']}
    AND gm.member_id NOT IN (
        SELECT winner_member_id FROM auctions
        WHERE chit_group_id = {$auction['chit_group_id']}
        AND winner_member_id IS NOT NULL
    )
    ORDER BY RAND()
    LIMIT 4
");

$wheelMembers = [];
while ($m = $membersForWheel->fetch_assoc()) {
    $wheelMembers[] = $m;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Auction - Kulukkal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .wheel-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        .wheel-title {
            font-size: 1.8em;
            margin-bottom: 10px;
        }

        .wheel-subtitle {
            opacity: 0.9;
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
            border-top: 20px solid #fff;
            z-index: 10;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        canvas {
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.3));
        }

        .waiting-msg {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px 25px;
            border-radius: 50px;
            display: inline-block;
            font-size: 0.95em;
        }

        .winner-box {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .winner-box .trophy {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .winner-box h2 {
            color: #2d3748;
            margin-bottom: 5px;
        }

        .winner-box .amount {
            color: #667eea;
            font-size: 1.5em;
            font-weight: 700;
        }

        .participants-list {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .participants-list h4 {
            margin-bottom: 12px;
            color: #374151;
        }

        .participants-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .participants-list li {
            padding: 8px 12px;
            background: #fff;
            margin-bottom: 6px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">🎰 Open Auction (Kulukkal)</div>
                    <div class="page-subtitle">
                        <?= $auction['group_name'] ?> – Month <?= $auction['auction_month'] ?>
                    </div>
                </div>
            </div>
            <div class="content">

                <?php if ($auction['status'] === 'completed'): ?>
                    <!-- WINNER DISPLAY -->
                    <div class="winner-box">
                        <div class="trophy">🏆</div>
                        <h3>Winner Announced!</h3>
                        <?php
                        $w = $conn->query("
                        SELECT full_name FROM members
                        WHERE member_id='{$auction['winner_member_id']}'
                    ")->fetch_assoc();
                        ?>
                        <h2><?= htmlspecialchars($w['full_name']) ?></h2>
                        <p class="amount">₹<?= number_format($auction['winning_bid_amount']) ?></p>
                    </div>

                <?php else: ?>
                    <!-- SPIN WHEEL (View Only for Members) -->
                    <div class="wheel-section">
                        <div class="wheel-title">🎰 Kulukkal Lottery</div>
                        <p class="wheel-subtitle">Eligible participants for this month</p>

                        <div class="wheel-container">
                            <div class="pointer"></div>
                            <canvas id="wheel" width="320" height="320"></canvas>
                        </div>

                        <div class="waiting-msg">
                            ⏳ Waiting for admin to spin the wheel...
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ALL PARTICIPANTS -->
                <div class="participants-list">
                    <h4>👥 All Group Members</h4>
                    <ul>
                        <?php
                        $allMembers = $conn->query("
                            SELECT m.full_name
                            FROM chit_group_members gm
                            JOIN members m ON m.member_id = gm.member_id
                            WHERE gm.group_id = {$auction['chit_group_id']}
                        ");
                        while ($m = $allMembers->fetch_assoc()):
                            ?>
                            <li><?= htmlspecialchars($m['full_name']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        const members = <?= json_encode($wheelMembers) ?>;
        const canvas = document.getElementById('wheel');

        if (canvas && members.length > 0) {
            const ctx = canvas.getContext('2d');

            function drawWheel() {
                const slice = (2 * Math.PI) / members.length;
                const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#43e97b'];

                ctx.clearRect(0, 0, 320, 320);

                members.forEach((m, i) => {
                    ctx.beginPath();
                    ctx.moveTo(160, 160);
                    ctx.arc(160, 160, 150, i * slice, (i + 1) * slice);
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.fill();

                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 2;
                    ctx.stroke();

                    ctx.save();
                    ctx.translate(160, 160);
                    ctx.rotate(i * slice + slice / 2);
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

            drawWheel();
        }
    </script>
</body>

</html>