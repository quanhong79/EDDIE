@extends('layouts.app')

@section('title', 'Thống kê')

@section('content')
    <style>
        .card { transition: transform 0.3s; }
        .card:hover { transform: scale(1.05); }
        .nav-tabs .nav-link { font-weight: bold; }
        .tab-content { animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>

    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4 text-center">Thống kê Quản lý</h2>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="statsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="total-tab" data-toggle="tab" href="#total" role="tab">Tổng quan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="day-tab" data-toggle="tab" href="#day" role="tab">Theo ngày</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="week-tab" data-toggle="tab" href="#week" role="tab">Theo tuần</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="month-tab" data-toggle="tab" href="#month" role="tab">Theo tháng</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="year-tab" data-toggle="tab" href="#year" role="tab">Theo năm</a>
            </li>
        </ul>

        <div class="tab-content" id="statsTabContent">
            <!-- Tab Tổng quan -->
            <div class="tab-pane fade show active" id="total" role="tabpanel">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5><i class="fas fa-users mr-2"></i>Tổng người dùng</h5>
                                <p class="display-4">{{ $totalUsers }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5><i class="fas fa-box mr-2"></i>Tổng sản phẩm</h5>
                                <p class="display-4">{{ $totalProducts }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5><i class="fas fa-shopping-cart mr-2"></i>Tổng đơn hàng</h5>
                                <p class="display-4">{{ $totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5><i class="fas fa-dollar-sign mr-2"></i>Tổng doanh thu</h5>
                                <p class="display-4">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fas fa-chart-pie mr-2"></i>Trạng thái đơn hàng</h5>
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <a href="{{ route('admin.thongke.export', 'total') }}" class="btn btn-primary btn-lg">Xuất Excel Tổng quan</a>
                    </div>
                </div>
            </div>

            <!-- Tab Theo ngày -->
            <div class="tab-pane fade" id="day" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5><i class="fas fa-shopping-cart mr-2"></i>Đơn hàng hôm nay</h5>
                                <p class="display-4">{{ $ordersCountDay }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5><i class="fas fa-dollar-sign mr-2"></i>Doanh thu hôm nay</h5>
                                <p class="display-4">{{ number_format($revenueDay, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12 d-flex justify-content-center">
                        <a href="{{ route('admin.thongke.export', 'day') }}" class="btn btn-primary btn-lg">Xuất Excel Theo ngày</a>
                    </div>
                </div>
            </div>

            <!-- Tab Theo tuần -->
            <div class="tab-pane fade" id="week" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5><i class="fas fa-shopping-cart mr-2"></i>Đơn hàng tuần này</h5>
                                <p class="display-4">{{ $ordersCountWeek }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5><i class="fas fa-dollar-sign mr-2"></i>Doanh thu tuần này</h5>
                                <p class="display-4">{{ number_format($revenueWeek, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fas fa-chart-bar mr-2"></i>Doanh thu theo ngày trong tuần</h5>
                                <canvas id="revenueWeekChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <a href="{{ route('admin.thongke.export', 'week') }}" class="btn btn-primary btn-lg">Xuất Excel Theo tuần</a>
                    </div>
                </div>
            </div>

            <!-- Tab Theo tháng -->
            <div class="tab-pane fade" id="month" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5><i class="fas fa-shopping-cart mr-2"></i>Đơn hàng tháng này</h5>
                                <p class="display-4">{{ $ordersCountMonth }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5><i class="fas fa-dollar-sign mr-2"></i>Doanh thu tháng này</h5>
                                <p class="display-4">{{ number_format($revenueMonth, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fas fa-chart-bar mr-2"></i>Doanh thu theo tuần trong tháng</h5>
                                <canvas id="revenueMonthChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <a href="{{ route('admin.thongke.export', 'month') }}" class="btn btn-primary btn-lg">Xuất Excel Theo tháng</a>
                    </div>
                </div>
            </div>

            <!-- Tab Theo năm -->
            <div class="tab-pane fade" id="year" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5><i class="fas fa-shopping-cart mr-2"></i>Đơn hàng năm nay</h5>
                                <p class="display-4">{{ $ordersCountYear }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5><i class="fas fa-dollar-sign mr-2"></i>Doanh thu năm nay</h5>
                                <p class="display-4">{{ number_format($revenueYear, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fas fa-chart-bar mr-2"></i>Doanh thu theo tháng trong năm</h5>
                                <canvas id="revenueYearChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <a href="{{ route('admin.thongke.export', 'year') }}" class="btn btn-primary btn-lg">Xuất Excel Theo năm</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts cho biểu đồ -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Biểu đồ Pie Tổng quan
            new Chart(document.getElementById('orderStatusChart'), {
                type: 'pie',
                data: {
                    labels: ['Đang xử lý', 'Đã hủy', 'Đã xác nhận'],
                    datasets: [{
                        data: [{{ $orderStatus['processing'] }}, {{ $orderStatus['cancelled'] }}, {{ $orderStatus['confirmed'] }}],
                        backgroundColor: ['#007bff', '#dc3545', '#28a745']
                    }]
                },
                options: { responsive: true }
            });

            // Biểu đồ Bar Tuần
            new Chart(document.getElementById('revenueWeekChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($revenueByDayWeek)) !!},
                    datasets: [{
                        label: 'Doanh thu',
                        data: {!! json_encode(array_values($revenueByDayWeek)) !!},
                        backgroundColor: '#28a745'
                    }]
                },
                options: { responsive: true }
            });

            // Biểu đồ Bar Tháng
            new Chart(document.getElementById('revenueMonthChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($revenueByWeekMonth)) !!},
                    datasets: [{
                        label: 'Doanh thu',
                        data: {!! json_encode(array_values($revenueByWeekMonth)) !!},
                        backgroundColor: '#ffc107'
                    }]
                },
                options: { responsive: true }
            });

            // Biểu đồ Bar Năm
            new Chart(document.getElementById('revenueYearChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($revenueByMonthYear)) !!},
                    datasets: [{
                        label: 'Doanh thu',
                        data: {!! json_encode(array_values($revenueByMonthYear)) !!},
                        backgroundColor: '#17a2b8'
                    }]
                },
                options: { responsive: true }
            });
        </script>
    @else
        <div class="alert alert-danger">Bạn không có quyền truy cập!</div>
    @endif
@endsection