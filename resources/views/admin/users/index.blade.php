@extends('admin.layouts.app')

@section('content')

<h1 class="admin-page-title">Users</h1>

<div class="admin-card" style="padding: 0; overflow: hidden;">

    <table class="admin-table">

        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @foreach($users as $user)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $user->name }}</td>

                <td>{{ $user->email }}</td>

                <td>
                    <span class="admin-badge admin-badge-info">
                        {{ $user->role }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('admin.users.show', $user) }}"
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

{{ $users->links('vendor.pagination.admin') }}

@endsection
