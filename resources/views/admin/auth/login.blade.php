

@extends('admin.auth.layout')

@section('content')

<div class="admin-login-wrapper">

    <div class="admin-login-card">

        <div class="admin-login-card-header">
            <h1>Admin Panel</h1>
            <p>Sign in to access the dashboard</p>
        </div>

        <div class="admin-login-card-body">

            <form method="POST"
                  action="{{ route('admin.login.submit') }}">

                @csrf

                <div class="admin-login-form-group">
                    <label class="admin-login-label" for="email">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="admin-login-input"
                        placeholder="admin@example.com"
                        autofocus
                        required
                    >
                </div>

                <div class="admin-login-form-group">
                    <label class="admin-login-label" for="password">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="admin-login-input"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit"
                        class="admin-login-submit">
                    Login
                </button>

            </form>

        </div>

    </div>

</div>

@endsection