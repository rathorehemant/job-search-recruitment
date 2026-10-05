@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<div class="container-fluid p-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Users
            </h4>

            <p class="text-muted mb-0">
                Manage system users and their roles.
            </p>
        </div>

        <a href="#" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add User
        </a>

    </div>



    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('users.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-6 col-lg-5">

                        <label
                            for="search"
                            class="form-label fw-semibold">
                            Search
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                placeholder="Search by name or email">

                        </div>

                    </div>


                    <div class="col-md-4 col-lg-3">

                        <label
                            for="role"
                            class="form-label fw-semibold">
                            Role
                        </label>

                        <select
                            name="role"
                            id="role"
                            class="form-select">

                            <option value="">
                                All Roles
                            </option>

                            @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2 col-lg-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            <i class="bi bi-search me-1"></i>
                            Search

                        </button>

                    </div>


                    <div class="col-md-12 col-lg-2">

                        @if(request('search') || request('role'))

                        <a
                            href="{{ route('users.index') }}"
                            class="btn btn-light border w-100">

                            Clear

                        </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Users Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">
                                #
                            </th>

                            <th>
                                User
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
                            </th>

                            <th class="text-end px-4">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($users as $user)

                        <tr>

                            {{-- ID --}}
                            <td class="px-4">
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            {{-- User --}}
                            <td>

                                <div class="d-flex align-items-center gap-2">

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $user->name }}
                                        </div>
                                    </div>

                                </div>

                            </td>

                            {{-- Email --}}
                            <td>
                                {{ $user->email }}
                            </td>

                            {{-- Role --}}
                            <td>

                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $user->role?->name ?? 'No Role' }}
                                </span>

                            </td>

                            {{-- Status --}}
                            <td>

                                <span class="badge bg-success-subtle text-success">
                                    Active
                                </span>

                            </td>

                            {{-- Created At --}}
                            <td>
                                {{ $user->created_at?->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end px-4">

                                <div class="dropdown">

                                    <button
                                        class="btn btn-sm btn-light"
                                        type="button"
                                        data-bs-toggle="dropdown">

                                        <i class="bi bi-three-dots-vertical"></i>

                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">

                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="#">
                                                <i class="bi bi-pencil me-2"></i>
                                                Edit
                                            </a>
                                        </li>

                                        <li>
                                            <a
                                                class="dropdown-item text-danger"
                                                href="#">
                                                <i class="bi bi-trash me-2"></i>
                                                Delete
                                            </a>
                                        </li>

                                    </ul>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No users found.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Pagination placeholder --}}

        <div class="d-flex justify-content-between align-items-center px-4 py-3">

            <div class="text-muted small">
                Showing {{ $users->firstItem() ?? 0 }}
                to {{ $users->lastItem() ?? 0 }}
                of {{ $users->total() }} users
            </div>

            <div>
                {{ $users->links() }}
            </div>

        </div>

    </div>

</div>

@endsection