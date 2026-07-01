@extends('admin.layouts.app')

@section('content')

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
    <div>
        <h1 class="admin-page-title" style="margin-bottom: 4px;">
            Payment #{{ $payment->id }}
        </h1>
        <p class="admin-page-subtitle" style="margin-bottom: 0;">
            Order #{{ $payment->order_id }} &middot; {{ $payment->payment_method }}
        </p>
    </div>
    <a href="{{ route('admin.payments.index') }}"
       class="admin-btn admin-btn-secondary"
       style="display: inline-flex;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to Payments
    </a>
</div>

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

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

    {{-- Payment Info --}}
    <div class="admin-card">

        <h2 style="font-size: 17px; font-weight: 700; color: var(--color-gray-900); margin-bottom: 20px;">
            Payment Details
        </h2>

        <div style="display: grid; gap: 4px;">

            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Payment ID</span>
                <span style="color: var(--color-gray-900); font-weight: 600;">#{{ $payment->id }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Order</span>
                <a href="{{ route('admin.orders.show', $payment->order) }}"
                   style="color: var(--color-primary); font-weight: 600; text-decoration: none;">
                    #{{ $payment->order_id }}
                </a>
            </div>

            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Payment Method</span>
                <span class="admin-badge admin-badge-info" style="text-transform: uppercase;">
                    {{ $payment->payment_method }}
                </span>
            </div>

            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Amount</span>
                <span style="color: var(--color-gray-900); font-weight: 700; font-size: 18px;">
                    ${{ number_format($payment->amount, 2) }}
                </span>
            </div>

            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Reference</span>
                <span style="color: var(--color-gray-700);">
                    {{ $payment->reference_number ?? 'N/A' }}
                </span>
            </div>

            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Status</span>
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
            </div>

            <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Paid At</span>
                <span style="color: var(--color-gray-700);">
                    {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : 'N/A' }}
                </span>
            </div>

        </div>

    </div>

    {{-- Customer Info --}}
    <div class="admin-card">

        <h2 style="font-size: 17px; font-weight: 700; color: var(--color-gray-900); margin-bottom: 20px;">
            Customer Details
        </h2>

        <div style="display: flex; align-items: center; gap: 14px; padding-bottom: 16px; border-bottom: 1px solid var(--color-gray-100); margin-bottom: 12px;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light)); display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 18px; font-weight: 700; flex-shrink: 0;">
                {{ substr($payment->user->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <div style="font-weight: 600; color: var(--color-gray-900);">{{ $payment->user->name ?? 'N/A' }}</div>
                <div style="font-size: 13px; color: var(--color-gray-500);">{{ $payment->user->email ?? '' }}</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-gray-100);">
            <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">User ID</span>
            <span style="color: var(--color-gray-700);">#{{ $payment->user_id }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
            <span style="font-weight: 600; color: var(--color-gray-500); font-size: 13px;">Registered</span>
            <span style="color: var(--color-gray-700);">
                {{ $payment->user->created_at->format('M d, Y') }}
            </span>
        </div>

    </div>

</div>

{{-- Payment Image --}}
<div class="admin-card" style="margin-top: 24px;">

    <h2 style="font-size: 17px; font-weight: 700; color: var(--color-gray-900); margin-bottom: 16px;">
        Payment Proof
    </h2>

    <div style="background: var(--color-gray-50); border-radius: var(--radius-md); padding: 24px; display: flex; justify-content: center; border: 2px dashed var(--color-gray-200);">
        <a href="{{ asset('storage/' . $payment->payment_image) }}"
           target="_blank"
           class="payment-proof-link"
           style="display: inline-block; text-decoration: none;">
            <img src="{{ asset('storage/' . $payment->payment_image) }}"
                 alt="Payment Proof"
                 class="payment-proof-img"
                 style="max-width: 100%; max-height: 400px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); object-fit: contain; cursor: pointer; transition: transform var(--transition-normal);">
        </a>

        <style>
            .payment-proof-link:hover .payment-proof-img {
                transform: scale(1.02);
            }
        </style>
    </div>

</div>

{{-- Action Buttons --}}
@if($payment->status === 'pending')

    <div class="admin-card" style="margin-top: 24px;">

        <h2 style="font-size: 17px; font-weight: 700; color: var(--color-gray-900); margin-bottom: 4px;">
            Review Payment
        </h2>
        <p style="font-size: 14px; color: var(--color-gray-500); margin-bottom: 20px;">
            Verify the payment proof before approving or rejecting.
        </p>

        <div style="display: flex; gap: 12px;">

            {{-- Approve Form --}}
            <form method="POST"
                  action="{{ route('admin.payments.approve', $payment) }}"
                  onsubmit="return confirm('Are you sure you want to APPROVE this payment? This will mark the order as paid and cannot be undone.')">

                @csrf

                <button type="submit"
                        class="admin-btn admin-btn-primary"
                        style="display: inline-flex; padding: 12px 28px; font-size: 15px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Approve Payment
                </button>

            </form>

            {{-- Reject Button triggers modal --}}
            <button type="button"
                    onclick="openRejectModal()"
                    class="admin-btn admin-btn-danger"
                    style="display: inline-flex; padding: 12px 28px; font-size: 15px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Reject Payment
            </button>

        </div>

    </div>

@endif

{{-- Order Items Summary --}}
@if($payment->order->items->isNotEmpty())
    <div class="admin-card" style="margin-top: 24px; padding: 0; overflow: hidden;">

        <div style="padding: 20px 24px 0;">
            <h2 style="font-size: 17px; font-weight: 700; color: var(--color-gray-900); margin-bottom: 4px;">
                Order Items
            </h2>
            <p style="font-size: 14px; color: var(--color-gray-500); margin-bottom: 16px;">
                Products in order #{{ $payment->order_id }}
            </p>
        </div>

        <table class="admin-table">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach($payment->order->items as $item)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                         width="36"
                                         height="36"
                                         style="border-radius: 6px; object-fit: cover;">
                                @endif
                                <span style="font-weight: 600; color: var(--color-gray-800);">
                                    {{ $item->product->name }}
                                </span>
                            </div>
                        </td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="font-weight: 700; color: var(--color-gray-900);">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 700; color: var(--color-gray-800); font-size: 15px;">
                        Total
                    </td>
                    <td style="font-weight: 700; color: var(--color-gray-900); font-size: 16px;">
                        ${{ number_format($payment->order->total_amount, 2) }}
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>
@endif

{{-- Reject Modal --}}
<div id="reject-modal" class="admin-modal-overlay" style="display: none;">

    <div class="admin-modal-box" style="max-width: 480px; text-align: left;">

        <div class="admin-modal-icon" style="text-align: center;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>

        <h3 class="admin-modal-title" style="text-align: center;">Reject Payment</h3>
        <p class="admin-modal-text" style="text-align: center;">Are you sure you want to reject this payment? This action cannot be undone.</p>

        <form method="POST"
              action="{{ route('admin.payments.reject', $payment) }}"
              id="reject-form">

            @csrf

            <div class="admin-form-group">
                <label for="rejection_reason" class="admin-form-label">
                    Reason (optional)
                </label>
                <textarea
                    id="rejection_reason"
                    name="rejection_reason"
                    class="admin-form-input"
                    rows="3"
                    placeholder="Provide a reason for rejection..."
                    maxlength="500"></textarea>
                <div class="admin-form-hint">
                    The customer will see this reason.
                </div>
            </div>

            <div class="admin-modal-actions">
                <button type="button"
                        class="admin-btn admin-btn-secondary"
                        onclick="closeRejectModal()">
                    Cancel
                </button>
                <button type="submit"
                        class="admin-btn admin-btn-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Yes, Reject Payment
                </button>
            </div>

        </form>

    </div>

</div>

<script>
    function openRejectModal() {
        document.getElementById('reject-modal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('reject-modal').style.display = 'none';
    }

    document.getElementById('reject-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>

@endsection
