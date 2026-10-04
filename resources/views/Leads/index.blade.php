@extends('layouts.dashboard')

@section('title', 'Leads')

@section('content')

<div class="container-fluid p-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Leads
            </h4>

            <p class="text-muted mb-0">
                Manage leads and follow-ups.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#leadModal"
            onclick="openCreateLeadModal()">

            <i class="bi bi-plus-lg me-1"></i>
            Add Lead
        </button>

    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close">
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close">
        </button>
    </div>
    @endif


    {{-- Search & Filters --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('leads.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- Search --}}
                    <div class="col-md-6 col-lg-4">

                        <label for="search" class="form-label fw-semibold">
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
                                placeholder="Name, email, phone or company">

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-3 col-lg-2">

                        <label for="status" class="form-label fw-semibold">
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select">

                            <option value="">All Status</option>

                            @foreach(['New', 'In Progress', 'Won', 'Lost'] as $status)

                            <option
                                value="{{ $status }}"
                                {{ request('status') === $status ? 'selected' : '' }}>

                                {{ $status }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Source --}}
                    <div class="col-md-3 col-lg-2">

                        <label for="source" class="form-label fw-semibold">
                            Source
                        </label>

                        <select
                            name="source"
                            id="source"
                            class="form-select">

                            <option value="">All Sources</option>

                            @foreach(['Web', 'Ads', 'Referral'] as $source)

                            <option
                                value="{{ $source }}"
                                {{ request('source') === $source ? 'selected' : '' }}>

                                {{ $source }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Search Button --}}
                    <div class="col-md-2 col-lg-1">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            <i class="bi bi-search"></i>

                        </button>

                    </div>


                    {{-- Clear --}}
                    <div class="col-md-2 col-lg-1">

                        @if(request()->hasAny(['search', 'status', 'source', 'assigned_to']))

                        <a
                            href="{{ route('leads.index') }}"
                            class="btn btn-light border w-100">

                            Clear

                        </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Leads Table --}}
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
                                Lead
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Company
                            </th>

                            <th>
                                Source
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Assigned To
                            </th>

                            <th>
                                Follow-up
                            </th>

                            <th class="text-end px-4">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($leads as $lead)

                        <tr>

                            {{-- ID --}}
                            <td class="px-4">
                                {{ $leads->firstItem() + $loop->index }}
                            </td>


                            {{-- Lead --}}
                            <td>

                                <div class="fw-semibold">
                                    {{ $lead->name }}
                                </div>

                            </td>


                            {{-- Contact --}}
                            <td>

                                <div>
                                    {{ $lead->email }}
                                </div>

                                @if($lead->phone)
                                <small class="text-muted">
                                    {{ $lead->phone }}
                                </small>
                                @endif

                            </td>


                            {{-- Company --}}
                            <td>
                                {{ $lead->company ?? '-' }}
                            </td>


                            {{-- Source --}}
                            <td>

                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $lead->source }}
                                </span>

                            </td>


                            {{-- Status --}}
                            <td>

                                @php
                                $statusClass = match($lead->status) {
                                'New' => 'bg-primary-subtle text-primary',
                                'In Progress' => 'bg-warning-subtle text-warning',
                                'Won' => 'bg-success-subtle text-success',
                                'Lost' => 'bg-danger-subtle text-danger',
                                default => 'bg-secondary-subtle text-secondary',
                                };
                                @endphp

                                <span class="badge {{ $statusClass }}">
                                    {{ $lead->status }}
                                </span>

                            </td>


                            {{-- Assigned To --}}
                            <td>
                                {{ $lead->assignedUser?->name ?? 'Unassigned' }}
                            </td>


                            {{-- Follow-up --}}
                            <td>

                                @if($lead->follow_up_date)

                                {{ $lead->follow_up_date->format('d M Y') }}

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

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
                                                href="#"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#leadModal"
                                                onclick='openEditLeadModal(@json($lead))'>

                                                <i class="bi bi-pencil me-2"></i>
                                                Edit

                                            </a>
                                        </li>

                                        <li>

                                            <form
                                                action="{{ route('leads.destroy', $lead) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this lead?');">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="dropdown-item text-danger">

                                                    <i class="bi bi-trash me-2"></i>
                                                    Delete

                                                </button>

                                            </form>

                                        </li>

                                    </ul>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="9"
                                class="text-center py-5 text-muted">

                                No leads found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}

        <div class="d-flex justify-content-between align-items-center px-4 py-3">

            <div class="text-muted small">
                Showing {{ $leads->firstItem() ?? 0 }}
                to {{ $leads->lastItem() ?? 0 }}
                of {{ $leads->total() }} leads
            </div>

            <div>
                {{ $leads->links() }}
            </div>

        </div>

    </div>

