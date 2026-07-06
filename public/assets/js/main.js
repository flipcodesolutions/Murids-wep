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
