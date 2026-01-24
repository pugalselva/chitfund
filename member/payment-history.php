<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$memberId = $_SESSION['member_id'];

/* ===============================
   PAYMENT SUMMARY
================================ */
$summary = $conn->prepare("
SELECT
    COUNT(CASE WHEN status='paid' THEN 1 END)    AS paid_count,
    COUNT(CASE WHEN status='pending' THEN 1 END) AS pending_count,
    COUNT(CASE WHEN status='overdue' THEN 1 END) AS overdue_count
FROM payments
WHERE member_id = ?
");
$summary->bind_param('s', $memberId);
$summary->execute();
$summaryData = $summary->get_result()->fetch_assoc();

/* ===============================
   PAYMENT HISTORY
================================ */
$stmt = $conn->prepare("
SELECT 
    p.*,
    g.group_name,
    g.group_code
FROM payments p
JOIN chit_groups g ON g.id = p.chit_group_id
WHERE p.member_id = ?
ORDER BY p.created_at DESC
");
$stmt->bind_param('s', $memberId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .summary-card {
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            color: white;
            transition: transform 0.2s;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .icon-large {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            right: 1rem;
            bottom: 0.5rem;
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-custom tbody tr {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .table-custom td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .table-custom td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .table-custom td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .pagination-container .page-link {
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 4px;
            border: none;
            color: #64748b;
        }

        .pagination-container .page-item.active .page-link {
            background-color: #4338ca;
            color: white;
            box-shadow: 0 4px 6px rgba(67, 56, 202, 0.3);
        }

        .payment-card-mobile {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .mode-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .mode-cash {
            background-color: #ecfccb;
            color: #3f6212;
        }

        .mode-bank {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .mode-upi {
            background-color: #fce7f3;
            color: #9d174d;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex" id="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            <!-- Navbar -->
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-light text-primary me-3 d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">Payment Record</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">View your payment history and status</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($name) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($email) ?></small>
                        </div>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">

                <!-- Summary Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="summary-card bg-gradient-success">
                            <div class="icon-large"><i class="fas fa-check-circle"></i></div>
                            <h6 class="text-white-50 text-uppercase mb-1 fw-bold">Paid</h6>
                            <h2 class="mb-0 fw-bold"><?= $summaryData['paid_count'] ?></h2>
                            <small class="text-white-50">Successful payments</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="summary-card bg-gradient-warning">
                            <div class="icon-large"><i class="fas fa-hourglass-half"></i></div>
                            <h6 class="text-white-50 text-uppercase mb-1 fw-bold">Pending</h6>
                            <h2 class="mb-0 fw-bold"><?= $summaryData['pending_count'] ?></h2>
                            <small class="text-white-50">Review required</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="summary-card bg-gradient-danger">
                            <div class="icon-large"><i class="fas fa-exclamation-triangle"></i></div>
                            <h6 class="text-white-50 text-uppercase mb-1 fw-bold">Overdue</h6>
                            <h2 class="mb-0 fw-bold"><?= $summaryData['overdue_count'] ?></h2>
                            <small class="text-white-50">Action needed</small>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="searchBox"
                                placeholder="Search receipt, month...">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <select id="groupFilter" class="form-select">
                            <option value="">All Groups</option>
                            <?php
                            $groups = $conn->query("
                                SELECT DISTINCT g.group_name
                                FROM payments p
                                JOIN chit_groups g ON g.id = p.chit_group_id
                                WHERE p.member_id = '$memberId'
                                ORDER BY g.group_name
                            ");
                            while ($g = $groups->fetch_assoc()):
                                ?>
                                <option value="<?= htmlspecialchars($g['group_name']) ?>">
                                    <?= htmlspecialchars($g['group_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-auto ms-auto d-flex align-items-center">
                        <label class="me-2 text-muted small fw-bold">Show:</label>
                        <select id="perPage" class="form-select form-select-sm" style="width: 70px;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover">
                            <thead class="text-muted text-uppercase small fw-bold">
                                <tr>
                                    <th>Receipt</th>
                                    <th>Group / Month</th>
                                    <th>Due Date</th>
                                    <th>Actual</th>
                                    <th>Discount</th>
                                    <th>Paid Amount</th>
                                    <th>Mode</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody id="paymentTableBody">
                                <?php if ($result->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No records found</td>
                                    </tr>
                                <?php endif; ?>

                                <?php while ($p = $result->fetch_assoc()):
                                    $modeClass = 'mode-cash';
                                    if (strtolower($p['payment_mode']) == 'bank')
                                        $modeClass = 'mode-bank';
                                    if (strtolower($p['payment_mode']) == 'upi')
                                        $modeClass = 'mode-upi';
                                    ?>
                                    <tr class="align-middle">
                                        <td class="fw-bold text-dark">#<?= htmlspecialchars($p['receipt_no']) ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($p['group_name']) ?></div>
                                            <small class="text-muted">Month <?= $p['month_no'] ?> ·
                                                <?= htmlspecialchars($p['group_code']) ?></small>
                                        </td>
                                        <td><?= date('d M Y', strtotime($p['due_date'])) ?></td>
                                        <td class="text-secondary">₹<?= number_format($p['actual_amount']) ?></td>
                                        <td class="text-success small">-₹<?= number_format($p['discount_amount']) ?></td>
                                        <td class="fw-bold text-dark">₹<?= number_format($p['final_amount']) ?></td>
                                        <td>
                                            <span class="mode-badge <?= $modeClass ?>">
                                                <?= strtoupper($p['payment_mode']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span
                                                class="badge rounded-pill bg-<?= $p['status'] === 'paid' ? 'success' : ($p['status'] === 'pending' ? 'warning' : 'danger') ?> bg-opacity-10 text-<?= $p['status'] === 'paid' ? 'success' : ($p['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($p['status']) ?>
                                            </span>
                                            <div class="small text-muted mt-1">
                                                <?= $p['payment_date'] ? date('d M Y', strtotime($p['payment_date'])) : '' ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none" id="mobileListView">
                    <!-- Populated by JS for filtering support, or we can just duplicate logic here, 
                          but since we have JS search/pagination, it's better to build this via JS or 
                          simplify by letting the JS renderer handle both desktop and mobile views if desired.
                          For simplicity, I will let the JS renderer below handle the visibility toggling 
                          based on rows, but effectively for mobile we need a different markup properly.
                          
                          Actually, easier approach: Re-render rows as cards in JS?
                          Or just output them here hidden on desktop. -->

                    <!-- I'll use JS to clone data into mobile cards to keep search/pagination consistent easily -->
                    <div id="mobileCardsContainer"></div>
                </div>

                <!-- Pagination -->
                <nav class="mt-4 d-flex justify-content-center">
                    <ul class="pagination pagination-container" id="pagination">
                        <!-- JS populated -->
                    </ul>
                </nav>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tbody = document.getElementById('paymentTableBody');

            // Collect data from the server-rendered table for client-side functionality
            // This allows us to keep the PHP rendering simple but add dynamic features
            const originalRows = Array.from(tbody.querySelectorAll('tr'));
            let allData = [];

            // Parse table rows into object array
            originalRows.forEach(row => {
                if (row.cells.length < 8) return; // Skip empty message

                const receipt = row.cells[0].innerText.replace('#', '');
                const groupName = row.cells[1].querySelector('div').innerText;
                const groupCode = row.cells[1].querySelector('small').innerText;
                const dueDate = row.cells[2].innerText;
                const actual = row.cells[3].innerText;
                const discount = row.cells[4].innerText;
                const paid = row.cells[5].innerText;
                const modeHTML = row.cells[6].innerHTML; // html for badge
                const modeText = row.cells[6].innerText;
                const statusHTML = row.cells[7].innerHTML; // html for badge
                const statusText = row.cells[7].querySelector('.badge').innerText;

                allData.push({
                    element: row, // Keep reference to desktop row
                    searchStr: (receipt + ' ' + groupName + ' ' + groupCode + ' ' + statusText + ' ' + modeText).toLowerCase(),
                    groupName: groupName,
                    data: { receipt, groupName, groupCode, dueDate, actual, discount, paid, modeHTML, statusHTML }
                });
            });

            const searchBox = document.getElementById('searchBox');
            const groupDrop = document.getElementById('groupFilter');
            const perPageEl = document.getElementById('perPage');
            const pagination = document.getElementById('pagination');
            const mobileContainer = document.getElementById('mobileCardsContainer');

            let currentPage = 1;
            let perPage = parseInt(localStorage.getItem('member_payments_per_page')) || 5;
            perPageEl.value = perPage;

            function getFilteredData() {
                const search = searchBox.value.toLowerCase();
                const group = groupDrop.value;

                return allData.filter(item => {
                    const matchesSearch = item.searchStr.includes(search);
                    const matchesGroup = !group || item.groupName === group;
                    return matchesSearch && matchesGroup;
                });
            }

            function createMobileCard(item) {
                const d = item.data;
                return `
                    <div class="payment-card-mobile">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                             <div>
                                 <h6 class="fw-bold mb-0 text-dark">${d.groupName}</h6>
                                 <small class="text-muted">#${d.receipt} · ${d.groupCode}</small>
                             </div>
                             ${d.statusHTML}
                        </div>
                        <div class="row g-2 mb-3 small">
                             <div class="col-6 text-muted">Amount Paid</div>
                             <div class="col-6 text-end fw-bold text-dark">${d.paid}</div>
                             
                             <div class="col-6 text-muted">Discount</div>
                             <div class="col-6 text-end text-success">${d.discount}</div>
                             
                             <div class="col-6 text-muted">Due Date</div>
                             <div class="col-6 text-end">${d.dueDate}</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                             <small class="text-muted">Mode</small>
                             ${d.modeHTML}
                        </div>
                    </div>
                `;
            }

            function render() {
                const filtered = getFilteredData();
                const totalPages = Math.ceil(filtered.length / perPage) || 1;

                if (currentPage > totalPages) currentPage = 1;

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                const pageItems = filtered.slice(start, end);

                // Desktop Render
                tbody.innerHTML = '';
                if (pageItems.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No records matching filters</td></tr>';
                } else {
                    pageItems.forEach(item => {
                        tbody.appendChild(item.element.cloneNode(true));
                    });
                }

                // Mobile Render
                mobileContainer.innerHTML = '';
                if (pageItems.length === 0) {
                    mobileContainer.innerHTML = '<div class="text-center py-4 text-muted">No records matching filters</div>';
                } else {
                    pageItems.forEach(item => {
                        mobileContainer.innerHTML += createMobileCard(item);
                    });
                }

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                pagination.innerHTML = '';

                if (totalPages <= 1) return;

                const createItem = (label, page, isActive = false, isDisabled = false) => {
                    const li = document.createElement('li');
                    li.className = `page-item ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}`;

                    const btn = document.createElement('button');
                    btn.className = 'page-link';
                    btn.innerHTML = label;

                    if (!isDisabled) {
                        btn.onclick = () => {
                            currentPage = page;
                            render();
                        };
                    }

                    li.appendChild(btn);
                    return li;
                };

                // Prev
                pagination.appendChild(createItem('<i class="fas fa-chevron-left"></i>', currentPage - 1, false, currentPage === 1));

                // Pages
                let startPage = Math.max(1, currentPage - 1);
                let endPage = Math.min(totalPages, currentPage + 1);

                if (startPage > 1) pagination.appendChild(createItem('1', 1));
                if (startPage > 2) pagination.appendChild(createItem('...', 0, false, true)); // Ellipsis

                for (let i = startPage; i <= endPage; i++) {
                    pagination.appendChild(createItem(i, i, i === currentPage));
                }

                if (endPage < totalPages - 1) pagination.appendChild(createItem('...', 0, false, true));
                if (endPage < totalPages) pagination.appendChild(createItem(totalPages, totalPages));

                // Next
                pagination.appendChild(createItem('<i class="fas fa-chevron-right"></i>', currentPage + 1, false, currentPage === totalPages));
            }

            /* EVENTS */
            perPageEl.onchange = () => {
                perPage = parseInt(perPageEl.value);
                localStorage.setItem('member_payments_per_page', perPage);
                currentPage = 1;
                render();
            };

            searchBox.onkeyup = () => {
                currentPage = 1;
                render();
            };

            groupDrop.onchange = () => {
                currentPage = 1;
                render();
            };

            render(); // init
        });
    </script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>