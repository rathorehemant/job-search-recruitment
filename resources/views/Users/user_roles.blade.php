@extends('layouts.dashboard')

@section('title', 'User Roles')

@section('content')

<div class="container-fluid p-4">

    {{-- =========================================================
        Page Header
    ========================================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                User Roles
            </h4>

            <p class="text-muted mb-0">
                Manage system user roles and permissions.
            </p>

        </div>


        {{-- Add Role --}}
        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#roleModal"
            onclick="openCreateRoleModal()">

            <i class="bi bi-plus-lg me-1"></i>

            Add User Role

        </button>

    </div>


    {{-- =========================================================
        Flash Messages
    ========================================================== --}}

    @if(session('success'))

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if(session('error'))

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert">

            <i class="bi bi-exclamation-circle me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
        Roles Table
    ========================================================== --}}

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
                                Role
                            </th>

                            <th>
                                Permissions
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

                        @forelse($roles as $role)

                            <tr>

                                {{-- ID --}}
                                <td class="px-4">

                                    {{ $roles->firstItem() + $loop->index }}

                                </td>


                                {{-- Role --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $role->name }}

                                    </div>

                                </td>


                                {{-- Permissions --}}
                                <td>

                                    <span class="badge bg-primary-subtle text-primary">

                                        {{ $role->permissions->count() }}

                                        Permissions

                                    </span>

                                </td>


                                {{-- Created --}}
                                <td>

                                    {{ $role->created_at?->format('d M Y') }}

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

                                            {{-- Edit --}}
                                            <li>

                                                <a
                                                    href="#"
                                                    class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#roleModal"
                                                    onclick='openEditRoleModal(@json($role))'>

                                                    <i class="bi bi-pencil me-2"></i>

                                                    Edit

                                                </a>

                                            </li>

                                        </ul>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-5 text-muted">

                                    <i class="bi bi-shield-x fs-1 d-block mb-3"></i>

                                    <div class="fw-semibold mb-1">
                                        No user roles found.
                                    </div>

                                    <small>
                                        Create a role to manage user permissions.
                                    </small>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================================================
            Pagination
        ========================================================== --}}

        <div class="d-flex justify-content-between align-items-center px-4 py-3">

            <div class="text-muted small">

                Showing {{ $roles->firstItem() ?? 0 }}

                to {{ $roles->lastItem() ?? 0 }}

                of {{ $roles->total() }} user roles

            </div>


            <div>

                {{ $roles->links() }}

            </div>

        </div>

    </div>

</div>


{{-- =============================================================
    Role Modal
============================================================= --}}

<div
    class="modal fade"
    id="roleModal"
    tabindex="-1"
    aria-labelledby="roleModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            {{-- Modal Header --}}
            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title fw-bold"
                        id="roleModalLabel">

                        Add User Role

                    </h5>

                    <small
                        class="text-muted"
                        id="roleModalDescription">

                        Create a role and assign permissions.

                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            {{-- Form --}}
            <form
                id="roleForm"
                data-ajax-form
                action="{{ route('roles.store') }}"
                method="POST">

                @csrf

                <input
                    type="hidden"
                    name="_method"
                    id="roleFormMethod"
                    value="POST">


                <div class="modal-body">

                    {{-- Role Name --}}
                    <div class="mb-4">

                        <label
                            for="role_name"
                            class="form-label fw-semibold">

                            Role Name

                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="text"
                            name="name"
                            id="role_name"
                            class="form-control"
                            placeholder="e.g. Sales Manager"
                            data-label="Role Name"
                            required>

                    </div>


                    {{-- Permissions --}}
                    <div>

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div>

                                <label class="form-label fw-semibold mb-1">

                                    Permissions

                                </label>

                                <div class="small text-muted">

                                    Select the permissions available to this role.

                                </div>

                            </div>


                            <div>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-light border"
                                    onclick="selectAllPermissions()">

                                    Select All

                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-light border"
                                    onclick="clearAllPermissions()">

                                    Clear

                                </button>

                            </div>

                        </div>


                        <div class="border rounded p-3">

                            <div class="row g-3">

                                @forelse($permissions as $permission)

                                    <div class="col-md-6">

                                        <div class="form-check">

                                            <input
                                                class="form-check-input permission-checkbox"
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->id }}"
                                                id="permission_{{ $permission->id }}">

                                            <label
                                                class="form-check-label"
                                                for="permission_{{ $permission->id }}">

                                                {{ $permission->name }}

                                            </label>

                                        </div>

                                        <small class="text-muted ms-4">

                                            {{ $permission->slug }}

                                        </small>

                                    </div>

                                @empty

                                    <div class="col-12">

                                        <div class="alert alert-warning mb-0">

                                            No permissions available.

                                        </div>

                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Modal Footer --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="roleSubmitButton">

                        <i class="bi bi-check-lg me-1"></i>

                        Create Role

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | Create Role
    |--------------------------------------------------------------------------
    */

    function openCreateRoleModal()
    {
        const form = document.getElementById('roleForm');

        form.reset();

        form.action = "{{ route('roles.store') }}";

        document.getElementById('roleFormMethod').value = 'POST';

        document.getElementById('roleModalLabel').innerText =
            'Add User Role';

        document.getElementById('roleModalDescription').innerText =
            'Create a role and assign permissions.';

        document.getElementById('roleSubmitButton').innerHTML =
            '<i class="bi bi-check-lg me-1"></i> Create Role';

        clearAllPermissions();
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Role
    |--------------------------------------------------------------------------
    */

    function openEditRoleModal(role)
    {
        const form = document.getElementById('roleForm');

        form.action = `/roles/${role.id}`;

        document.getElementById('roleFormMethod').value = 'PUT';

        document.getElementById('roleModalLabel').innerText =
            'Edit User Role';

        document.getElementById('roleModalDescription').innerText =
            'Update role information and permissions.';

        document.getElementById('roleSubmitButton').innerHTML =
            '<i class="bi bi-check-lg me-1"></i> Update Role';


        /*
        |--------------------------------------------------------------------------
        | Role Name
        |--------------------------------------------------------------------------
        */

        document.getElementById('role_name').value =
            role.name ?? '';


        /*
        |--------------------------------------------------------------------------
        | Clear Existing Permissions
        |--------------------------------------------------------------------------
        */

        clearAllPermissions();


        /*
        Select Existing Permissions
       
        */

        if (role.permissions) {

            role.permissions.forEach(function(permission) {

                const checkbox = document.getElementById(
                    `permission_${permission.id}`
                );

                if (checkbox) {
                    checkbox.checked = true;
                }

            });

        }
    }


    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */

    function selectAllPermissions()
    {
        document
            .querySelectorAll('.permission-checkbox')
            .forEach(function(checkbox) {

                checkbox.checked = true;

            });
    }


    /*
    |--------------------------------------------------------------------------
    | Clear All
    |--------------------------------------------------------------------------
    */

    function clearAllPermissions()
    {
        document
            .querySelectorAll('.permission-checkbox')
            .forEach(function(checkbox) {

                checkbox.checked = false;

            });
    }

</script>

@endpush

@endsection