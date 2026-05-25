<div class="page-header">
    <h1>+ Crear Liga</h1>
</div>

<div class="card" style="max-width:520px">
    <form method="POST" action="<?= BASE_URL ?>/leagues/store">
        <div class="form-group">
            <label for="name">Nombre de la liga *</label>
            <input type="text" id="name" name="name" maxlength="100" required placeholder="Ej: Liga de amigos">
        </div>
        <div class="form-group">
            <label for="description">Descripción (opcional)</label>
            <input type="text" id="description" name="description" maxlength="255" placeholder="Breve descripción">
        </div>
        <div class="form-group form-check">
            <input type="checkbox" id="is_public" name="is_public" checked>
            <label for="is_public">Liga pública (cualquiera puede unirse)</label>
        </div>
        <p class="help-text">Las ligas privadas solo se pueden unir con el código de invitación.</p>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear liga</button>
            <a href="<?= BASE_URL ?>/leagues" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
