@extends('admin.layout.app')
@section('title', 'Dashboard')

@section('content')
    <div class="page-content">
        <!-- Top stats -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">2,847</div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-change up">
                            <i class="bi bi-arrow-up-short"></i> 12.5% this month
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon gold">
                        <i class="bi bi-cart"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">1,254</div>
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-change up">
                            <i class="bi bi-arrow-up-short"></i> 8.2% this month
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">$48,290</div>
                        <div class="stat-label">Revenue</div>
                        <div class="stat-change up">
                            <i class="bi bi-arrow-up-short"></i> 15.3% this month
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon gold">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">94.2%</div>
                        <div class="stat-label">Growth Rate</div>
                        <div class="stat-change down">
                            <i class="bi bi-arrow-down-short"></i> 2.1% this month
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Revenue overview -->
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Revenue Overview</h3>
                        <select class="form-select form-select-sm" style="width: auto; border-radius: 8px;">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>Last 90 days</option>
                        </select>
                    </div>
                    <div class="card-body-custom">
                        <div class="chart-placeholder d-flex align-items-center justify-content-center">
                            <span>
                                <i class="bi bi-bar-chart-line me-2"></i>
                                Chart area — integrate Chart.js or similar
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent activity -->
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h3 class="mb-0">Recent Activity</h3>
                    </div>
                    <div class="card-body-custom">
                        <div class="activity-item d-flex align-items-start gap-3 mb-3">
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text">New user registered</div>
                                <div class="activity-time">2 minutes ago</div>
                            </div>
                        </div>

                        <div class="activity-item d-flex align-items-start gap-3 mb-3">
                            <div class="activity-dot gold"></div>
                            <div>
                                <div class="activity-text">Order #1247 completed</div>
                                <div class="activity-time">15 minutes ago</div>
                            </div>
                        </div>

                        <div class="activity-item d-flex align-items-start gap-3 mb-3">
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text">Payment received $320</div>
                                <div class="activity-time">1 hour ago</div>
                            </div>
                        </div>

                        <div class="activity-item d-flex align-items-start gap-3 mb-3">
                            <div class="activity-dot gold"></div>
                            <div>
                                <div class="activity-text">New product added</div>
                                <div class="activity-time">3 hours ago</div>
                            </div>
                        </div>

                        <div class="activity-item d-flex align-items-start gap-3">
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text">Report generated</div>
                                <div class="activity-time">5 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent orders -->
        <div class="content-card mt-3">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Recent Orders</h3>
                <a href="{{ url('table') }}" class="btn btn-sm btn-outline-primary-custom">View All</a>
            </div>

            <div class="card-body-custom p-0">
                <div class="table-scroll-wrap">
                    <table class="table table-custom table-stack-mobile mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-1247</td>
                                <td>Ahmed Khan</td>
                                <td>Jul 1, 2026</td>
                                <td>$320.00</td>
                                <td><span class="badge-status active">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-1246</td>
                                <td>Sara Ali</td>
                                <td>Jun 30, 2026</td>
                                <td>$145.50</td>
                                <td><span class="badge-status pending">Pending</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-1245</td>
                                <td>Usman Malik</td>
                                <td>Jun 29, 2026</td>
                                <td>$89.00</td>
                                <td><span class="badge-status active">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-1244</td>
                                <td>Fatima Noor</td>
                                <td>Jun 28, 2026</td>
                                <td>$512.75</td>
                                <td><span class="badge-status inactive">Cancelled</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
