@extends('adminlte::page')

@section('title', 'Iniciar Sesión')

@push('css')
    {{-- Tipografía + colores + estilo visual heredado del dashboard --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-gradient, linear-gradient(135deg,#1a1a2e,#0f3460));
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .login-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
        }

        .login-card h3 {
            font-weight: 700;
            font-size: 1.8rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-label, .form-check-label, .text-link {
            color: #fff;
        }

        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .btn-primary {
            background-color: #4F46E5;
            border: none;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #3730a3;
        }

        .alert {
            background-color: rgba(255,0,0,0.2);
            color: #fff;
            border: 1px solid rgba(255,0,0,0.3);
        }

        .text-link a {
            color: #00d4ff;
            text-decoration: underline;
        }

        .logo {
            width: 90px;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">
        <div class="login-card text-center">
            <img src="{{ asset('images/logo2.png') }}" alt="Logo Tu Voto Cipote" class="logo mb-3">
            <h3 class="mb-4">Iniciar Sesión</h3>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="usuario@correo.com" required autofocus>
                </div>

                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="********" required>
                </div>

                <div class="mb-3 form-check text-start">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>

                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
            </form>

            <div class="text-link mt-3">
                ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
            </div>
        </div>
    </div>
@endsection
