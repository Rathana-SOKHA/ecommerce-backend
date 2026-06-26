@extends('admin.layouts.app')

@section('content')

<div class="admin-flex-between admin-mb-lg">
    <div>
        <h1 class="admin-page-title">Dashboard</h1>
        <p class="admin-page-subtitle">Here's what's happening with your store today.</p>
    </div>
    <div class="admin-flex admin-gap-sm">
        <a href="{{ route('products.create') }}" class="admin-btn admin-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Product
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="admin-stats-grid">

    <div class="admin-stat-card">
        <div class="stat-icon-wrap stat-icon-categories">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Categories</div>
            <div class="stat-value">{{ $totalCategories }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                All categories
            </div>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="stat-icon-wrap stat-icon-products">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Products</div>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                In stock
            </div>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="stat-icon-wrap stat-icon-users">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Users</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                Registered
            </div>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="stat-icon-wrap stat-icon-orders">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Orders</div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                Total orders
            </div>
        </div>
    </div>

</div>

<!-- Content Grid -->
<div class="admin-content-grid">

    <!-- Recent Users -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="card-header-action">
                View All
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>
        <div style="margin: 0 -24px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div class="cell-user">
                                <div class="user-avatar-sm" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="user-name">{{ $user->name }}</span>
                                    <span class="user-email">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="admin-badge admin-badge-success">
                                <span class="badge-dot"></span>
                                Active
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">
                            <div class="admin-empty">
                                <p>No users yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="card-header-action">
                View All
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>
        <div style="margin: 0 -24px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td style="font-weight: 600; color: var(--color-gray-900);">
                            #{{ $loop->iteration }}
                        </td>
                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $badge = match($order->status) {
                                    'pending' => 'admin-badge-warning',
                                    'paid' => 'admin-badge-info',
                                    'shipped' => 'admin-badge-info',
                                    'completed' => 'admin-badge-success',
                                    'cancelled' => 'admin-badge-danger',
                                    default => 'admin-badge-info',
                                };
                            @endphp
                            <span class="admin-badge {{ $badge }}">
                                <span class="badge-dot"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="admin-empty">
                                <p>No orders yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
