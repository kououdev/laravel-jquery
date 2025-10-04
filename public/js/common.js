/**
 * Common Utility Functions
 * Reusable functions for all pages
 */

/**
 * Setup CSRF token for all AJAX requests
 */
function setupCSRFToken() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
}

/**
 * Show loading spinner in element
 * @param {jQuery} element - Element to show loading in
 * @param {boolean} show - Show or hide loading
 */
function toggleLoading(element, show) {
    const spinner = element.find(".spinner-border");
    if (show) {
        spinner.removeClass("d-none");
        element.prop("disabled", true);
    } else {
        spinner.addClass("d-none");
        element.prop("disabled", false);
    }
}

/**
 * Show Bootstrap toast notification
 * @param {string} message - Message to show
 * @param {string} type - Type of toast (success, error, warning, info)
 */
function showToast(message, type = "info") {
    const toastContainer = getOrCreateToastContainer();
    const toastId = "toast_" + Date.now();

    const bgClass =
        {
            success: "bg-success",
            error: "bg-danger",
            warning: "bg-warning",
            info: "bg-info",
        }[type] || "bg-info";

    const iconClass =
        {
            success: "bi-check-circle-fill",
            error: "bi-exclamation-triangle-fill",
            warning: "bi-exclamation-triangle-fill",
            info: "bi-info-circle-fill",
        }[type] || "bi-info-circle-fill";

    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgClass} text-white">
                <i class="bi ${iconClass} me-2"></i>
                <strong class="me-auto">${
                    type.charAt(0).toUpperCase() + type.slice(1)
                }</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    toastContainer.append(toastHtml);
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();

    // Remove toast element after it's hidden
    document
        .getElementById(toastId)
        .addEventListener("hidden.bs.toast", function () {
            this.remove();
        });
}

/**
 * Get or create toast container
 * @returns {jQuery} Toast container element
 */
function getOrCreateToastContainer() {
    let container = $("#toast-container");
    if (container.length === 0) {
        $("body").append(`
            <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            </div>
        `);
        container = $("#toast-container");
    }
    return container;
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    if (text === null || text === undefined) return "";
    const map = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;",
    };
    return String(text).replace(/[&<>"']/g, function (m) {
        return map[m];
    });
}

/**
 * Format date to readable string
 * @param {string} dateString - Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

/**
 * Debounce function
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} Is valid email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone format (Indonesian)
 * @param {string} phone - Phone to validate
 * @returns {boolean} Is valid phone
 */
function isValidPhone(phone) {
    const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
    return phoneRegex.test(phone.replace(/[-\s]/g, ""));
}

/**
 * Show confirmation dialog
 * @param {string} title - Dialog title
 * @param {string} message - Dialog message
 * @param {Function} onConfirm - Callback when confirmed
 */
function showConfirmDialog(title, message, onConfirm) {
    const modalId = "confirmModal_" + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="${modalId}_confirm">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $("body").append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById(modalId));

    // Handle confirm click
    $(`#${modalId}_confirm`).on("click", function () {
        modal.hide();
        if (typeof onConfirm === "function") {
            onConfirm();
        }
    });

    // Remove modal from DOM after hidden
    document
        .getElementById(modalId)
        .addEventListener("hidden.bs.modal", function () {
            this.remove();
        });

    modal.show();
}

/**
 * Initialize common features when document is ready
 */
$(document).ready(function () {
    // Setup CSRF token
    setupCSRFToken();

    // Add loading states to all buttons with data-loading attribute
    $("[data-loading]").on("click", function () {
        const button = $(this);
        const originalText = button.html();
        const loadingText = button.data("loading");

        button.html(
            `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`
        );
        button.prop("disabled", true);

        // Auto-restore after 5 seconds if not manually restored
        setTimeout(() => {
            if (button.prop("disabled")) {
                button.html(originalText);
                button.prop("disabled", false);
            }
        }, 5000);
    });

    // Auto-dismiss alerts after 5 seconds
    $(".alert[data-auto-dismiss]").each(function () {
        const alert = $(this);
        setTimeout(() => {
            alert.fadeOut();
        }, 5000);
    });
});
