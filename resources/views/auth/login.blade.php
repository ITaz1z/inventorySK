@extends('layouts.app')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-10 col-lg-8 col-xl-6">
                <div class="login-card">
                    <!-- Header -->
                    <div class="login-header">
                        <div class="brand-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <h2 class="brand-title">Inventory SK</h2>
                        <p class="brand-subtitle">PT. Sumatera Kemasindo</p>
                    </div>

                    <!-- Alerts -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        <!-- Nama Input -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Nama
                            </label>
                            <input id="name" 
                                   type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Masukkan nama Anda"
                                   required 
                                   autocomplete="name" 
                                   autofocus>
                            
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i>
                                Password
                            </label>
                            <div class="password-wrapper">
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required 
                                       autocomplete="current-password">
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check-wrapper">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Masuk
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="login-footer">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Sistem aman dan terpercaya
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Variables */
:root {
    --primary-blue: #2563eb;
    --light-blue: #eff6ff;
    --border-blue: #dbeafe;
    --text-blue: #1e40af;
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-400: #9ca3af;
    --gray-600: #4b5563;
    --gray-900: #111827;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Base Styles */
.login-page {
    background: linear-gradient(135deg, var(--light-blue) 0%, var(--white) 50%, var(--light-blue) 100%);
    min-height: 100vh;
    font-family: system-ui, -apple-system, sans-serif;
}

/* Login Card */
.login-card {
    background: var(--white);
    border-radius: 16px;
    padding: 3rem 2.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-blue);
    max-width: 420px;
    margin: 0 auto;
}

/* Header */
.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.brand-icon {
    width: 64px;
    height: 64px;
    background: var(--primary-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.75rem;
}

.brand-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.brand-subtitle {
    color: var(--gray-600);
    font-size: 0.9rem;
    margin: 0;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    border-left: 4px solid;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border-left-color: #dc2626;
}

.alert-success {
    background: #f0fdf4;
    color: #16a34a;
    border-left-color: #16a34a;
}

/* Form Styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-700);
    font-weight: 500;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.form-label i {
    color: var(--primary-blue);
    width: 16px;
    text-align: center;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-100);
    border-radius: 8px;
    background: var(--white);
    transition: all 0.2s ease;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control.is-invalid {
    border-color: #dc2626;
}

.invalid-feedback {
    display: block;
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Password Field */
.password-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--gray-400);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: color 0.2s ease;
}

.password-toggle:hover {
    color: var(--primary-blue);
}

/* Checkbox */
.form-check-wrapper {
    margin-bottom: 1.5rem;
}

.form-check {
    display: flex;
    align-items: center;
}

.form-check-input {
    width: 18px;
    height: 18px;
    margin-right: 0.5rem;
    accent-color: var(--primary-blue);
}

.form-check-label {
    color: var(--gray-600);
    font-size: 0.9rem;
    cursor: pointer;
}

/* Login Button */
.btn-login {
    width: 100%;
    background: var(--primary-blue);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.875rem 1rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 1.5rem;
}

.btn-login:hover {
    background: var(--text-blue);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

/* Footer */
.login-footer {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-100);
}

.login-footer small {
    color: var(--gray-400);
    font-size: 0.8rem;
}

.login-footer i {
    color: var(--primary-blue);
}

/* Responsive */
@media (max-width: 768px) {
    .login-card {
        margin: 1rem;
        padding: 2rem 1.5rem;
    }
    
    .brand-title {
        font-size: 1.5rem;
    }
    
    .brand-icon {
        width: 56px;
        height: 56px;
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .login-card {
        padding: 1.5rem 1rem;
    }
}

/* Simple animations */
.login-card {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control:focus {
    animation: focusGlow 0.3s ease-out;
}

@keyframes focusGlow {
    0% {
        box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.2);
    }
    100% {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Auto dismiss success alerts
    setTimeout(function() {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-10px)';
            setTimeout(() => successAlert.remove(), 300);
        }
    }, 4000);

    // Simple form validation visual feedback
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() && this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endsection