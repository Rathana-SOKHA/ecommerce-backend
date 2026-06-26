@extends('admin.layouts.app')

@section('content')

<h1 class="admin-page-title">
    Order #{{ $order->id }}
</h1>

<div class="admin-card">

    <div style="display: grid; gap: 16px;">

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">Customer</span>
            <span style="color: #1e293b;">{{ $order->user->name }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">Total</span>
            <span style="color: #1e293b; font-weight: 700;">${{ $order->total_amount }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">Status</span>
            <span style="color: #1e293b; text-transform: capitalize;">{{ $order->status }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
            <span style="font-weight: 600; color: #64748b;">Date</span>
            <span style="color: #1e293b;">{{ $order->created_at }}</span>
        </div>

    </div>

</div>

<div class="admin-card" style="margin-top: 20px;">

    <h2 class="admin-card-header" style="margin-bottom: 16px;">Update Status</h2>

    <form method="POST"
          action="{{ route('admin.orders.status', $order) }}">

        @csrf

        <div style="display: flex; gap: 10px; align-items: center;">

            <select
                name="status"
                class="admin-form-input"
                style="max-width: 220px;"
            >

                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>

                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>

                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>

                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>

                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>

            </select>

            <button type="submit"
                    class="admin-btn admin-btn-primary">
                Update
            </button>

        </div>

    </form>

</div>

@endsection
