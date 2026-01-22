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
// summary data can be calculated here or in the HTML section
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
        .tabs {
    display: flex;
    gap: 14px;
    margin-bottom: 15px;
}

.tabs span {
    cursor: pointer;
    padding: 6px 14px;
    border-radius: 999px;
    background: #f1f5f9;
    font-size: 13px;
    transition: all 0.25s ease;
}

.tabs span.active {
    background: #2563eb;
    color: #fff;
}

    </style>
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Payment Reports</div>
                    <div class="page-subtitle">Track all payment transactions</div>

                </div>
                <?php include '../layout/header.php'; ?>
            </div>

            <div class="content">
                <!-- SUMMARY -->
                <div class="summary-cards">

                    <div class="summary-card">
                        <small>Total Collected</small>
                        <h2>₹<?= number_format($summary['total_collected'] ?? 0) ?></h2>
                        <small><?= $summary['paid_count'] ?> payments</small>
                    </div>

                    <div class="summary-card">
                        <small>Pending</small>
                        <h2>₹<?= number_format($summary['pending_amount'] ?? 0) ?></h2>
                        <small><?= $summary['pending_count'] ?> payment</small>
                    </div>

                    <div class="summary-card red">
                        <small>Overdue</small>
                        <h2>₹<?= number_format($summary['overdue_amount'] ?? 0) ?></h2>
                        <small><?= $summary['overdue_count'] ?> payment</small>
                    </div>
                </div>
                <a href="create.php">
                    <button class="btn-primary">＋ Record Payment</button>
                </a>
                <div class="table-box">
                    <div class="tabs">
                        <span class="active" onclick="filterPayments('all', this)">
                            All (<?= $summary['paid_count'] + $summary['pending_count'] + $summary['overdue_count'] ?>)
                        </span>
                        <span onclick="filterPayments('paid', this)">Paid (<?= $summary['paid_count'] ?>)</span>
                        <span onclick="filterPayments('pending', this)">Pending
                            (<?= $summary['pending_count'] ?>)</span>
                        <span onclick="filterPayments('overdue', this)">Overdue
                            (<?= $summary['overdue_count'] ?>)</span>
                    </div>
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

                        <select id="memberFilter" class="form-control">
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

                        <input type="text" id="searchBox" class="form-control" placeholder="Search..." />
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Member</th>
                                <th>Group</th>
                                <th>Month</th>
                                <th>Actual</th>
                                <th>Discount</th>
                                <th>Final</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="paymentTableBody">
                            <?php while($p = $payments->fetch_assoc()): ?>
                            <tr data-status="<?= $p['status'] ?>">
                                <td><?= $p['receipt_no'] ?></td>
                                <td><?= $p['full_name'] ?></td>
                                <td><?= $p['group_name'] ?></td>
                                <td>Month <?= $p['month_no'] ?></td>
                                <td>₹<?= $p['actual_amount'] ?></td>
                                <td>-₹<?= $p['discount_amount'] ?></td>
                                <td>₹<?= $p['final_amount'] ?></td>
                                <td>
                                    <span class="badge <?= $p['payment_mode'] ?>">
                                        <?= ucfirst($p['payment_mode']) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge <?= $p['status'] ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>

                                <!-- <td><a href="export_excel.php" class="btn-secondary">
                                    Export Excel
                                </a> -->
                                <td>
                                    <button class="btn-secondary" onclick="openInvoice('<?= $p['receipt_no'] ?>')">
                                        <i class="fa fa-file-invoice"></i> Invoice
                                    </button>
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

</body>

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
                    const groupName = row.children[2].innerText.toLowerCase();
                    const memberName = row.children[1].innerText.toLowerCase();
    
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
                    if (active) b.classList.add('active');
                    b.onclick = () => {
                        currentPage = page;
                        renderTable();
                    };
                    return b;
                };
    
                if (currentPage > 1) {
                    pagination.appendChild(btn('‹ Prev', currentPage - 1));
                }
    
                for (let i = 1; i <= totalPages; i++) {
                    pagination.appendChild(btn(i, i, i === currentPage));
                }
    
                if (currentPage < totalPages) {
                    pagination.appendChild(btn('Next ›', currentPage + 1));
                }
            }
    
            /* =========================
               TAB FILTER (GLOBAL)
            ========================= */
            window.filterPayments = function(status, el) {
                activeStatusFilter = status;
                currentPage = 1;
    
                document.querySelectorAll('.tabs span')
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
    </script>

<script>
    function filterPayments(status, el) {
        // remove active class from all tabs
        document.querySelectorAll('.tabs span').forEach(tab => {
            tab.classList.remove('active');
        });

        // add active to clicked tab
        el.classList.add('active');

        // filter rows
        document.querySelectorAll('tbody tr').forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function openInvoice(receipt) {
        window.open('invoice.php?receipt=' + receipt, '_blank');
    }
</script>

</html>
