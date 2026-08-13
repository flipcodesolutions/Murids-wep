/**
 * Murids Admin Panel - Main JavaScript
 */

document.addEventListener("DOMContentLoaded", function () {
    initSidebar();
    initResponsiveTables();
    initTableSearch();
    initFormValidation();
    initLoginForm();
});

/* ---- Sidebar Toggle (Mobile) ---- */
function initSidebar() {
    const toggle = document.getElementById("sidebarToggle");
    const closeBtn = document.getElementById("sidebarClose");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (!sidebar) return;

    function openSidebar() {
        sidebar.classList.add("show");
        if (overlay) overlay.classList.add("show");
        document.body.classList.add("sidebar-open");
    }

    function closeSidebar() {
        sidebar.classList.remove("show");
        if (overlay) overlay.classList.remove("show");
        document.body.classList.remove("sidebar-open");
    }

    if (toggle) {
        toggle.addEventListener("click", function () {
            if (sidebar.classList.contains("show")) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener("click", closeSidebar);
    }

    document
        .querySelectorAll(".sidebar-nav .nav-link")
        .forEach(function (link) {
            link.addEventListener("click", function () {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && sidebar.classList.contains("show")) {
            closeSidebar();
        }
    });

    window.addEventListener("resize", function () {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });
}

/* ---- Auto data-label for mobile table cards ---- */
function initResponsiveTables() {
    document.querySelectorAll(".table-stack-mobile").forEach(function (table) {
        const headers = Array.from(table.querySelectorAll("thead th")).map(
            function (th) {
                return th.textContent.trim();
            },
        );

        table.querySelectorAll("tbody tr").forEach(function (row) {
            row.querySelectorAll("td").forEach(function (td, index) {
                if (headers[index]) {
                    td.setAttribute("data-label", headers[index]);
                }
            });
        });
    });
}

/* ---- Table Search Filter ---- */
function initTableSearch() {
    const searchInput = document.getElementById("tableSearch");
    const table = document.getElementById("dataTable");

    if (!searchInput || !table) return;

    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? "" : "none";
        });
    });
}

/* ---- Form Validation ---- */
function initFormValidation() {
    const form = document.getElementById("adminForm");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add("was-validated");
            return;
        }

        const toast = document.getElementById("formToast");
        if (toast) {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        form.classList.add("was-validated");
    });
}

/* ---- Login Form ---- */
function initLoginForm() {
    const loginForm = document.getElementById("loginForm");

    if (!loginForm) return;

    loginForm.addEventListener("submit", function (e) {
        if (!loginForm.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }

        loginForm.classList.add("was-validated");
    });
}

/* ---- Delete Row Confirmation ---- */
function confirmDelete(btn) {
    if (confirm("Are you sure you want to delete this record?")) {
        const row = btn.closest("tr");
        if (row) {
            row.style.transition = "opacity 0.3s ease";
            row.style.opacity = "0";
            setTimeout(function () {
                row.remove();
            }, 300);
        }
    }
}

/* ---- Global Pagination Helper ---- */
function renderPagination(data, wrapperId, infoId, onPageChange) {
    const wrapper = typeof wrapperId === "string" ? document.getElementById(wrapperId) : wrapperId;
    const info = typeof infoId === "string" ? document.getElementById(infoId) : infoId;

    if (!data || !data.total || data.total === 0) {
        if (info) info.textContent = "No records found.";
        if (wrapper) wrapper.innerHTML = "";
        return;
    }

    if (info) {
        info.textContent = `Showing ${data.from} to ${data.to} of ${data.total} entries`;
    }

    // Only render pagination buttons when needed (last_page > 1)
    if (!wrapper) return;
    if (data.last_page <= 1) {
        wrapper.innerHTML = "";
        return;
    }

    const current = data.current_page;
    const last = data.last_page;

    let html = `<div class="pagination-group">`;

    // Previous Page Button
    html += `
        <button type="button" class="page-btn nav-btn" data-page="${current - 1}" ${current === 1 ? "disabled" : ""}>
            <i class="bi bi-chevron-left"></i>
        </button>
    `;

    // Page Numbers
    let start = Math.max(1, current - 2);
    let end = Math.min(last, current + 2);

    if (start > 1) {
        html += `<button type="button" class="page-btn" data-page="1">1</button>`;
        if (start > 2) html += `<span class="page-dots">...</span>`;
    }

    for (let page = start; page <= end; page++) {
        html += `<button type="button" class="page-btn ${page === current ? "active" : ""}" data-page="${page}">${page}</button>`;
    }

    if (end < last) {
        if (end < last - 1) html += `<span class="page-dots">...</span>`;
        html += `<button type="button" class="page-btn" data-page="${last}">${last}</button>`;
    }

    // Next Page Button
    html += `
        <button type="button" class="page-btn nav-btn" data-page="${current + 1}" ${current === last ? "disabled" : ""}>
            <i class="bi bi-chevron-right"></i>
        </button>
    `;

    html += `</div>`;
    wrapper.innerHTML = html;

    wrapper.querySelectorAll(".page-btn[data-page]").forEach((btn) => {
        btn.addEventListener("click", function () {
            if (this.disabled || this.classList.contains("active")) return;
            const page = parseInt(this.dataset.page, 10);
            if (!isNaN(page) && typeof onPageChange === "function") {
                onPageChange(page);
            }
        });
    });
}

