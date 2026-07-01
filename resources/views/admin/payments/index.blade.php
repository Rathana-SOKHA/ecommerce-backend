@extends('admin.layouts.app')

@section('content')

<h1 class="admin-page-title">Payments</h1>

@if(session('success'))
    <div class="admin-alert admin-alert-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="admin-alert admin-alert-error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

<div class="admin-card" style="padding: 0; overflow: hidden;">

    <table class="admin-table">

        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Order</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @forelse($payments as $payment)

            <tr>

                <td style="font-weight: 600; color: var(--color-gray-900);">
                    #{{ $payment->id }}
                </td>

                <td>
                    <div class="cell-user">
                        <div class="user-avatar-sm" style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1); color: var(--color-primary);">
                            {{ substr($payment->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div class="user-name">{{ $payment->user->name ?? 'N/A' }}</div>
                            <span class="user-email">{{ $payment->user->email ?? '' }}</span>
                        </div>
                    </div>
                </td>

                <td>
                    <a href="{{ route('admin.orders.show', $payment->order) }}"
                       class="admin-btn admin-btn-sm"
                       style="background: var(--color-gray-100); color: var(--color-gray-700); font-weight: 600; text-decoration: none;">
                        #{{ $payment->order_id }}
                    </a>
                </td>

                <td>
                    <span class="admin-badge admin-badge-info" style="text-transform: uppercase;">
                        {{ $payment->payment_method }}
                    </span>
                </td>

                <td style="font-weight: 700; color: var(--color-gray-900);">
                    ${{ number_format($payment->amount, 2) }}
                </td>

                <td>
                    @php
                        $statusBadge = match($payment->status) {
                            'pending' => 'admin-badge-warning',
                            'approved' => 'admin-badge-success',
                            'rejected' => 'admin-badge-danger',
                            default => 'admin-badge-info',
                        };
                    @endphp
                    <span class="admin-badge {{ $statusBadge }}">
                        @if($payment->status === 'pending')
                            <span class="badge-dot" style="animation: pulse 1.5s infinite;"></span>
                        @else
                            <span class="badge-dot"></span>
                        @endif
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>

                <td style="color: var(--color-gray-500); font-size: 13px;">
                    {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                </td>

                <td>
                    <a href="{{ route('admin.payments.show', $payment) }}"
                       class="admin-btn admin-btn-primary admin-btn-sm"
                       style="display: inline-flex;">
                        View
                    </a>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="8" class="admin-empty">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <p>No payments yet</p>
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

{{ $payments->links('vendor.pagination.admin') }}

@endsection
