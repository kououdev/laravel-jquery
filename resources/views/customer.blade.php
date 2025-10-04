@extends('layouts.app')

@section('title', 'Customer - Laravel Dashboard')

@section('styles')
    <!-- Custom Customer Styles -->
    <link href="{{ asset('css/customer.css') }}" rel="stylesheet">
@endsection

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
@endsection

@section('scripts')
    <!-- Customer Management JavaScript -->
    <script src="{{ asset('js/customer.js') }}"></script>
@endsection
