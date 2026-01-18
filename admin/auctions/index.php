<?php
// session_start();
include '../../config/database.php';
include 'auto-close.php';
include 'auto-status.php';
include '../auth.php';

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../../index.php");
//     exit;
// }

$auctions = $conn->query("
    SELECT a.*, g.group_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    ORDER BY a.created_at DESC
");
?>


<!DOCTYPE html>
<html>

<head>
    <title>Upcoming Auctions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .pagination button {
            padding: 6px 10px;
            margin: 0 3px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
            border-radius: 6px;
        }

        .pagination button.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Upcoming Auctions</div>
                    <div class="page-subtitle">Scheduled auctions for chit groups</div>
                </div>
                <?php include '../layout/header.php'; ?>

            </div>

            <div class="content">

                <a href="create.php"><button class="btn-primary">＋ Create Auction</button></a>
                <a href="all_bidding_view.php"><button class="btn-primary">Live Auction</button></a>

                <div class="table-box">
                    <!-- <h3>All Groups (<?= $count ?>)</h3> -->
                    <div class="table-controls" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
                        <select id="groupFilter" class="form-control">
                            <option value="">All Groups</option>
                            <?php
                            $groups = $conn->query("SELECT DISTINCT group_name FROM chit_groups ORDER BY group_name");
                            while ($g = $groups->fetch_assoc()):
                            ?>
                            <option value="<?= htmlspecialchars($g['group_name']) ?>">
                                <?= htmlspecialchars($g['group_name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>

                        <input type="text" id="searchBox" class="form-control" placeholder="Search..." />
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Month</th>
                                <th>Date & Time</th>
                                <th>End</th>
                                <th>Starting Bid</th>
                                <th>Status</th>
                                <th>Auctions</th>
                            </tr>
                        </thead>
                        <tbody id="auctionTableBody">
                            <?php while ($a = $auctions->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['group_name']) ?></td>
                                <td><?= $a['auction_month'] ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($a['auction_datetime'])) ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($a['auction_end_datetime'])) ?></td>
                                <td>₹<?= number_format($a['starting_bid_amount']) ?></td>

                                <!-- <td><?= ucfirst($a['status']) ?></td> -->
                                <td>
                                    <?= ucfirst($a['status']) ?>

                                    <?php if ($a['status'] !== 'completed'): ?>
                                    <button class="btn-danger" onclick="closeAuction(<?= (int) $a['id'] ?>)">
                                        Close
                                    </button>
                                    <?php endif; ?>

                                </td>
                                <td>
                                    <a href="all_bidding_view.php?auction_id=<?= $a['id'] ?>">
                                        <button class="btn-primary">Live View</button>
                                    </a>

                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div class="pagination-wrapper"
                        style="margin-top: 10px;display:flex;align-items:center;justify-content:flex-end;">
                        <div class="pagination" id="pagination" style="margin-right: 100px;"></div>
                        <label for=""
                            style="margin-top: 4px; margin-right: 10px; color: #333; font-weight: bold;">Show
                            per page </label>
                        <select id="perPage"
                            style="margin-left: 10px; padding: 4px 8px; border-radius: 4px; border: 0.5px solid #ccc; font-size: 14px;">

                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- script -->
    <script>
        function closeAuction(id) {
            if (!confirm('Mark this auction as completed?')) return;

            fetch('close.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        auction_id: id
                    })
                })
                .then(r => r.text())
                .then(res => {
                    if (res === 'success') location.reload();
                    else alert(res);
                });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const tbody = document.getElementById('auctionTableBody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const searchBox = document.getElementById('searchBox');
            const groupDrop = document.getElementById('groupFilter');
            const perPageEl = document.getElementById('perPage');
            const pagination = document.getElementById('pagination');

            let currentPage = 1;

            /* 🔐 Restore page size */
            let perPage = localStorage.getItem('auction_per_page') || perPageEl.value;
            perPageEl.value = perPage;

            function getFilteredRows() {
                const search = searchBox.value.toLowerCase();
                const group = groupDrop.value.toLowerCase();

                return rows.filter(row => {
                    const text = row.innerText.toLowerCase();
                    const groupName = row.children[0].innerText.toLowerCase(); // Group column
                    return text.includes(search) && (!group || groupName.includes(group));
                });
            }

            function renderTable() {
                const filtered = getFilteredRows();
                const totalPages = Math.ceil(filtered.length / perPage) || 1;

                currentPage = Math.min(currentPage, totalPages);

                rows.forEach(r => r.style.display = 'none');

                filtered
                    .slice((currentPage - 1) * perPage, currentPage * perPage)
                    .forEach(r => r.style.display = '');

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                pagination.innerHTML = '';

                const createBtn = (label, page, active = false, disabled = false) => {
                    const btn = document.createElement('button');
                    btn.textContent = label;

                    if (active) btn.classList.add('active');
                    if (disabled) btn.disabled = true;

                    btn.onclick = () => {
                        if (!disabled) {
                            currentPage = page;
                            renderTable();
                        }
                    };
                    return btn;
                };

                /* PREV */
                if (currentPage > 1) {
                    pagination.appendChild(createBtn('‹ Prev', currentPage - 1));
                }

                const range = 1; // pages around current
                let start = Math.max(2, currentPage - range);
                let end = Math.min(totalPages - 1, currentPage + range);

                /* FIRST PAGE */
                pagination.appendChild(createBtn(1, 1, currentPage === 1));

                /* LEFT ELLIPSIS */
                if (start > 2) {
                    pagination.appendChild(createBtn('…', 0, false, true));
                }

                /* MIDDLE PAGES */
                for (let i = start; i <= end; i++) {
                    pagination.appendChild(createBtn(i, i, i === currentPage));
                }

                /* RIGHT ELLIPSIS */
                if (end < totalPages - 1) {
                    pagination.appendChild(createBtn('…', 0, false, true));
                }

                /* LAST PAGE */
                if (totalPages > 1) {
                    pagination.appendChild(
                        createBtn(totalPages, totalPages, currentPage === totalPages)
                    );
                }

                /* NEXT */
                if (currentPage < totalPages) {
                    pagination.appendChild(createBtn('Next ›', currentPage + 1));
                }
            }



            /* EVENTS */
            perPageEl.onchange = () => {
                perPage = perPageEl.value;
                localStorage.setItem('auction_per_page', perPage);
                currentPage = 1;
                renderTable();
            };

            searchBox.onkeyup = () => {
                currentPage = 1;
                renderTable();
            };

            groupDrop.onchange = () => {
                currentPage = 1;
                renderTable();
            };

            renderTable(); // init
        });
    </script>

</body>

</html>
