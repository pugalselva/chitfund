<?php
// session_start();
include '../../config/database.php';
include 'auto-close.php';
include 'auto-status.php';
include '../auth.php';


// $auctions = $conn->query("
//     SELECT a.*, g.group_name
//     FROM auctions a
//     JOIN chit_groups g ON g.id = a.chit_group_id
//     ORDER BY a.created_at DESC
// ");
$auctions = $conn->query("
    SELECT a.*, g.group_name, g.auction_type
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    ORDER BY a.created_at DESC
");

?>


<!DOCTYPE html>
<html>

<head>
    <title>Upcoming Auctions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Auctions</h4>
                        <small class="text-secondary">Manage scheduled and live auctions</small>
                    </div>
                </div>

                <div class="mb-4 d-flex gap-2">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Auction
                    </a>
                    <!-- <a href="kulukkal_spin.php" class="btn btn-outline-primary">kulukal</a> -->
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">Auction List</h5>

                            <div class="d-flex gap-2">
                                <select id="groupFilter" class="form-select w-auto">
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
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Group</th>
                                        <th>Month</th>
                                        <th>Date & Time</th>
                                        <th>End</th>
                                        <th>Starting Bid</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="auctionTableBody">
                                    <?php while ($a = $auctions->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span
                                                    class="fw-medium text-dark"><?= htmlspecialchars($a['group_name']) ?></span>
                                                <br><small class="text-muted"><?= htmlspecialchars($a['auction_type']) ?>
                                                    Type</small>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">Month
                                                    <?= $a['auction_month'] ?></span></td>
                                            <td>
                                                <div class="d-flex flex-column" style="font-size: 0.9em;">
                                                    <span><?= date('d M Y', strtotime($a['auction_datetime'])) ?></span>
                                                    <small
                                                        class="text-muted"><?= date('h:i A', strtotime($a['auction_datetime'])) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column" style="font-size: 0.9em;">
                                                    <span><?= date('d M Y', strtotime($a['auction_end_datetime'])) ?></span>
                                                    <small
                                                        class="text-muted"><?= date('h:i A', strtotime($a['auction_end_datetime'])) ?></small>
                                                </div>
                                            </td>
                                            <td><span
                                                    class="fw-bold text-success">₹<?= number_format($a['starting_bid_amount']) ?></span>
                                            </td>

                                            <td>
                                                <?php
                                                $statusClass = match (strtolower($a['status'])) {
                                                    'active' => 'bg-success',
                                                    'upcoming' => 'bg-warning text-dark',
                                                    'completed' => 'bg-secondary',
                                                    default => 'bg-light text-dark'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= ucfirst($a['status']) ?>
                                                </span>

                                                <?php if ($a['status'] !== 'completed'): ?>
                                                    <div class="mt-1">
                                                        <a href="javascript:void(0)"
                                                            onclick="closeAuction(<?= (int) $a['id'] ?>)"
                                                            class="text-danger small text-decoration-none">
                                                            <i class="fas fa-times-circle"></i> Close
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="all_bidding_view.php?auction_id=<?= $a['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>

                                                    <?php if ($a['auction_type'] === 'Open' && $a['status'] !== 'completed'): ?>
                                                        <a href="kulukkal_spin.php?auction_id=<?= $a['id'] ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-dice"></i> Kulukkal
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <small>Show per page:</small>
                                <select id="perPage" class="form-select form-select-sm w-auto">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                </select>
                            </div>
                            <div id="pagination" class="btn-group"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
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
                    const groupNameCol = row.querySelector('td:nth-child(1) span');
                    const groupName = groupNameCol ? groupNameCol.innerText.toLowerCase() : '';

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
                    btn.className = 'btn btn-outline-secondary btn-sm'; // Bootstrap button

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
                    pagination.appendChild(createBtn('‹', currentPage - 1));
                }

                const range = 1; // pages around current
                let start = Math.max(2, currentPage - range);
                let end = Math.min(totalPages - 1, currentPage + range);

                /* FIRST PAGE */
                pagination.appendChild(createBtn(1, 1, currentPage === 1));

                /* LEFT ELLIPSIS */
                if (start > 2) {
                    pagination.appendChild(createBtn('...', 0, false, true));
                }

                /* MIDDLE PAGES */
                for (let i = start; i <= end; i++) {
                    pagination.appendChild(createBtn(i, i, i === currentPage));
                }

                /* RIGHT ELLIPSIS */
                if (end < totalPages - 1) {
                    pagination.appendChild(createBtn('...', 0, false, true));
                }

                /* LAST PAGE */
                if (totalPages > 1) {
                    pagination.appendChild(
                        createBtn(totalPages, totalPages, currentPage === totalPages)
                    );
                }

                /* NEXT */
                if (currentPage < totalPages) {
                    pagination.appendChild(createBtn('›', currentPage + 1));
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