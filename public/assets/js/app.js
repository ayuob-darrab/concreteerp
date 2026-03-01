/**
 * ConcreteERP - Main JavaScript
 * Sidebar, Dark Mode, Notifications
 */

(function () {
    "use strict";

    // ==========================================================================
    // DOM Elements
    // ==========================================================================
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarClose = document.getElementById("sidebarClose");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    const mainContent = document.getElementById("mainContent");
    const darkModeToggle = document.getElementById("darkModeToggle");
    const loadingOverlay = document.getElementById("loadingOverlay");

    // ==========================================================================
    // Sidebar Functions
    // ==========================================================================
    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add("show");
            sidebarOverlay.classList.add("show");
            document.body.style.overflow = "hidden";
        }
    }

    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove("show");
            sidebarOverlay.classList.remove("show");
            document.body.style.overflow = "";
        }
    }

    // Sidebar Toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", openSidebar);
    }

    if (sidebarClose) {
        sidebarClose.addEventListener("click", closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebar);
    }

    // Close sidebar on ESC key
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeSidebar();
        }
    });

    // ==========================================================================
    // Dark Mode
    // ==========================================================================
    const html = document.documentElement;
    const savedTheme = localStorage.getItem("theme") || "light";
    html.setAttribute("data-theme", savedTheme);
    updateDarkModeIcon(savedTheme);

    if (darkModeToggle) {
        darkModeToggle.addEventListener("click", function () {
            const currentTheme = html.getAttribute("data-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";

            html.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            updateDarkModeIcon(newTheme);
        });
    }

    function updateDarkModeIcon(theme) {
        if (darkModeToggle) {
            const icon = darkModeToggle.querySelector("i");
            if (icon) {
                icon.className =
                    theme === "dark" ? "fas fa-sun" : "fas fa-moon";
            }
        }
    }

    // ==========================================================================
    // Loading Overlay
    // ==========================================================================
    window.showLoading = function () {
        if (loadingOverlay) {
            loadingOverlay.classList.add("show");
        }
    };

    window.hideLoading = function () {
        if (loadingOverlay) {
            loadingOverlay.classList.remove("show");
        }
    };

    // ==========================================================================
    // Toast Notifications
    // ==========================================================================
    window.showToast = function (message, type = "success", duration = 3000) {
        const container = document.getElementById("toastContainer");
        if (!container) return;

        const icons = {
            success: "check-circle",
            error: "times-circle",
            warning: "exclamation-triangle",
            info: "info-circle",
        };

        const toastId = "toast-" + Date.now();
        const toast = document.createElement("div");
        toast.id = toastId;
        toast.className = `toast toast-${type} show`;
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="toast-header">
                <i class="fas fa-${
                    icons[type] || icons.info
                } me-2 text-${type}"></i>
                <strong class="me-auto">${
                    type === "success"
                        ? "نجاح"
                        : type === "error"
                        ? "خطأ"
                        : "تنبيه"
                }</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>
        `;

        container.appendChild(toast);

        // Auto dismiss
        setTimeout(function () {
            const toastEl = document.getElementById(toastId);
            if (toastEl) {
                toastEl.classList.remove("show");
                setTimeout(() => toastEl.remove(), 300);
            }
        }, duration);
    };

    // ==========================================================================
    // Confirm Modal
    // ==========================================================================
    window.confirmAction = function (message, callback) {
        const modal = document.getElementById("confirmModal");
        if (!modal) return;

        const bsModal = new bootstrap.Modal(modal);
        document.getElementById("confirmMessage").textContent = message;

        const confirmBtn = document.getElementById("confirmButton");
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener("click", function () {
            callback();
            bsModal.hide();
        });

        bsModal.show();
    };

    // ==========================================================================
    // Notifications
    // ==========================================================================
    function loadNotifications() {
        const notificationList = document.getElementById("notificationList");
        const notificationCount = document.getElementById("notificationCount");

        if (!notificationList) return;

        fetch("/notifications/dropdown")
            .then((response) => response.json())
            .then((data) => {
                if (data.count > 0) {
                    notificationCount.textContent =
                        data.count > 99 ? "99+" : data.count;
                    notificationCount.style.display = "inline-block";
                } else {
                    notificationCount.style.display = "none";
                }

                if (data.notifications && data.notifications.length > 0) {
                    notificationList.innerHTML = data.notifications
                        .map(
                            (n) => `
                        <div class="notification-item ${
                            n.read_at ? "" : "unread"
                        }">
                            <div class="d-flex">
                                <div class="notification-icon me-2">
                                    <i class="fas fa-${n.icon || "bell"}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">${n.message}</p>
                                    <small class="text-muted">${
                                        n.time_ago
                                    }</small>
                                </div>
                            </div>
                        </div>
                    `
                        )
                        .join("");
                } else {
                    notificationList.innerHTML = `
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-bell-slash"></i>
                            <p class="mb-0 small">لا توجد إشعارات جديدة</p>
                        </div>
                    `;
                }
            })
            .catch((err) => console.log("Failed to load notifications"));
    }

    // Load notifications initially and refresh every 30 seconds
    loadNotifications();
    setInterval(loadNotifications, 30000);

    // ==========================================================================
    // Form Validation
    // ==========================================================================
    const forms = document.querySelectorAll(".needs-validation");
    Array.from(forms).forEach(function (form) {
        form.addEventListener(
            "submit",
            function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            },
            false
        );
    });

    // ==========================================================================
    // AJAX Setup
    // ==========================================================================
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        // Setup AJAX CSRF token
        if (window.jQuery) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
                },
            });
        }
    }

    // ==========================================================================
    // Delete Confirmation
    // ==========================================================================
    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-delete")) {
            e.preventDefault();
            const btn = e.target.closest(".btn-delete");
            const form = btn.closest("form");

            confirmAction(
                "هل أنت متأكد من الحذف؟ هذا الإجراء لا يمكن التراجع عنه.",
                function () {
                    if (form) {
                        form.submit();
                    }
                }
            );
        }
    });

    // ==========================================================================
    // Auto-dismiss Alerts
    // ==========================================================================
    const alerts = document.querySelectorAll(".alert-dismissible");
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
})();
