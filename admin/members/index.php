<?php
include '../../config/database.php';
include '../auth.php';

$result = $conn->query('SELECT * FROM members ORDER BY created_at DESC');
$count = $result->num_rows;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Members</title>
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
                        <h4 class="mb-0">Members</h4>
                        <small class="text-secondary">View all registered members</small>
                    </div>
                </div>

                <div class="mb-4">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Enroll Member
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">All Members (<?= $count ?>)</h5>

                            <div class="d-flex gap-2">
                                <select id="nameFilter" class="form-select w-auto">
                                    <option value="">All Members</option>
                                    <?php
                                    $names = $conn->query("SELECT DISTINCT full_name FROM members ORDER BY full_name");
                                    while ($n = $names->fetch_assoc()):
                                        ?>
                                        <option value="<?= htmlspecialchars($n['full_name']) ?>">
                                            <?= htmlspecialchars($n['full_name']) ?>
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
                                        <th>Member ID</th>
                                        <th>UTR ID</th>
                                        <th>Full Name</th>
                                        <th>Gender</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Joining Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($m = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['member_id']) ?></td>
                                            <td><?= htmlspecialchars($m['utr_id']) ?></td>

                                            <td>
                                                <strong><?= htmlspecialchars($m['full_name']) ?></strong><br>
                                                <small class="text-muted">DOB: <?= htmlspecialchars($m['dob']) ?></small>
                                            </td>

                                            <td><?= htmlspecialchars($m['gender']) ?></td>
                                            <td><?= htmlspecialchars($m['mobile']) ?></td>
                                            <td><?= htmlspecialchars($m['email']) ?></td>
                                            <td><?= date('d M Y', strtotime($m['joining_date'])) ?></td>

                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        <?= $m['is_active'] ? 'checked' : '' ?>
                                                        onchange="toggleStatus('<?= $m['member_id'] ?>', this.checked)">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="edit.php?id=<?= $m['member_id'] ?>"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-pen"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="deleteMember('<?= $m['member_id'] ?>')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    <a href="view.php?id=<?= $m['member_id'] ?>"
                                                        class="btn btn-sm btn-info text-white">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
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
        function deleteMember(memberId) {
            if (!memberId) {
                return;
            }

            if (!confirm('Are you sure you want to delete this member?')) {
                return;
            }

            fetch('delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    member_id: memberId
                })
            })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
                        alert('Member deleted successfully');
                        location.reload();
                    } else {
                        alert(result);
                    }
                });
        }

        function toggleStatus(memberId, status) {
            fetch('toggle-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    member_id: memberId,
                    status: status ? 1 : 0
                })
            })
                .then(r => r.text())
                .then(res => {
                    if (res !== 'success') alert(res);
                });
        }

        document.addEventListener('DOMContentLoaded', () => {

            const tbody = document.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const searchBox = document.getElementById('searchBox');
            const nameFilter = document.getElementById('nameFilter');
            const perPageEl = document.getElementById('perPage');
            const pagination = document.getElementById('pagination');

            let currentPage = 1;

            /* 🔐 Restore page size */
            let perPage = localStorage.getItem('members_per_page') || perPageEl.value;
            perPageEl.value = perPage;

            function filterRows() {
                const search = searchBox.value.toLowerCase();
                const name = nameFilter.value.toLowerCase();

                return rows.filter(row => {
                    const text = row.innerText.toLowerCase();
                    const rowName = row.children[2].innerText.toLowerCase(); // Full Name column
                    return text.includes(search) && (!name || rowName.includes(name));
                });
            }

            function renderTable() {
                const filtered = filterRows();
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
                    btn.className = 'btn btn-outline-secondary btn-sm';

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
                localStorage.setItem('members_per_page', perPage);
                currentPage = 1;
                renderTable();
            };

            searchBox.onkeyup = () => {
                currentPage = 1;
                renderTable();
            };

            nameFilter.onchange = () => {
                currentPage = 1;
                renderTable();
            };

            renderTable(); // init
        });
    </script>
</body>

</html>