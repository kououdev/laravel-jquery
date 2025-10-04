@extends('layouts.app')

@section('title', 'Dashboard - Laravel Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="py-5">
                        <i class="bi bi-speedometer2 display-1 text-primary mb-3"></i>
                        <h2 class="card-title">Welcome to Dashboard Page</h2>
                        <p class="card-text text-muted">Selamat datang di halaman dashboard utama</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <h2>150</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Active Orders</h5>
                            <h2>75</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-bag-check display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Pending Tasks</h5>
                            <h2>25</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
