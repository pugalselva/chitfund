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
<html>

<head>
    <title>Payment History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
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
        <?php include 'layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Payment Record</div>
                    <div class="page-subtitle">
                        View your payment history and status
                    </div>
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

                <!-- ================= SUMMARY ================= -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="summary-icon icon-paid">✔</div>
                        <div>
                            <div>Paid</div>
                            <b><?= $summaryData['paid_count'] ?></b>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="summary-icon icon-pending">⏳</div>
                        <div>
                            <div>Pending</div>
                            <b><?= $summaryData['pending_count'] ?></b>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="summary-icon icon-overdue">⚠</div>
                        <div>
                            <div>Overdue</div>
                            <b><?= $summaryData['overdue_count'] ?></b>
                        </div>
                    </div>
                </div>
                <div class="table-box">
                    <h3>All Payment</h3>
                    <div class="table-controls" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
                        <select id="groupFilter" class="form-control">
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

                        <input type="text" class="form-control" id="searchBox" placeholder="Search payments..." />
                    </div>

                    <!-- ================= TABLE ================= -->
                    <div class="table-card">
                        <h4>All Payments</h4>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Chit Group</th>
                                    <th>Month</th>
                                    <th>Actual Amount</th>
                                    <th>Discount</th>
                                    <th>Amount Paid</th>
                                    <th>Mode</th>
                                    <th>Due Date</th>
                                    <th>Paid Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody id="paymentTableBody">
                                <?php if ($result->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="10">No payment records found.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php while ($p = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['receipt_no']) ?></td>

                                        <td>
                                            <?= htmlspecialchars($p['group_name']) ?><br>
                                            <small><?= htmlspecialchars($p['group_code']) ?></small>
                                        </td>

                                        <td>Month <?= $p['month_no'] ?></td>

                                        <td>₹<?= number_format($p['actual_amount']) ?></td>

                                        <td style="color:#16a34a;">
                                            <?= $p['discount_amount'] > 0 ? '-₹' . number_format($p['discount_amount']) : '₹0' ?>
                                        </td>

                                        <td>₹<?= number_format($p['final_amount']) ?></td>

                                        <td>
                                            <span class="mode <?= strtolower($p['payment_mode']) ?>">
                                                <?= strtoupper($p['payment_mode']) ?>
                                            </span>
                                        </td>

                                        <td><?= date('d/m/Y', strtotime($p['due_date'])) ?></td>

                                        <td>
                                            <?= $p['payment_date'] ? date('d/m/Y', strtotime($p['payment_date'])) : '-' ?>
                                        </td>

                                        <td>
                                            <span class="badge <?= $p['status'] ?>">
                                                <?= ucfirst($p['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"
                            style="margin-top: 10px; display: flex; align-items: center; justify-content: flex-end;">
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
    </div>
    <!-- script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const tbody = document.getElementById('paymentTableBody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            const searchBox = document.getElementById('searchBox');
            const groupDrop = document.getElementById('groupFilter');
            const perPageEl = document.getElementById('perPage');
            const pagination = document.getElementById('pagination');

            let currentPage = 1;

            /* 🔐 Restore page size */
            let perPage = localStorage.getItem('member_payments_per_page') || perPageEl.value;
            perPageEl.value = perPage;

            function getFilteredRows() {
                const search = searchBox.value.toLowerCase();
                const group = groupDrop.value.toLowerCase();

                return rows.filter(row => {
                    const text = row.innerText.toLowerCase();
                    const groupName = row.children[1].innerText.toLowerCase(); // group column

                    return (
                        text.includes(search) &&
                        (!group || groupName.includes(group))
                    );
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
                localStorage.setItem('member_payments_per_page', perPage);
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