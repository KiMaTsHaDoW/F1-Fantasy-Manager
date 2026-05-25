<div class="page-header">
    <h1>👤 Mi Perfil</h1>
</div>

<div class="detail-page" style="max-width:520px">

    <div class="card mb-md">
        <h2 style="margin-bottom:1rem;font-size:1.1rem;color:var(--f1-muted);text-transform:uppercase;letter-spacing:.05em">Información de cuenta</h2>
        <div style="display:flex;flex-direction:column;gap:.75rem">
            <div>
                <span style="color:var(--f1-muted);font-size:.85rem">Nombre de usuario</span>
                <p style="font-size:1.2rem;font-weight:700;margin:.15rem 0 0"><?= htmlspecialchars($user['username']) ?></p>
            </div>
            <div>
                <span style="color:var(--f1-muted);font-size:.85rem">Email</span>
                <p style="margin:.15rem 0 0"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div>
                <span style="color:var(--f1-muted);font-size:.85rem">Miembro desde</span>
                <p style="margin:.15rem 0 0"><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>

    <div class="card mb-md">
        <h2 style="margin-bottom:1.25rem;font-size:1.1rem;color:var(--f1-muted);text-transform:uppercase;letter-spacing:.05em">Cambiar nombre de usuario</h2>
        <form method="POST" action="<?= BASE_URL ?>/profile/username">
            <div class="form-group">
                <label for="username">Nuevo nombre de usuario</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="30"
                       value="<?= htmlspecialchars($user['username']) ?>" autocomplete="username">
            </div>
            <div class="form-actions" style="margin-top:1.25rem">
                <button type="submit" class="btn btn-primary">Guardar nombre</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 style="margin-bottom:1.25rem;font-size:1.1rem;color:var(--f1-muted);text-transform:uppercase;letter-spacing:.05em">Cambiar contraseña</h2>
        <form method="POST" action="<?= BASE_URL ?>/profile/password">
            <div class="form-group">
                <label for="current_password">Contraseña actual</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="form-group">
                <label for="new_password">Nueva contraseña</label>
                <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar nueva contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-actions" style="margin-top:1.25rem">
                <button type="submit" class="btn btn-primary">Guardar contraseña</button>
            </div>
        </form>
    </div>

</div>
