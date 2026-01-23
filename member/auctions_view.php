<?php
include 'auth.php';
include '../config/database.php';

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
    g.group_name,
    g.auction_type
FROM auctions a
JOIN chit_groups g ON g.id = a.chit_group_id
JOIN chit_group_members gm ON gm.group_id = g.id
WHERE gm.member_id = ?
AND a.status IN ('upcoming','active')
ORDER BY a.auction_datetime ASC
");
$stmt->bind_param('s', $memberId);
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

    <div class="wrapper" data-server-time="<?= time() * 1000 ?>">
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
                            <?php while ($a = $result->fetch_assoc()): ?>
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
                                        <?php
                                        $now = time();
                                        $start = strtotime($a['auction_datetime']);
                                        $end = strtotime($a['auction_end_datetime']);

                                        // Show buttons based on auction type and status
                                        if ($a['auction_type'] === 'Open') {
                                            // Open Auction (Kulukkal) - show button if active or upcoming
                                            if ($a['status'] === 'active' && $now >= $start && $now <= $end) {
                                                echo '<a href="kulukkal-view.php?auction_id=' . $a['id'] . '" class="btn-primary">🎰 View Kulukkal</a>';
                                            } elseif ($now < $start) {
                                                echo '<small>Upcoming</small>';
                                            } elseif ($a['status'] === 'completed') {
                                                echo '<a href="kulukkal-view.php?auction_id=' . $a['id'] . '" class="btn-secondary">👀 View Result</a>';
                                            } else {
                                                echo '<small>Ended</small>';
                                            }
                                        } else {
                                            // Reverse Auction
                                            if ($a['status'] === 'active' && $now >= $start && $now <= $end) {
                                                echo '<a href="live-auction.php?auction_id=' . $a['id'] . '" class="btn-primary">🔴 Live Auction</a>';
                                            } elseif ($now < $start) {
                                                echo '<small>Upcoming</small>';
                                            } elseif ($a['status'] === 'completed') {
                                                echo '<a href="live-auction.php?auction_id=' . $a['id'] . '" class="btn-secondary">👀 View Result</a>';
                                            } else {
                                                echo '<small>Ended</small>';
                                            }
                                        }
                                        ?>
                                    </td>

                                    <!-- <td>
                                    <?php if ($a['status'] === 'active'): ?>
                                    <a href="live-auction.php?auction_id=<?= $a['id'] ?>" class="btn-primary">
                                        🔴 Live Auction
                                    </a>
                                    <?php else: ?>
                                    <small>Waiting</small>
                                    <?php endif; ?>
                                </td> -->
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
        /* Update countdown immediately, then every 1 second */
        function updateCountdowns() {
            // Get initial server time and calculate offset
            const serverTimeStr = document.querySelector('.wrapper').dataset.serverTime;
            const serverTimeInitial = parseInt(serverTimeStr);
            const clientTimeInitial = Date.now();
            const timeOffset = serverTimeInitial - clientTimeInitial; // Sync offset

            // Function to get current synchronized time
            const getSyncTime = () => Date.now() + timeOffset;

            // We need to keep updating, so we move the logic inside the interval or use a closure
            // Better: redefine the update function to use the closure offset, 
            // but for simplicity, let's just calculate offset once globally if possible, 
            // or just use valid diff inside loop.

            // Actually, let's just make the offset global or recalculate simpler.
            // Let's use a simpler approach: 
            // We'll calculate the "current server time" by adding elapsed time since page load to the initial server time.

            // Let's put the offset logic outside.
        }

        const serverTimeInitial = parseInt(document.querySelector('.wrapper').dataset.serverTime);
        const performanceStart = performance.now();

        function getCurrentServerTime() {
            const elapsed = performance.now() - performanceStart;
            return serverTimeInitial + elapsed;
        }

        function updateCountdowns() {
            const now = getCurrentServerTime();

            document.querySelectorAll('tbody tr').forEach(row => {
                const start = row.dataset.start * 1000;
                const end = row.dataset.end * 1000;

                const cell = row.querySelector('.countdown');
                const statusCell = row.querySelector('.badge');
                // const actionCell = row.querySelector('td:last-child'); // We are not updating action cell via JS dynamically yet, but we could reload or just rely on the reload interval.

                if (now < start) {
                    cell.innerText = 'Starts in ' + format(start - now);
                } else if (now >= start && now <= end) {
                    cell.innerText = 'Ends in ' + format(end - now);
                } else {
                    cell.innerText = 'Ended';
                }
            });
        }

        function format(ms) {
            let s = Math.floor(ms / 1000);
            let d = Math.floor(s / 86400);
            s %= 86400;
            let h = Math.floor(s / 3600);
            s %= 3600;
            let m = Math.floor(s / 60);
            s %= 60;

            if (d > 0) return `${d}d ${h}h ${m}m`;
            return `${h}h ${m}m ${s}s`;
        }

        // Run immediately on page load
        updateCountdowns();

        // Update every 1 second
        setInterval(updateCountdowns, 1000);

        // Auto-refresh page every 2 seconds to check for new auctions/status changes
        setInterval(() => {
            location.reload();
        }, 2000);
    </script>

</body>

</html>