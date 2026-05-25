<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">🏎️</div>
        <h1>Crear cuenta</h1>
        <form method="POST" action="<?= BASE_URL ?>/auth/register" class="auth-form">
            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" required placeholder="Ej: SpeedMaster99">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repite la contraseña">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Crear cuenta</button>
        </form>
        <p class="auth-link">¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/auth/login">Inicia sesión</a></p>
    </div>
</div>
