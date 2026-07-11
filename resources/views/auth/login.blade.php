<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Moda — Iniciar sesión</title>
    @vite(['resources/sass/app.scss'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-base, #f5f5f5);
            font-family: var(--font-body, 'Inter', sans-serif);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-brand-name {
            font-size: 32px;
            font-weight: 800;
            color: #1f2125;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .login-brand-sub {
            font-size: 13px;
            color: #9e9e9e;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 36px 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .login-card h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2125;
            margin-bottom: 6px;
        }

        .login-card p {
            font-size: 13px;
            color: #9e9e9e;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            color: #1f2125;
            background: #fafafa;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1f2125;
            background: #ffffff;
        }

        .form-group input.is-invalid {
            border-color: #d32f2f;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #d32f2f;
            margin-top: 4px;
        }

        .form-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .form-remember input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .form-remember label {
            font-size: 13px;
            color: #777;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1f2125;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-login:hover {
            background: #2d3137;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #bbb;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-brand">
        <div class="login-brand-name">elite moda</div>
        <div class="login-brand-sub">Sistema de gestión</div>
    </div>

    <div class="login-card">
        <h1>Bienvenido</h1>
        <p>Ingresa tus credenciales para continuar</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                       placeholder="usuario@elitemoda.com"
                       autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="••••••••"
                       autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-remember">
                <input type="checkbox" id="remember" name="remember"
                       {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Recordarme</label>
            </div>

            <button type="submit" class="btn-login">
                Iniciar sesión
            </button>

        </form>
    </div>

    <div class="login-footer">
        Elite Moda © {{ date('Y') }}
    </div>

</div>

</body>
</html>