@extends('admin.layouts.app')

@section('content')

<h1 class="admin-page-title">Orders</h1>

<div class="admin-card" style="padding: 0; overflow: hidden;">

    <table class="admin-table">

        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @foreach($orders as $order)

            <tr>

                <td>#{{ $loop->iteration }}</td>

                <td>{{ $order->user->name ?? 'N/A' }}</td>

                <td>${{ $order->total_amount }}</td>

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
                        {{ ucfirst($order->status) }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('admin.orders.show', $order) }}"
                       class="admin-btn admin-btn-primary admin-btn-sm"
                       style="display: inline-flex;">
                        View
                    </a>
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

<div style="margin-top: 20px;">
    {{ $orders->links() }}
</div>

@endsection
