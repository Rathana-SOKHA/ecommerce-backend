@extends('admin.layouts.app')

@section('content')

<h1 class="admin-page-title">User Detail</h1>

<div class="admin-card">

    <div style="display: grid; gap: 16px;">

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">ID</span>
            <span style="color: #1e293b;">{{ $user->id }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">Name</span>
            <span style="color: #1e293b;">{{ $user->name }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">Email</span>
            <span style="color: #1e293b;">{{ $user->email }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 600; color: #64748b;">Role</span>
            <span class="admin-badge admin-badge-info">{{ $user->role }}</span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
            <span style="font-weight: 600; color: #64748b;">Created</span>
            <span style="color: #1e293b;">{{ $user->created_at }}</span>
        </div>

    </div>

    <div style="margin-top: 24px;">
        <a href="{{ route('admin.users.index') }}"
           class="admin-btn admin-btn-secondary">
            Back to Users
        </a>
    </div>

</div>

@endsection
