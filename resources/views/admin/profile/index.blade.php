@extends('admin.layouts.app')

@section('content')

<h1 class="admin-page-title">Profile</h1>

@if(session('success'))
    <div class="admin-alert admin-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="admin-card" style="max-width: 600px;">

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="admin-form-group">
            <label class="admin-form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="admin-form-input @error('name') admin-input-error @enderror">
            @error('name')
                <div class="admin-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="admin-form-input @error('email') admin-input-error @enderror">
            @error('email')
                <div class="admin-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Role</label>
            <input type="text" value="{{ $admin->role }}" class="admin-form-input" disabled>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">New Password</label>
            <input type="password" name="password" class="admin-form-input @error('password') admin-input-error @enderror" placeholder="Leave blank to keep current password">
            @error('password')
                <div class="admin-error">{{ $message }}</div>
            @enderror
            <div class="admin-form-hint">Leave blank to keep your current password</div>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="admin-form-input" placeholder="Confirm new password">
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="admin-btn admin-btn-primary">Save Changes</button>
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn-secondary">Cancel</a>
        </div>
    </form>

</div>

@endsection