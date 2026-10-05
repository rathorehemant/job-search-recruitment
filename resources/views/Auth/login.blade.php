<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | Lead Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            min-height: 100vh;
            background: #f5f7fb;
        }

        .login-wrapper {
            min-height: 100vh;
        }

        .login-card {
            max-width: 430px;
            width: 100%;
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .brand-icon {
            width: 55px;
            height: 55px;
            background: #0d6efd;
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            margin: 0 auto 15px;
        }

        .form-control {
            padding: 12px 14px;
            border-radius: 8px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
        }

        .btn-login {
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center login-wrapper">

            <div class="col-12">

                <div class="card login-card mx-auto">

                    <div class="card-body p-4 p-md-5">

                        <!-- Brand -->
                        <div class="text-center mb-4">

                            <div class="brand-icon">
                                L
                            </div>

                            <h3 class="fw-bold mb-1">
                                Welcome Back
                            </h3>

                        </div>

                        <!-- Error Message -->
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                         <!-- Success Message -->
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif

                        <!-- Validation Errors -->
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form id="loginForm" method="POST" action="{{ route('login') }}" data-ajax-form>

                            @csrf

                            <!-- Email -->
                            <div class="mb-3">

                                <label for="email" class="form-label fw-semibold">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your email"
                                    autocomplete="email"
                                    required
                                    data-required-message="Please enter your email address.">

                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                            <!-- Password -->
                            <div class="mb-3">

                                <label for="password" class="form-label fw-semibold">
                                    Password
                                </label>

                                <div class="position-relative">

                                    <input
                                        type="password"
                                        class="form-control pe-5"
                                        id="password"
                                        name="password"
                                        placeholder="Enter your password"
                                        autocomplete="current-password"
                                        required
                                        data-required-message="Please enter your password.">

                                    <button
                                        type="button"
                                        class="btn position-absolute top-50 end-0 translate-middle-y me-1 border-0"
                                        id="togglePassword"
                                        aria-label="Show password">
                                        <i class="bi bi-eye" id="passwordIcon"></i>
                                    </button>

                                </div>

                            </div>

                            <!-- Remember -->
                            <div class="d-flex justify-content-between align-items-center mb-4">

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="remember"
                                        id="remember">

                                    <label class="form-check-label text-muted" for="remember">
                                        Remember me
                                    </label>
                                </div>

                            </div>

                            <!-- Login Button -->
                            <button
                                type="submit"
                                class="btn btn-primary btn-login w-100">
                                Sign In
                            </button>

                        </form>

                    </div>

                </div>

                <p class="text-center text-muted mt-4 small">
                    Lead Management System
                </p>

            </div>

        </div>
    </div>

</body>
<script>
    const togglePassword =
        document.getElementById('togglePassword');

    const password =
        document.getElementById('password');

    const passwordIcon =
        document.getElementById('passwordIcon');

    if (togglePassword && password && passwordIcon) {

        togglePassword.addEventListener('click', () => {

            const isPassword =
                password.type === 'password';

            password.type =
                isPassword ? 'text' : 'password';

            passwordIcon.classList.toggle(
                'bi-eye',
                !isPassword
            );

            passwordIcon.classList.toggle(
                'bi-eye-slash',
                isPassword
            );

            togglePassword.setAttribute(
                'aria-label',
                isPassword ?
                'Hide password' :
                'Show password'
            );
        });
    }
</script>

</html>