</div>
<div
    class="modal fade"
    id="leadModal"
    tabindex="-1"
    aria-labelledby="leadModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">

                <div>
                    <h5 class="modal-title fw-bold" id="leadModalLabel">
                        Add New Lead
                    </h5>

                    <small class="text-muted" id="leadModalDescription">
                        Add a new lead to the system.
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <form
                id="leadForm"
                data-ajax-form
                action="{{ route('leads.store') }}"
                method="POST">

                @csrf

                <input
                    type="hidden"
                    name="_method"
                    id="leadFormMethod"
                    value="POST">

                <div class="modal-body">

                    <div class="row g-3">

                        {{-- Name --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="lead_name"
                                class="form-control"
                                data-label="Name"
                                required>

                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="lead_email"
                                class="form-control"
                                data-label="Email"
                                required>

                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="lead_phone"
                                data-label="Phone"
                                data-validate="phone"
                                class="form-control">

                        </div>

                        {{-- Company --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Company
                            </label>

                            <input
                                type="text"
                                name="company"
                                id="lead_company"
                                data-label="Company"
                                class="form-control">

                        </div>

                        {{-- Source --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Source <span class="text-danger">*</span>
                            </label>

                            <select
                                name="source"
                                id="lead_source"
                                class="form-select"
                                data-label="Source"
                                required>

                                <option value="">Select Source</option>
                                <option value="Web">Web</option>
                                <option value="Ads">Ads</option>
                                <option value="Referral">Referral</option>

                            </select>

                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select
                                name="status"
                                id="lead_status"
                                class="form-select"
                                data-label="Status"
                                required>

                                <option value="New">New</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Won">Won</option>
                                <option value="Lost">Lost</option>

                            </select>

                        </div>

                        {{-- Assigned To --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Assigned To
                            </label>

                            <select
                                name="assigned_to"
                                id="lead_assigned_to"
                                data-label="Assigned To"
                                required
                                class="form-select">

                                <option value="">
                                    Select Sales User
                                </option>

                                @foreach($users as $user)

                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Follow Up --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Follow-up Date
                            </label>

                            <input
                                type="date"
                                name="follow_up_date"
                                id="lead_follow_up_date"
                                data-label="Follow-up Date"
                                class="form-control">

                        </div>

                        {{-- Notes --}}
                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                id="lead_notes"
                                rows="4"
                                data-label="Notes"
                                class="form-control"></textarea>

                        </div>

                    </div>

                </div>

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
                        id="leadSubmitButton">

                        <i class="bi bi-check-lg me-1"></i>
                        Create Lead

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function openCreateLeadModal() {
        const form = document.getElementById('leadForm');

        form.reset();

        form.action = "{{ route('leads.store') }}";

        document.getElementById('leadFormMethod').value = 'POST';

        document.getElementById('leadModalLabel').innerText = 'Add New Lead';

        document.getElementById('leadModalDescription').innerText =
            'Add a new lead to the system.';

        document.getElementById('leadSubmitButton').innerHTML =
            '<i class="bi bi-check-lg me-1"></i> Create Lead';
    }

    function openEditLeadModal(lead) {
        const form = document.getElementById('leadForm');

        form.action = `/leads/${lead.id}`;

        document.getElementById('leadFormMethod').value = 'PUT';

        document.getElementById('leadModalLabel').innerText = 'Edit Lead';

        document.getElementById('leadModalDescription').innerText =
            'Update lead information.';

        document.getElementById('leadSubmitButton').innerHTML =
            '<i class="bi bi-check-lg me-1"></i> Update Lead';

        document.getElementById('lead_name').value = lead.name ?? '';
        document.getElementById('lead_email').value = lead.email ?? '';
        document.getElementById('lead_phone').value = lead.phone ?? '';
        document.getElementById('lead_company').value = lead.company ?? '';
        document.getElementById('lead_source').value = lead.source ?? '';
        document.getElementById('lead_status').value = lead.status ?? 'New';
        document.getElementById('lead_assigned_to').value = lead.assigned_to ?? '';
        document.getElementById('lead_follow_up_date').value =
            lead.follow_up_date ? lead.follow_up_date.substring(0, 10) : '';
        document.getElementById('lead_notes').value = lead.notes ?? '';
    }
</script>
@endpush

@endsection