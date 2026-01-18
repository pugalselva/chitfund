<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../index.php');
    exit();
}

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$memberId = $_SESSION['member_id'];

/* Auctions for member's groups */
$stmt = $conn->prepare("
SELECT 
    a.id,
    a.auction_month,
    a.auction_datetime,
    a.auction_end_datetime,
    a.status,
    g.group_name
FROM auctions a
JOIN chit_groups g ON g.id = a.chit_group_id
JOIN chit_group_members gm ON gm.group_id = g.id
WHERE gm.member_id = ?
AND a.status IN ('upcoming','active')
ORDER BY a.auction_datetime ASC
");
$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title>My Auctions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">My Auctions</div>
                    <div class="page-subtitle">Upcoming & Live auctions</div>
                </div>

                <div style="text-align:right;">
                    <b><?= htmlspecialchars($name) ?></b><br>
                    <small><?= htmlspecialchars($email) ?></small><br>

                    <a href="../logout.php" class="btn btn-danger" style="margin-top:6px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>

            <div class="content">
                <div class="table-box">

                    <table>
                        <thead>
                            <tr>
                                <th>Chit Group</th>
                                <th>Month</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Countdown</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while($a = $result->fetch_assoc()): ?>
                            <tr data-start="<?= strtotime($a['auction_datetime']) ?>"
                                data-end="<?= strtotime($a['auction_end_datetime']) ?>">

                                <td><?= htmlspecialchars($a['group_name']) ?></td>
                                <td>Month <?= $a['auction_month'] ?></td>

                                <td><?= date('d M Y H:i', strtotime($a['auction_datetime'])) ?></td>
                                <td><?= date('d M Y H:i', strtotime($a['auction_end_datetime'])) ?></td>

                                <td class="countdown">—</td>
                                <td>
                                    <span class="badge <?= $a['status'] ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($a['status'] === 'active'): ?>
                                    <a href="live-auction.php?auction_id=<?= $a['id'] ?>" class="btn-primary">
                                        🔴 Live Auction
                                    </a>
                                    <?php else: ?>
                                    <small>Waiting</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
    /* Countdown Timer */
    setInterval(() => {
        document.querySelectorAll('tbody tr').forEach(row => {
            const start = row.dataset.start * 1000;
            const end = row.dataset.end * 1000;
            const now = Date.now();
            const cell = row.querySelector('.countdown');

            if (now < start) {
                cell.innerText = 'Starts in ' + format(start - now);
            } else if (now >= start && now <= end) {
                cell.innerText = 'Ends in ' + format(end - now);
            } else {
                cell.innerText = 'Ended';
            }
        });
    }, 1000);

    function format(ms) {
        let s = Math.floor(ms / 1000);
        let h = Math.floor(s / 3600);
        s %= 3600;
        let m = Math.floor(s / 60);
        s %= 60;
        return `${h}h ${m}m ${s}s`;
    }
    </script>

</body>

</html>