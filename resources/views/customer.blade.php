@extends('layouts.app')

@section('title', 'Customer - Laravel Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Customer Management</h1>
                <button class="btn btn-primary" id="addCustomerBtn">
                    <i class="bi bi-plus-circle"></i> Add Customer
                </button>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search customers...">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-outline-secondary" id="refreshBtn">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="customerTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="loadingIndicator" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="noDataIndicator" class="text-center py-5" style="display: none;">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No customers found</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="customerForm">
                    <div class="modal-body">
                        <input type="hidden" id="customerId" name="id">

                        <div class="mb-3">
                            <label for="customerName" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customerName" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="customerEmail" name="email" required>
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customerPhone" name="phone" required>
                            <div class="invalid-feedback" id="phoneError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="customerStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="customerStatus" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback" id="statusError"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveCustomerBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete customer <strong id="deleteCustomerName"></strong>?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                    <input type="hidden" id="deleteCustomerId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Delete Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal for Success/Error Messages -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="alertModalHeader">
                    <h5 class="modal-title" id="alertModalLabel">Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i id="alertIcon" class="me-3 display-6"></i>
                        <div>
                            <p id="alertMessage" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Load customers on page load
            loadCustomers();

            // Search functionality
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    loadCustomers($('#searchInput').val());
                }, 500);
            });

            // Refresh button
            $('#refreshBtn').on('click', function() {
                $('#searchInput').val('');
                loadCustomers();
            });

            // Add customer button
            $('#addCustomerBtn').on('click', function() {
                resetForm();
                $('#customerModalLabel').text('Add Customer');
                $('#customerModal').modal('show');
            });

            // Customer form submission
            $('#customerForm').on('submit', function(e) {
                e.preventDefault();
                saveCustomer();
            });

            // Edit customer
            $(document).on('click', '.edit-btn', function() {
                const customerId = $(this).data('id');
                editCustomer(customerId);
            });

            // Delete customer
            $(document).on('click', '.delete-btn', function() {
                const customerId = $(this).data('id');
                const customerName = $(this).data('name');
                $('#deleteCustomerId').val(customerId);
                $('#deleteCustomerName').text(customerName);
                $('#deleteModal').modal('show');
            });

            // Confirm delete
            $('#confirmDeleteBtn').on('click', function() {
                deleteCustomer($('#deleteCustomerId').val());
            });
        });

        function loadCustomers(search = '') {
            $('#loadingIndicator').show();
            $('#noDataIndicator').hide();

            $.ajax({
                url: '/api/customers',
                method: 'GET',
                data: {
                    search: search
                },
                success: function(response) {
                    $('#loadingIndicator').hide();
                    if (response.status === 'success') {
                        displayCustomers(response.data);
                    }
                },
                error: function() {
                    $('#loadingIndicator').hide();
                    showAlert('error', 'Failed to load customers');
                }
            });
        }

        function displayCustomers(customers) {
            const tbody = $('#customerTableBody');
            tbody.empty();

            if (customers.length === 0) {
                $('#noDataIndicator').show();
                return;
            }

            customers.forEach(function(customer, index) {
                const statusBadge = getStatusBadge(customer.status);
                const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${customer.name}</td>
                <td>${customer.email}</td>
                <td>${customer.phone}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${customer.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${customer.id}" data-name="${customer.name}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
                tbody.append(row);
            });
        }

        function getStatusBadge(status) {
            const badges = {
                'active': '<span class="badge bg-success">Active</span>',
                'pending': '<span class="badge bg-warning">Pending</span>',
                'inactive': '<span class="badge bg-secondary">Inactive</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
        }

        function resetForm() {
            $('#customerForm')[0].reset();
            $('#customerId').val('');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function saveCustomer() {
            const customerId = $('#customerId').val();
            const isEdit = customerId !== '';
            const url = isEdit ? `/api/customers/${customerId}` : '/api/customers';
            const method = isEdit ? 'PUT' : 'POST';

            const formData = {
                name: $('#customerName').val(),
                email: $('#customerEmail').val(),
                phone: $('#customerPhone').val(),
                status: $('#customerStatus').val()
            };

            // Show loading
            const saveBtn = $('#saveCustomerBtn');
            saveBtn.find('.spinner-border').removeClass('d-none');
            saveBtn.prop('disabled', true);

            // Clear previous errors
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    saveBtn.find('.spinner-border').addClass('d-none');
                    saveBtn.prop('disabled', false);

                    if (response.status === 'success') {
                        $('#customerModal').modal('hide');
                        loadCustomers();
                        showAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    saveBtn.find('.spinner-border').addClass('d-none');
                    saveBtn.prop('disabled', false);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(field) {
                            $(`#customer${field.charAt(0).toUpperCase() + field.slice(1)}`).addClass(
                                'is-invalid');
                            $(`#${field}Error`).text(errors[field][0]);
                        });
                    } else {
                        showAlert('error', 'Failed to save customer');
                    }
                }
            });
        }

        function editCustomer(customerId) {
            $.ajax({
                url: `/api/customers/${customerId}`,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        const customer = response.data;
                        $('#customerId').val(customer.id);
                        $('#customerName').val(customer.name);
                        $('#customerEmail').val(customer.email);
                        $('#customerPhone').val(customer.phone);
                        $('#customerStatus').val(customer.status);
                        $('#customerModalLabel').text('Edit Customer');
                        $('#customerModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('error', 'Failed to load customer data');
                }
            });
        }

        function deleteCustomer(customerId) {
            const deleteBtn = $('#confirmDeleteBtn');
            deleteBtn.find('.spinner-border').removeClass('d-none');
            deleteBtn.prop('disabled', true);

            $.ajax({
                url: `/api/customers/${customerId}`,
                method: 'DELETE',
                success: function(response) {
                    deleteBtn.find('.spinner-border').addClass('d-none');
                    deleteBtn.prop('disabled', false);

                    if (response.status === 'success') {
                        $('#deleteModal').modal('hide');
                        loadCustomers();
                        showAlert('success', response.message);
                    }
                },
                error: function() {
                    deleteBtn.find('.spinner-border').addClass('d-none');
                    deleteBtn.prop('disabled', false);
                    showAlert('error', 'Failed to delete customer');
                }
            });
        }

        function showAlert(type, message) {
            const alertModal = $('#alertModal');
            const alertHeader = $('#alertModalHeader');
            const alertIcon = $('#alertIcon');
            const alertMessage = $('#alertMessage');

            if (type === 'success') {
                alertHeader.removeClass('bg-danger').addClass('bg-success text-white');
                alertIcon.removeClass().addClass('bi bi-check-circle-fill text-success me-3 display-6');
                $('#alertModalLabel').text('Success');
            } else {
                alertHeader.removeClass('bg-success').addClass('bg-danger text-white');
                alertIcon.removeClass().addClass('bi bi-exclamation-triangle-fill text-danger me-3 display-6');
                $('#alertModalLabel').text('Error');
            }

            alertMessage.text(message);
            alertModal.modal('show');
        }
    </script>
@endsection
