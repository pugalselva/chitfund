<?php
include '../../config/database.php';
include '../auth.php';

$payments = $conn->query("
    SELECT p.*, 
           m.full_name, 
           g.group_name
    FROM payments p
    JOIN members m ON m.member_id = p.member_id
    JOIN chit_groups g ON g.id = p.chit_group_id
    ORDER BY p.created_at DESC
");
// summary data
$summary = $conn
    ->query(
        "
    SELECT 
        SUM(CASE WHEN status='paid' THEN final_amount ELSE 0 END) AS total_collected,
        COUNT(CASE WHEN status='paid' THEN 1 END) AS paid_count,

        SUM(CASE WHEN status='pending' THEN final_amount ELSE 0 END) AS pending_amount,
        COUNT(CASE WHEN status='pending' THEN 1 END) AS pending_count,

        SUM(CASE WHEN status='overdue' THEN final_amount ELSE 0 END) AS overdue_amount,
        COUNT(CASE WHEN status='overdue' THEN 1 END) AS overdue_count
    FROM payments
",
    )
    ->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Payment Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .summary-card-modern {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }

        .summary-card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .status-tab {
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 99px;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .status-tab:hover {
            background: #e5e7eb;
        }

        .status-tab.active {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Payments</h4>
                        <small class="text-secondary">Track all Member transactions</small>
                    </div>
                </div>

                <!-- SUMMARY -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="summary-card-modern border-start border-4 border-success">
                            <h6 class="text-muted text-uppercase mb-2"
                                style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Collected</h6>
                            <h2 class="mb-0 fw-bold text-success">
                                ₹<?= number_format($summary['total_collected'] ?? 0) ?></h2>
                            <small class="text-muted"><?= $summary['paid_count'] ?> payments</small>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="summary-card-modern border-start border-4 border-warning">
                            <h6 class="text-muted text-uppercase mb-2"
                                style="font-size: 0.75rem; letter-spacing: 0.05em;">Pending</h6>
                            <h2 class="mb-0 fw-bold text-warning">₹<?= number_format($summary['pending_amount'] ?? 0) ?>
                            </h2>
                            <small class="text-muted"><?= $summary['pending_count'] ?> pending</small>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="summary-card-modern border-start border-4 border-danger">
                            <h6 class="text-muted text-uppercase mb-2"
                                style="font-size: 0.75rem; letter-spacing: 0.05em;">Overdue</h6>
                            <h2 class="mb-0 fw-bold text-danger">₹<?= number_format($summary['overdue_amount'] ?? 0) ?>
                            </h2>
                            <small class="text-muted"><?= $summary['overdue_count'] ?> overdue</small>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Record Payment
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <!-- Tabs -->
                        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
                            <div class="status-tab active" onclick="filterPayments('all', this)">
                                All <span
                                    class="badge bg-white text-dark ms-1"><?= $summary['paid_count'] + $summary['pending_count'] + $summary['overdue_count'] ?></span>
                            </div>
                            <div class="status-tab" onclick="filterPayments('paid', this)">
                                Paid <span class="badge bg-white text-dark ms-1"><?= $summary['paid_count'] ?></span>
                            </div>
                            <div class="status-tab" onclick="filterPayments('pending', this)">
                                Pending <span
                                    class="badge bg-white text-dark ms-1"><?= $summary['pending_count'] ?></span>
                            </div>
                            <div class="status-tab" onclick="filterPayments('overdue', this)">
                                Overdue <span
                                    class="badge bg-white text-dark ms-1"><?= $summary['overdue_count'] ?></span>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-3">
                                <select id="groupFilter" class="form-select">
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
                            </div>
                            <div class="col-12 col-md-3">
                                <select id="memberFilter" class="form-select">
                                    <option value="">All Members</option>
                                    <?php
                                    $members = $conn->query("SELECT DISTINCT full_name FROM members ORDER BY full_name");
                                    while ($m = $members->fetch_assoc()):
                                        ?>
                                        <option value="<?= htmlspecialchars($m['full_name']) ?>">
                                            <?= htmlspecialchars($m['full_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" id="searchBox" class="form-control"
                                    placeholder="Search receipt, member..." />
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt</th>
                                        <th>Member</th>
                                        <th>Group Info</th>
                                        <th>Breakdown</th>
                                        <th>Final Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentTableBody">
                                    <?php while ($p = $payments->fetch_assoc()): ?>
                                        <tr data-status="<?= $p['status'] ?>">
                                            <td><span
                                                    class="badge bg-light text-dark border">#<?= $p['receipt_no'] ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-medium"><?= htmlspecialchars($p['full_name']) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column" style="font-size: 0.85em;">
                                                    <span class="fw-medium"><?= htmlspecialchars($p['group_name']) ?></span>
                                                    <span class="text-muted">Month <?= $p['month_no'] ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column" style="font-size: 0.85em;">
                                                    <span>Actual: ₹<?= $p['actual_amount'] ?></span>
                                                    <span class="text-success">Disc: -₹<?= $p['discount_amount'] ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">₹<?= number_format($p['final_amount']) ?></span>
                                                <br>
                                                <small class="text-muted"><?= ucfirst($p['payment_mode']) ?></small>
                                            </td>

                                            <td>
                                                <?php
                                                $statusClass = match ($p['status']) {
                                                    'paid' => 'bg-success',
                                                    'pending' => 'bg-warning text-dark',
                                                    'overdue' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= ucfirst($p['status']) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    onclick="openInvoice('<?= $p['receipt_no'] ?>')">
                                                    <i class="fas fa-file-invoice"></i> Invoice
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
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
        document.addEventListener('DOMContentLoaded', () => {

            const tbody = document.getElementById('paymentTableBody');
            const allRows = Array.from(tbody.querySelectorAll('tr'));

            const searchBox = document.getElementById('searchBox');
            const groupDrop = document.getElementById('groupFilter');
            const memberDrop = document.getElementById('memberFilter');
            const perPageEl = document.getElementById('perPage');
            const pagination = document.getElementById('pagination');

            /* GLOBAL STATE */
            let activeStatusFilter = 'all';
            let currentPage = 1;
            let perPage = Number(perPageEl.value);

            /* =========================
               CORE FILTER PIPELINE
            ========================= */
            function getFilteredRows() {
                const search = searchBox.value.toLowerCase();
                const group = groupDrop.value.toLowerCase();
                const member = memberDrop.value.toLowerCase();

                return allRows.filter(row => {

                    /* STATUS FILTER */
                    if (activeStatusFilter !== 'all' &&
                        row.dataset.status !== activeStatusFilter) {
                        return false;
                    }

                    const text = row.innerText.toLowerCase();
                    // nth-child indices depend on the new column structure
                    // 1: Receipt, 2: Member, 3: Group, 4: Breakdown, 5: Final, 6: Status, 7: Action
                    const memberName = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
                    const groupName = row.querySelector('td:nth-child(3)').innerText.toLowerCase();

                    return (
                        text.includes(search) &&
                        (!group || groupName.includes(group)) &&
                        (!member || memberName.includes(member))
                    );
                });
            }

            /* =========================
               RENDER TABLE
            ========================= */
            function renderTable() {

                const filtered = getFilteredRows();
                const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
                currentPage = Math.min(currentPage, totalPages);

                /* Clear table */
                allRows.forEach(r => r.style.display = 'none');

                /* Show rows */
                filtered
                    .slice((currentPage - 1) * perPage, currentPage * perPage)
                    .forEach(r => r.style.display = '');

                renderPagination(totalPages);
            }

            /* =========================
               PAGINATION
            ========================= */
            function renderPagination(totalPages) {
                pagination.innerHTML = '';

                const btn = (label, page, active = false) => {
                    const b = document.createElement('button');
                    b.textContent = label;
                    b.className = 'btn btn-outline-secondary btn-sm';
                    if (active) b.classList.add('active');
                    b.onclick = () => {
                        currentPage = page;
                        renderTable();
                    };
                    return b;
                };

                if (currentPage > 1) {
                    pagination.appendChild(btn('‹', currentPage - 1));
                }

                const range = 1;
                let start = Math.max(1, currentPage - range);
                let end = Math.min(totalPages, currentPage + range);

                // Simplified pagination logic for range
                for (let i = 1; i <= totalPages; i++) {
                    if (i == 1 || i == totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                        pagination.appendChild(btn(i, i, i === currentPage));
                    } else if (i == currentPage - 2 || i == currentPage + 2) {
                        // ellipsis handled via CSS usually or separate text node, skipping for simplicity or adding simple text
                        // pagination.appendChild(document.createTextNode('...')); 
                    }
                }

                // Replacing simple loop with the full logic from before:
                pagination.innerHTML = ''; // reset
                if (currentPage > 1) pagination.appendChild(btn('‹', currentPage - 1));

                pagination.appendChild(btn(1, 1, currentPage === 1));
                if (currentPage > 3) {
                    const span = document.createElement('span'); span.className = 'btn btn-sm border-0'; span.innerText = '...';
                    pagination.appendChild(span);
                }

                for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                    pagination.appendChild(btn(i, i, i === currentPage));
                }

                if (currentPage < totalPages - 2) {
                    const span = document.createElement('span'); span.className = 'btn btn-sm border-0'; span.innerText = '...';
                    pagination.appendChild(span);
                }

                if (totalPages > 1) pagination.appendChild(btn(totalPages, totalPages, currentPage === totalPages));

                if (currentPage < totalPages) pagination.appendChild(btn('›', currentPage + 1));
            }

            /* =========================
               TAB FILTER (GLOBAL)
            ========================= */
            window.filterPayments = function (status, el) {
                activeStatusFilter = status;
                currentPage = 1;

                document.querySelectorAll('.status-tab')
                    .forEach(t => t.classList.remove('active'));
                el.classList.add('active');

                renderTable();
            };

            /* =========================
               EVENTS
            ========================= */
            searchBox.oninput = () => {
                currentPage = 1;
                renderTable();
            };

            groupDrop.onchange = memberDrop.onchange = () => {
                currentPage = 1;
                renderTable();
            };

            perPageEl.onchange = () => {
                perPage = Number(perPageEl.value);
                currentPage = 1;
                renderTable();
            };

            /* INIT */
            renderTable();
        });

        function openInvoice(receipt) {
            window.open('invoice.php?receipt=' + receipt, '_blank');
        }
    </script>

</body>

</html>