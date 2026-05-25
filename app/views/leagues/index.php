<div class="page-header">
    <div>
        <h1>🏆 Ligas</h1>
        <p style="color:var(--f1-muted)">Compite con amigos en ligas privadas o únete a ligas públicas.</p>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="page-header-actions">
            <a href="<?= BASE_URL ?>/leagues/create" class="btn btn-primary">+ Crear liga</a>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="card mb-md">
    <h2 style="font-size:1.05rem;margin-bottom:.75rem">Unirse por código de invitación</h2>
    <form method="POST" action="<?= BASE_URL ?>/leagues/join-code" class="form-inline">
        <input type="text" name="invite_code" placeholder="Código (ej: A1B2C3D4)" maxlength="12" required class="input-code">
        <button type="submit" class="btn btn-secondary">Unirse</button>
    </form>
</div>

<?php if (!empty($myLeagues)): ?>
<section class="mb-lg">
    <h2 style="margin-bottom:1rem">Mis ligas</h2>
    <div class="leagues-grid">
        <?php foreach ($myLeagues as $l): ?>
        <a href="<?= BASE_URL ?>/leagues/show/<?= $l['id'] ?>" class="league-card league-card--mine">
            <div class="league-badge"><?= $l['is_public'] ? '🌍 Pública' : '🔒 Privada' ?></div>
            <h3><?= htmlspecialchars($l['name']) ?></h3>
            <?php if ($l['description']): ?>
                <p class="league-desc"><?= htmlspecialchars($l['description']) ?></p>
            <?php endif; ?>
            <div class="league-meta">
                <span>👤 <?= $l['member_count'] ?> miembros</span>
                <span>Creada por <?= htmlspecialchars($l['creator_name']) ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<section>
    <h2 style="margin-bottom:1rem">Ligas públicas</h2>
    <?php if (empty($public)): ?>
        <div class="card"><p style="color:var(--f1-muted)">No hay ligas públicas todavía. ¡Crea la primera!</p></div>
    <?php else: ?>
    <div class="leagues-grid">
        <?php foreach ($public as $l): ?>
        <?php $joined = in_array($l['id'], $myIds ?? []); ?>
        <div class="league-card">
            <div class="league-badge">🌍 Pública</div>
            <h3><?= htmlspecialchars($l['name']) ?></h3>
            <?php if ($l['description']): ?>
                <p class="league-desc"><?= htmlspecialchars($l['description']) ?></p>
            <?php endif; ?>
            <div class="league-meta">
                <span>👤 <?= $l['member_count'] ?> miembros</span>
                <span>Creada por <?= htmlspecialchars($l['creator_name']) ?></span>
            </div>
            <div class="league-actions">
                <a href="<?= BASE_URL ?>/leagues/show/<?= $l['id'] ?>" class="btn btn-secondary btn-sm">Ver</a>
                <?php if (isset($_SESSION['user_id']) && !$joined): ?>
                    <a href="<?= BASE_URL ?>/leagues/join/<?= $l['id'] ?>" class="btn btn-primary btn-sm">Unirse</a>
                <?php elseif ($joined): ?>
                    <span class="badge badge-pos" style="font-size:.8rem;padding:.3rem .75rem">✓ Eres miembro</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
