@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-3">
                {{-- Header --}}
                <div class="card-header bg-primary text-white text-center py-4 rounded-top">
                    <div class="mb-3">
                        <i class="fas fa-warehouse fs-1"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">Inventory SK</h3>
                    <p class="mb-0 opacity-75">Sistem Manajemen Inventory</p>
                </div>

                {{-- Body --}}
                <div class="card-body p-4">
                    <h4 class="text-center mb-4 text-dark fw-semibold">Login Sistem</h4>
                    
                    {{-- Alert untuk error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Alert untuk success --}}
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Form Login --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Input Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-primary"></i>Nama
                            </label>
                            <input id="name" 
                                   type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Masukkan nama Anda"
                                   required 
                                   autocomplete="name" 
                                   autofocus>
                            
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Input Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-primary"></i>Password
                            </label>
                            <div class="input-group">
                                <input id="password" 
                                       type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required 
                                       autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    {{-- Info --}}
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Khusus untuk karyawan PT. Sumatera Kemasindo
                        </small>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer text-center py-3 bg-light">
                    <small class="text-muted">
                        © {{ date('Y') }} PT. Sumatera Kemasindo - Sistem Inventory
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Scripts --}}
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });

    // Auto dismiss alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-success')) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        });
    }, 5000);
</script>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .card {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .form-control-lg {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        transform: translateY(-2px);
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }

    .input-group .btn {
        border-left: none;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    .alert {
        border-radius: 0.5rem;
        border: none;
    }

    .card-header {
        border-bottom: none;
    }

    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0 0 0.375rem 0.375rem !important;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
@endsection