<div class="sidebar" id="sidebar">

    {{-- =========================================================
        Sidebar Header
    ========================================================== --}}
    <div class="sidebar-header">

        <div class="brand-icon">
            L
        </div>

        <div class="brand-text">
            Lead Management
        </div>

        <button
            type="button"
            class="sidebar-close"
            id="sidebarClose">

            <i class="bi bi-x-lg"></i>

        </button>

    </div>


    {{-- =========================================================
        Sidebar Menu
    ========================================================== --}}
    <div class="sidebar-menu">

        {{-- =====================================================
            Main
        ====================================================== --}}

        @if(auth()->user()->hasPermission('dashboard.view'))

            <div class="menu-title">
                MAIN
            </div>

            {{-- Dashboard --}}
            <a
                href="{{ route('dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid-1x2"></i>

                <span>
                    Dashboard
                </span>

            </a>

        @endif


        {{-- =====================================================
            Management
        ====================================================== --}}

        @if(
            auth()->user()->hasPermission('users.view') ||
            auth()->user()->hasPermission('roles.view') ||
            auth()->user()->hasPermission('leads.view') ||
            auth()->user()->hasPermission('customers.view')
        )

            <div class="menu-title">
                MANAGEMENT
            </div>


            {{-- =================================================
                Users
            ================================================== --}}

            @if(auth()->user()->hasPermission('users.view'))

                <a
                    href="{{ route('users.index') }}"
                    class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">

                    <i class="bi bi-person-gear"></i>

                    <span>
                        Users
                    </span>

                </a>

            @endif


            {{-- =================================================
                User Roles
            ================================================== --}}

            @if(auth()->user()->hasPermission('roles.view'))

                <a
                    href="{{ route('users.role') }}"
                    class="sidebar-link {{ request()->routeIs('users.role') ? 'active' : '' }}">

                    <i class="bi bi-shield-lock"></i>

                    <span>
                        User Roles
                    </span>

                </a>

            @endif


            {{-- =================================================
                Leads
            ================================================== --}}

            @if(auth()->user()->hasPermission('leads.view'))

                <a
                    href="{{ route('leads.index') }}"
                    class="sidebar-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">

                    <i class="bi bi-person-lines-fill"></i>

                    <span>
                        Leads
                    </span>

                </a>

            @endif


            {{-- =================================================
                Customers
            ================================================== --}}

            @if(auth()->user()->hasPermission('customers.view'))

                <a
                    href="{{ route('customers.index') }}"
                    class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">

                    <i class="bi bi-people"></i>

                    <span>
                        Customers
                    </span>

                </a>

            @endif

        @endif

    </div>


    {{-- =========================================================
        Sidebar Footer
    ========================================================== --}}
    <div class="sidebar-footer">

        <div class="user-info">

            {{-- User Avatar --}}
            <div class="user-avatar">

                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

            </div>


            {{-- User Details --}}
            <div class="user-details">

                <div class="user-name">

                    {{ auth()->user()->name }}

                </div>

                <div class="user-role">

                    {{ auth()->user()->role?->name }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =============================================================
    Sidebar Overlay
============================================================= --}}
<div
    class="sidebar-overlay"
    id="sidebarOverlay">
</div>