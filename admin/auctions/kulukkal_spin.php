<?php
include '../auth.php';
include '../../config/database.php';

$auctionId = (int) $_GET['auction_id'];

// Fetch auction status and winner if exists
$auction = $conn->query("
    SELECT a.*, m.full_name as winner_name, m.member_id as winner_id
    FROM auctions a
    LEFT JOIN members m ON m.member_id = a.winner_member_id
    WHERE a.id = $auctionId
")->fetch_assoc();

if (!$auction)
    die("Auction not found");

$isCompleted = ($auction['status'] === 'completed');
$winnerName = $isCompleted ? $auction['winner_name'] : '';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulukkal Lottery - Spin to Win</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            color: #2d3748;
            font-size: 2.5em;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #718096;
            font-size: 0.95em;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .wheel-container {
            position: relative;
            width: 400px;
            height: 400px;
            margin: 0 auto 30px;
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
            border-top: 25px solid #667eea;
            z-index: 10;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        canvas {
            filter: drop-shadow(0 15px 35px rgba(0, 0, 0, 0.2));
            transition: filter 0.3s ease;
        }

        canvas:hover {
            filter: drop-shadow(0 20px 45px rgba(102, 126, 234, 0.4));
        }

        .controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
        }

        .spin-btn {
            padding: 16px 50px;
            font-size: 18px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .spin-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.5);
        }

        .spin-btn:active:not(:disabled) {
            transform: translateY(-1px);
        }

        .spin-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .reset-btn {
            padding: 16px 30px;
            font-size: 16px;
            font-weight: 600;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .reset-btn:hover {
            background: rgba(102, 126, 234, 0.2);
        }

        .result-box {
            margin-top: 30px;
            padding: 25px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 20px;
            border: 2px solid rgba(102, 126, 234, 0.3);
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .winner-text {
            font-size: 1.8em;
            font-weight: 700;
            color: #2d3748;
            display: none;
        }

        .winner-text.show {
            display: block;
            animation: slideInUp 0.6s ease-out;
        }

        .trophy {
            font-size: 3em;
            margin-bottom: 10px;
            animation: bounce 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        audio {
            display: none;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h2 {
                font-size: 2em;
            }

            .wheel-container {
                width: 300px;
                height: 300px;
            }

            canvas {
                width: 300px;
                height: 300px;
            }

            .spin-btn {
                padding: 14px 40px;
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>🎰Open Auction (Kulukkal)</h2>
        <p class="subtitle">SPIN THE WHEEL • TRY YOUR LUCK</p>

        <div class="wheel-container">
            <div class="pointer"></div>
            <canvas id="wheel" width="400" height="400"></canvas>
        </div>

        <div class="controls">
            <button class="spin-btn" id="spinBtn" onclick="spin()">SPIN NOW</button>
            <button class="reset-btn" onclick="resetWheel()">RESET</button>
        </div>

        <div class="result-box">
            <div>
                <div class="trophy" id="trophy" style="display: none;">🏆</div>
                <div class="winner-text" id="winner"></div>
            </div>
        </div>
    </div>

    <audio id="spinSound" src="../../assets/sounds/spin.mp3"></audio>
    <audio id="winSound" src="../../assets/sounds/win.mp3"></audio>

    <script>
        const auctionId = <?= $auctionId ?>;
        const isCompleted = <?= json_encode($isCompleted) ?>;
        const savedWinnerName = <?= json_encode($winnerName) ?>;

        const canvas = document.getElementById('wheel');
        const ctx = canvas.getContext('2d');
        const spinBtn = document.getElementById('spinBtn');

        let members = [];
        let angle = 0;
        let spinning = false;

        // Initial setup
        if (isCompleted) {
            document.querySelector('.controls').style.display = 'none';
            document.getElementById('trophy').style.display = 'block';
            document.getElementById('winner').innerText = savedWinnerName;
            document.getElementById('winner').classList.add('show');
        }

        fetch(`ajax/get_kulukkal_members.php?auction_id=${auctionId}`)
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

            ctx.clearRect(0, 0, 400, 400);

            members.forEach((m, i) => {
                const startAngle = startOffset + i * slice;
                const endAngle = startOffset + (i + 1) * slice;

                ctx.beginPath();
                ctx.moveTo(200, 200);
                ctx.arc(200, 200, 190, startAngle, endAngle);
                ctx.fillStyle = colors[i % colors.length];
                ctx.fill();

                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 3;
                ctx.stroke();

                ctx.save();
                ctx.translate(200, 200);
                ctx.rotate(startAngle + slice / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 14px Segoe UI';
                ctx.fillText(m.full_name, 160, 5);
                ctx.restore();
            });

            // Center circle
            ctx.beginPath();
            ctx.arc(200, 200, 25, 0, 2 * Math.PI);
            ctx.fillStyle = '#fff';
            ctx.fill();
            ctx.strokeStyle = '#667eea';
            ctx.lineWidth = 3;
            ctx.stroke();
        }

        function spin() {
            if (spinning || members.length === 0) return;
            spinning = true;
            spinBtn.disabled = true;

            document.getElementById('spinSound').play();
            document.getElementById('winner').classList.remove('show');
            document.getElementById('trophy').style.display = 'none';

            // Pre-select a random winner
            const winnerIndex = Math.floor(Math.random() * members.length);
            const winner = members[winnerIndex];

            const numSegments = members.length;
            const slice = (2 * Math.PI) / numSegments;

            // Now that segments are drawn starting from top (12 o'clock):
            // Segment 0 is at the top, segment 1 is clockwise from there, etc.
            // The pointer is at the top.
            // To land segment i under the pointer, we need to rotate by -(i * slice + slice/2)
            // Then add full rotations to spin multiple times

            // Segment center offset from top = i * slice + slice/2
            const segmentCenterOffset = winnerIndex * slice + slice / 2;

            // We rotate the wheel, so to bring segment i to top, rotate by negative offset
            // We want full rotations (integer multiples of 2PI) minus the offset
            // This ensures we always land at -segmentCenterOffset (which is top for that segment)
            const numRotations = 5 + Math.floor(Math.random() * 4); // 5 to 8 full spins
            const extraSpins = numRotations * 2 * Math.PI;

            const totalAngle = extraSpins - segmentCenterOffset;

            const duration = 4000 + Math.random() * 1000;
            const start = performance.now();

            function animate(t) {
                const progress = Math.min((t - start) / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                angle = easeProgress * totalAngle;

                ctx.clearRect(0, 0, 400, 400);
                ctx.save();
                ctx.translate(200, 200);
                ctx.rotate(angle);
                ctx.translate(-200, -200);
                drawWheel();
                ctx.restore();

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    showWinner(winner);
                }
            }
            requestAnimationFrame(animate);
        }

        function showWinner(winner) {
            document.getElementById('winSound').play();

            document.getElementById('trophy').style.display = 'block';
            document.getElementById('winner').innerText = `${winner.full_name}`;
            document.getElementById('winner').classList.add('show');
            
            // Hide controls to prevent re-spin
            document.querySelector('.controls').style.display = 'none';

            fetch('ajax/save_kulukkal_winner.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `auction_id=${auctionId}&member_id=${winner.member_id}`
            }).catch(err => console.error('Failed to save winner:', err));

            spinning = false;
        }

        function resetWheel() {
            if (isCompleted) return;
            angle = 0;
            spinning = false;
            spinBtn.disabled = false;
            document.getElementById('winner').classList.remove('show');
            document.getElementById('trophy').style.display = 'none';
            drawWheel();
        }
    </script>
    <!-- <script>
        const auctionId = <?= $auctionId ?>;
        const canvas = document.getElementById('wheel');
        const ctx = canvas.getContext('2d');
        
        let members = [];
        let angle = 0;
        let spinning = false;
        
        fetch(`ajax/get_kulukkal_members.php?auction_id=${auctionId}`)
        .then(r => r.json())
        .then(data => {
            members = data;
            drawWheel();
            });
            
            function drawWheel() {
            const slice = 2 * Math.PI / members.length;
            members.forEach((m, i) => {
                ctx.beginPath();
                ctx.moveTo(200, 200);
                ctx.arc(200, 200, 200, i * slice, (i + 1) * slice);
                ctx.fillStyle = i % 2 ? '#fde047' : '#60a5fa';
                ctx.fill();
                ctx.save();
                ctx.translate(200, 200);
                ctx.rotate(i * slice + slice / 2);
                ctx.fillStyle = '#000';
                ctx.fillText(m.full_name, 70, 10);
                ctx.restore();
            });
        }

        function spin() {
            if (spinning) return;
            spinning = true;
            
            document.getElementById('spinSound').play();
            
            const spins = Math.random() * 3000 + 3000;
            const start = performance.now();

            function animate(t) {
                const progress = Math.min((t - start) / spins, 1);
                angle = progress * 10 * Math.PI;
                ctx.clearRect(0, 0, 400, 400);
                ctx.save();
                ctx.translate(200, 200);
                ctx.rotate(angle);
                ctx.translate(-200, -200);
                drawWheel();
                ctx.restore();
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    pickWinner();
                }
            }
            requestAnimationFrame(animate);
        }
        
        function pickWinner() {
            document.getElementById('winSound').play();
            const slice = 2 * Math.PI / members.length;
            const index = Math.floor(((2 * Math.PI - angle % (2 * Math.PI)) / slice)) % members.length;
            const winner = members[index];
            
            document.getElementById('winner').innerText =
            `🏆 Winner: ${winner.full_name}`;

            fetch('ajax/save_kulukkal_winner.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `auction_id=${auctionId}&member_id=${winner.member_id}`
            });
        }
    </script> -->
</body>

</html>

<!-- <div class="center">
    <h2>🎰 Open Auction (Kulukkal)</h2>
    <canvas id="wheel" width="400" height="400"></canvas>
    <br>
    <button class="spin-btn" onclick="spin()">SPIN</button>
    <h3 id="winner"></h3>
</div> -->