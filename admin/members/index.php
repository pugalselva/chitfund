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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s ease;
        }

        /* Edit */
        .btn-edit {
            background-color: #f39c12;
        }

        .btn-edit:hover {
            background-color: #d68910;
        }

        /* View */
        .btn-view {
            background-color: #3498db;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }

        /* Auctions */
        .btn-auction {
            background-color: #27ae60;
        }

        .btn-auction:hover {
            background-color: #1e8449;
        }

        .btn-danger {
            background-color: #e74c3c;
        }


        .btn-danger:hover {
            background-color: #c0392b;
        }


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

        #namefilter {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Members</div>
                    <div class="page-subtitle">View all registered members</div>

                </div>
                <?php include '../layout/header.php'; ?>

            </div>

            <div class="content">

                <a href="create.php"><button class="btn-primary">＋ Enroll Member</button></a>
                <div class="table-box">
                    <h3>All Members (<?= $count ?>)</h3>
                    <div class="table-controls" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
                        <select id="nameFilter" class="form-control">
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
                    <table>
                        <thead>
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
                                    <small>DOB: <?= htmlspecialchars($m['dob']) ?></small>
                                </td>

                                <td><?= htmlspecialchars($m['gender']) ?></td>
                                <td><?= htmlspecialchars($m['mobile']) ?></td>
                                <td><?= htmlspecialchars($m['email']) ?></td>
                                <td><?= date('d M Y', strtotime($m['joining_date'])) ?></td>

                                <td>
                                    <label class="switch">
                                        <input type="checkbox" <?= $m['is_active'] ? 'checked' : '' ?>
                                            onchange="toggleStatus('<?= $m['member_id'] ?>', this.checked)">
                                        <span class="slider"></span>
                                    </label>
                                </td>

                                <td class="action-buttons">

                                    <a href="edit.php?id=<?= $m['member_id'] ?>" class="btn btn-edit"> <i
                                            class="fa fa-pen"></i> </a>
                                    <a href="javascript:void(0)" class="btn btn-danger"
                                        onclick="deleteMember('<?= $m['member_id'] ?>')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a href="view.php?id=<?= $m['member_id'] ?>" class="btn btn-view"><i
                                            class="fa fa-eye"></i> </a>
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
        function deleteMember(memberId) {
            if (!memberId) {
                alert('Invalid Member ID');
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
    </script>
    <script>
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
    </script>
    <script>
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
    let end   = Math.min(totalPages - 1, currentPage + range);

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
