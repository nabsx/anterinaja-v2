@extends('layouts.admin')

@section('title', 'Keuangan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard Keuangan</h3>
                </div>
                <div class="card-body">
                    
                    <!-- Statistik Keuangan -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Pendapatan</h5>
                                    <h3>Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Total Komisi</h5>
                                    <h3>Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Penghasilan Driver</h5>
                                    <h3>Rp {{ number_format($stats['driver_earnings'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Pending Payouts</h5>
                                    <h3>Rp {{ number_format($stats['pending_payouts'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h5>Pendapatan Hari Ini</h5>
                                    <h3>Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5>Pendapatan Bulan Ini</h5>
                                    <h3>Rp {{ number_format($stats['this_month_revenue'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Drivers -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Top Driver (Penghasilan Tertinggi)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Driver</th>
                                                    <th>Total Penghasilan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($top_drivers as $index => $driver)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $driver->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $driver->user->email }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            Rp {{ number_format($driver->orders_sum_driver_earning ?? $driver->total_earnings ?? 0, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data driver</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Transaksi Terbaru</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kode Order</th>
                                                    <th>Customer</th>
                                                    <th>Driver</th>
                                                    <th>Total</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recent_transactions as $transaction)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $transaction->order_code }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $transaction->customer->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $transaction->driver->user->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            Rp {{ number_format($transaction->actual_fare, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $transaction->completed_at ? $transaction->completed_at->format('d/m/Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada transaksi terbaru</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Breakdown -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Ringkasan Keuangan</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-money-bill-wave"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Pendapatan</span>
                                                    <span class="info-box-number">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Komisi Platform</span>
                                                    <span class="info-box-number">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-car"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Penghasilan Driver</span>
                                                    <span class="info-box-number">Rp {{ number_format($stats['driver_earnings'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pending Payouts</span>
                                                    <span class="info-box-number">Rp {{ number_format($stats['pending_payouts'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border: none;
        border-radius: 8px;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .info-box {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .info-box-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 15px;
    }
    .info-box-content {
        flex: 1;
    }
    .info-box-text {
        font-weight: 500;
        color: #666;
    }
    .info-box-number {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
</style>
@endsection