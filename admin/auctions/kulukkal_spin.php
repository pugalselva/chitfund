<?php
include '../auth.php';
$auctionId = (int)$_GET['auction_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kulukkal Lottery</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        canvas { display:block; margin:20px auto; }
        .center { text-align:center; }
        .spin-btn {
            padding:12px 24px;
            font-size:16px;
            background:#2563eb;
            color:#fff;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }
    </style>
</head>
<body>

<div class="center">
    <h2>🎰 Open Auction (Kulukkal)</h2>
    <canvas id="wheel" width="400" height="400"></canvas>
    <br>
    <button class="spin-btn" onclick="spin()">SPIN</button>
    <h3 id="winner"></h3>
</div>

<audio id="spinSound" src="../../assets/sounds/spin.mp3"></audio>
<audio id="winSound" src="../../assets/sounds/win.mp3"></audio>

<script>
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
        ctx.moveTo(200,200);
        ctx.arc(200,200,200, i*slice, (i+1)*slice);
        ctx.fillStyle = i % 2 ? '#fde047' : '#60a5fa';
        ctx.fill();
        ctx.save();
        ctx.translate(200,200);
        ctx.rotate(i*slice + slice/2);
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
        ctx.clearRect(0,0,400,400);
        ctx.save();
        ctx.translate(200,200);
        ctx.rotate(angle);
        ctx.translate(-200,-200);
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
    const index = Math.floor(((2*Math.PI - angle % (2*Math.PI)) / slice)) % members.length;
    const winner = members[index];

    document.getElementById('winner').innerText =
        `🏆 Winner: ${winner.full_name}`;

    fetch('ajax/save_kulukkal_winner.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`auction_id=${auctionId}&member_id=${winner.member_id}`
    });
}
</script>
</body>
</html>
