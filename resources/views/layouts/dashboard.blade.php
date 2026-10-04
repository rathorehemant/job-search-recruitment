<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Dashboard') | Lead Management System
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite([
        'resources/css/dashboard.css',
        'resources/js/app.js'
    ])

    @stack('styles')
</head>

<body>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main Content --}}
    <main class="dashboard-content">

        {{-- Mobile Header --}}
        <div class="mobile-header">

            <button
                type="button"
                class="btn btn-light"
                id="sidebarToggle">

                <i class="bi bi-list"></i>

            </button>

            <span class="fw-semibold">
                Lead Management
            </span>

        </div>

        {{-- Page Content --}}
        @yield('content')

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const close = document.getElementById('sidebarClose');

            function openSidebar() {

                if (!sidebar) {
                    return;
                }

                sidebar.classList.add('show');

                if (overlay) {
                    overlay.classList.add('show');
                }

                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {

                if (!sidebar) {
                    return;
                }

                sidebar.classList.remove('show');

                if (overlay) {
                    overlay.classList.remove('show');
                }

                document.body.style.overflow = '';
            }

            if (toggle) {
                toggle.addEventListener('click', openSidebar);
            }

            if (close) {
                close.addEventListener('click', closeSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

        });
    </script>

    @stack('scripts')

</body>

</html>