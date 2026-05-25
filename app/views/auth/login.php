<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">🏎️</div>
        <h1>Iniciar sesión</h1>
        <form method="POST" action="<?= BASE_URL ?>/auth/login" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Entrar</button>
        </form>
        <p class="auth-link">¿No tienes cuenta? <a href="<?= BASE_URL ?>/auth/register">Regístrate</a></p>
    </div>
</div>
