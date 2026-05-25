<div class="detail-page">
    <?php if ($driver): ?>
    <div class="detail-hero" style="border-left: 4px solid <?= htmlspecialchars($driver['team_color'] ?? '#cccccc') ?>">
        <?php if (!empty($driver['headshot'])): ?>
        <img src="<?= htmlspecialchars($driver['headshot']) ?>" alt="<?= htmlspecialchars($driver['forename']) ?>" class="driver-headshot">
        <?php endif; ?>
        <div class="detail-number"><?= htmlspecialchars($driver['number'] ?? '?') ?></div>
        <div class="detail-info">
            <h1><?= htmlspecialchars($driver['forename'] . ' ' . $driver['surname']) ?></h1>
            <?php if (!empty($driver['team'])): ?>
            <p class="detail-team" style="color: <?= htmlspecialchars($driver['team_color'] ?? '#cccccc') ?>">
                <?= htmlspecialchars($driver['team']) ?>
            </p>
            <?php endif; ?>
            <p class="detail-nat">🌍 <?= htmlspecialchars($driver['nationality']) ?></p>
            <?php if ($position): ?>
                <div class="badge-row">
                    <span class="badge badge-pos">P<?= $position ?></span>
                    <span class="badge badge-pts"><?= $points ?> puntos</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="detail-stats">
        <div class="stat"><span class="stat-label">Código</span><span class="stat-value"><?= htmlspecialchars($driver['code'] ?? '') ?></span></div>
        <div class="stat"><span class="stat-label">Número</span><span class="stat-value">#<?= htmlspecialchars($driver['number'] ?? '?') ?></span></div>
        <div class="stat"><span class="stat-label">Fecha nacimiento</span><span class="stat-value"><?= htmlspecialchars($driver['dob'] ?? '-') ?></span></div>
        <div class="stat"><span class="stat-label">Nacionalidad</span><span class="stat-value"><?= htmlspecialchars($driver['nationality'] ?? '-') ?></span></div>
        <div class="stat"><span class="stat-label">Precio fantasy</span><span class="stat-value">💰 <?= number_format($driver['price'] ?? 0, 1) ?>M€</span></div>
        <?php if (!empty($driver['url'])): ?>
        <div class="stat"><span class="stat-label">Wikipedia</span><span class="stat-value"><a href="<?= htmlspecialchars($driver['url']) ?>" target="_blank">Ver perfil</a></span></div>
        <?php endif; ?>
    </div>
    <?php else: ?>
        <p>Piloto no encontrado.</p>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/drivers" class="btn btn-secondary mt-md">← Volver a pilotos</a>
</div>
