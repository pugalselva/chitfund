
// pagination buttons disable style chitgroups index.php
document.addEventListener('DOMContentLoaded', () => {

        const tableBody = document.getElementById('groupTable');
        const perPageEl = document.getElementById('perPage');
        const searchBox = document.getElementById('searchBox');
        const groupFilter = document.getElementById('groupFilter');
        const pagination = document.getElementById('pagination');

        let rows = Array.from(tableBody.querySelectorAll('tr'));

        /* 🔐 Restore state */
        let currentPage = parseInt(new URLSearchParams(location.search).get('page')) || 1;
        let perPage = localStorage.getItem('perPage') || perPageEl.value;
        perPageEl.value = perPage;

        function paginate() {
            const search = searchBox.value.toLowerCase();
            const group = groupFilter.value;

            const filtered = rows.filter(row => {
                const text = row.innerText.toLowerCase();
                const groupName = row.children[1].innerText;
                return text.includes(search) && (!group || groupName.includes(group));
            });

            const totalPages = Math.ceil(filtered.length / perPage);
            currentPage = Math.min(currentPage, totalPages || 1);

            rows.forEach(r => r.style.display = 'none');

            filtered
                .slice((currentPage - 1) * perPage, currentPage * perPage)
                .forEach(r => r.style.display = '');

            renderPagination(totalPages);
            updateURL();
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


        function updateURL() {
            const params = new URLSearchParams();
            params.set('page', currentPage);
            params.set('perPage', perPage);
            history.replaceState(null, '', '?' + params.toString());
        }

        /* EVENTS */
        perPageEl.onchange = () => {
            perPage = perPageEl.value;
            localStorage.setItem('perPage', perPage);
            currentPage = 1;
            paginate();
        };

        searchBox.onkeyup = () => {
            currentPage = 1;
            paginate();
        };

        groupFilter.onchange = () => {
            currentPage = 1;
            paginate();
        };

        paginate();
    });
