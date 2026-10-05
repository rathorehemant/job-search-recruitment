@extends('layouts.dashboard')

@section('title', 'Customers')

@section('content')

<div class="container-fluid p-4">

    {{-- =========================================================
        Page Header
    ========================================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Customers
            </h4>

            <p class="text-muted mb-0">
                Manage converted customers.
            </p>
        </div>

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
                data-bs-dismiss="alert"
                aria-label="Close">
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
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>
    @endif


    {{-- =========================================================
        Search
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('customers.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- Search --}}
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
                                placeholder="Name, email, phone or company">

                        </div>

                    </div>


                    {{-- Search Button --}}
                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            <i class="bi bi-search me-1"></i>

                            Search

                        </button>

                    </div>


                    {{-- Clear Button --}}
                    @if(request()->filled('search'))

                        <div class="col-md-2">

                            <a
                                href="{{ route('customers.index') }}"
                                class="btn btn-light border w-100">

                                <i class="bi bi-x-lg me-1"></i>

                                Clear

                            </a>

                        </div>

                    @endif

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        Customers Table
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
                                Customer
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Company
                            </th>

                            <th>
                                Created
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($customers as $customer)

                            <tr>

                                {{-- =================================================
                                    Serial Number
                                ================================================== --}}
                                <td class="px-4">

                                    {{ $customers->firstItem() + $loop->index }}

                                </td>


                                {{-- =================================================
                                    Customer
                                ================================================== --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $customer->name }}

                                    </div>

                                </td>


                                {{-- =================================================
                                    Contact
                                ================================================== --}}
                                <td>

                                    <div>
                                        {{ $customer->email }}
                                    </div>

                                    @if($customer->phone)

                                        <small class="text-muted">

                                            {{ $customer->phone }}

                                        </small>

                                    @else

                                        <small class="text-muted">
                                            No phone
                                        </small>

                                    @endif

                                </td>


                                {{-- =================================================
                                    Company
                                ================================================== --}}
                                <td>

                                    {{ $customer->company ?? '-' }}

                                </td>


                                {{-- =================================================
                                    Created
                                ================================================== --}}
                                <td>

                                    <span class="text-muted">

                                        {{ $customer->created_at?->format('d M Y') }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            {{-- =================================================
                                Empty State
                            ================================================== --}}

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i
                                            class="bi bi-people fs-1 d-block mb-3">
                                        </i>

                                        <div class="fw-semibold mb-1">
                                            No customers found
                                        </div>

                                        <small>
                                            Customers will appear here after a
                                            lead is converted successfully.
                                        </small>

                                    </div>

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

                Showing {{ $customers->firstItem() ?? 0 }}

                to {{ $customers->lastItem() ?? 0 }}

                of {{ $customers->total() }} customers

            </div>

            <div>

                {{ $customers->links() }}

            </div>

        </div>

    </div>

</div>

@endsection