/**
 * Customer Management JavaScript
 * Handle all AJAX operations for Customer CRUD
 */

$(document).ready(function () {
    // Initialize customer management
    initializeCustomerManagement();
});

function initializeCustomerManagement() {
    // Load customers on page load
    loadCustomers();

    // Search functionality with debounce
    const debouncedSearch = debounce(function (searchTerm) {
        loadCustomers(searchTerm);
    }, 500);

    $("#searchInput").on("input", function () {
        debouncedSearch($(this).val());
    });

    // Refresh button
    $("#refreshBtn").on("click", function () {
        $("#searchInput").val("");
        loadCustomers();
    });

    // Add customer button
    $("#addCustomerBtn").on("click", function () {
        resetForm();
        $("#customerModalLabel").text("Add Customer");
        $("#customerModal").modal("show");
    });

    // Customer form submission
    $("#customerForm").on("submit", function (e) {
        e.preventDefault();
        saveCustomer();
    });

    // Edit customer - using event delegation
    $(document).on("click", ".edit-btn", function () {
        const customerId = $(this).data("id");
        editCustomer(customerId);
    });

    // Delete customer - using event delegation
    $(document).on("click", ".delete-btn", function () {
        const customerId = $(this).data("id");
        const customerName = $(this).data("name");
        showConfirmDialog(
            "Delete Customer",
            `Are you sure you want to delete customer <strong>${escapeHtml(
                customerName
            )}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
            () => deleteCustomer(customerId)
        );
    });
}

/**
 * Load customers from server
 * @param {string} search - Search query
 */
function loadCustomers(search = "") {
    showLoading(true);
    $("#noDataIndicator").hide();

    $.ajax({
        url: "/api/customers",
        method: "GET",
        data: { search: search },
        success: function (response) {
            showLoading(false);
            if (response.status === "success") {
                displayCustomers(response.data);
            } else {
                showAlert("error", "Failed to load customers");
            }
        },
        error: function (xhr) {
            showLoading(false);
            const message =
                xhr.responseJSON?.message || "Failed to load customers";
            showAlert("error", message);
        },
    });
}

/**
 * Display customers in table
 * @param {Array} customers - Array of customer objects
 */
function displayCustomers(customers) {
    const tbody = $("#customerTableBody");
    tbody.empty();

    if (customers.length === 0) {
        $("#noDataIndicator").show();
        return;
    }

    customers.forEach(function (customer, index) {
        const statusBadge = getStatusBadge(customer.status);
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHtml(customer.name)}</td>
                <td>${escapeHtml(customer.email)}</td>
                <td>${escapeHtml(customer.phone)}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary edit-btn" 
                            data-id="${customer.id}" 
                            title="Edit Customer">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" 
                            data-id="${customer.id}" 
                            data-name="${escapeHtml(customer.name)}"
                            title="Delete Customer">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

/**
 * Get status badge HTML
 * @param {string} status - Customer status
 * @returns {string} Badge HTML
 */
function getStatusBadge(status) {
    const badges = {
        active: '<span class="badge bg-success">Active</span>',
        pending: '<span class="badge bg-warning text-dark">Pending</span>',
        inactive: '<span class="badge bg-secondary">Inactive</span>',
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Reset customer form
 */
function resetForm() {
    $("#customerForm")[0].reset();
    $("#customerId").val("");
    $(".form-control, .form-select").removeClass("is-invalid");
    $(".invalid-feedback").text("");
}

/**
 * Save customer (create or update)
 */
function saveCustomer() {
    const customerId = $("#customerId").val();
    const isEdit = customerId !== "";
    const url = isEdit ? `/api/customers/${customerId}` : "/api/customers";
    const method = isEdit ? "PUT" : "POST";

    const formData = {
        name: $("#customerName").val().trim(),
        email: $("#customerEmail").val().trim(),
        phone: $("#customerPhone").val().trim(),
        status: $("#customerStatus").val(),
    };

    // Validate form data
    if (!validateCustomerForm(formData)) {
        return;
    }

    // Show loading state
    const saveBtn = $("#saveCustomerBtn");
    toggleLoading(saveBtn, true);

    // Clear previous errors
    clearFormErrors();

    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function (response) {
            toggleLoading(saveBtn, false);

            if (response.status === "success") {
                $("#customerModal").modal("hide");
                loadCustomers();
                showToast(response.message, "success");
            } else {
                showToast(
                    response.message || "Failed to save customer",
                    "error"
                );
            }
        },
        error: function (xhr) {
            toggleLoading(saveBtn, false);

            if (xhr.status === 422) {
                displayValidationErrors(xhr.responseJSON.errors);
            } else {
                const message =
                    xhr.responseJSON?.message || "Failed to save customer";
                showToast(message, "error");
            }
        },
    });
}

/**
 * Validate customer form data
 * @param {Object} formData - Form data to validate
 * @returns {boolean} Is form valid
 */
function validateCustomerForm(formData) {
    let isValid = true;

    if (!formData.name) {
        $("#customerName").addClass("is-invalid");
        $("#nameError").text("Name is required");
        isValid = false;
    }

    if (!formData.email) {
        $("#customerEmail").addClass("is-invalid");
        $("#emailError").text("Email is required");
        isValid = false;
    } else if (!isValidEmail(formData.email)) {
        $("#customerEmail").addClass("is-invalid");
        $("#emailError").text("Please enter a valid email address");
        isValid = false;
    }

    if (!formData.phone) {
        $("#customerPhone").addClass("is-invalid");
        $("#phoneError").text("Phone is required");
        isValid = false;
    } else if (!isValidPhone(formData.phone)) {
        $("#customerPhone").addClass("is-invalid");
        $("#phoneError").text("Please enter a valid phone number");
        isValid = false;
    }

    if (!formData.status) {
        $("#customerStatus").addClass("is-invalid");
        $("#statusError").text("Status is required");
        isValid = false;
    }

    return isValid;
}

/**
 * Edit customer - load data into form
 * @param {number} customerId - Customer ID
 */
function editCustomer(customerId) {
    $.ajax({
        url: `/api/customers/${customerId}`,
        method: "GET",
        success: function (response) {
            if (response.status === "success") {
                const customer = response.data;
                $("#customerId").val(customer.id);
                $("#customerName").val(customer.name);
                $("#customerEmail").val(customer.email);
                $("#customerPhone").val(customer.phone);
                $("#customerStatus").val(customer.status);
                $("#customerModalLabel").text("Edit Customer");
                $("#customerModal").modal("show");
            } else {
                showToast(
                    response.message || "Failed to load customer data",
                    "error"
                );
            }
        },
        error: function (xhr) {
            const message =
                xhr.responseJSON?.message || "Failed to load customer data";
            showToast(message, "error");
        },
    });
}

/**
 * Delete customer
 * @param {number} customerId - Customer ID
 */
function deleteCustomer(customerId) {
    $.ajax({
        url: `/api/customers/${customerId}`,
        method: "DELETE",
        success: function (response) {
            if (response.status === "success") {
                loadCustomers();
                showToast(response.message, "success");
            } else {
                showToast(
                    response.message || "Failed to delete customer",
                    "error"
                );
            }
        },
        error: function (xhr) {
            const message =
                xhr.responseJSON?.message || "Failed to delete customer";
            showToast(message, "error");
        },
    });
}

/**
 * Show/hide loading indicator
 * @param {boolean} show - Show or hide loading
 */
function showLoading(show) {
    if (show) {
        $("#loadingIndicator").show();
    } else {
        $("#loadingIndicator").hide();
    }
}

/**
 * Set button loading state
 * @param {jQuery} button - Button element
 * @param {boolean} loading - Loading state
 */
function setButtonLoading(button, loading) {
    const spinner = button.find(".spinner-border");
    if (loading) {
        spinner.removeClass("d-none");
        button.prop("disabled", true);
    } else {
        spinner.addClass("d-none");
        button.prop("disabled", false);
    }
}

/**
 * Clear form validation errors
 */
function clearFormErrors() {
    $(".form-control, .form-select").removeClass("is-invalid");
    $(".invalid-feedback").text("");
}

/**
 * Display validation errors
 * @param {Object} errors - Validation errors object
 */
function displayValidationErrors(errors) {
    Object.keys(errors).forEach(function (field) {
        const fieldName = field.charAt(0).toUpperCase() + field.slice(1);
        $(`#customer${fieldName}`).addClass("is-invalid");
        $(`#${field}Error`).text(errors[field][0]);
    });
}
