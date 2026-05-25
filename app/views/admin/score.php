<div class="page-header">
    <h1>🏆 Puntuar Carrera</h1>
    <a href="<?= BASE_URL ?>/admin/recalculate" class="btn btn-secondary">Recalcular precios</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($result): ?>
    <div class="alert alert-success">
        ✅ Ronda <?= $round ?> (<?= htmlspecialchars($type ?? 'race') ?>) puntuada correctamente — <?= htmlspecialchars($raceName) ?>
    </div>
    <div class="card mt-md">
        <h2 style="margin-bottom:1rem">Puntos repartidos</h2>
        <div class="ranking-table">
            <div class="ranking-header">
                <span>Equipo</span>
                <span style="text-align:right">Puntos esta ronda</span>
            </div>
            <?php usort($result, fn($a,$b) => $b['points'] <=> $a['points']); ?>
            <?php foreach ($result as $row): ?>
            <div class="ranking-row">
                <span><?= htmlspecialchars($row['team_name']) ?></span>
                <span class="rank-pts" style="text-align:right">+<?= number_format($row['points'], 1) ?> pts</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <a href="<?= BASE_URL ?>/admin/score" class="btn btn-secondary mt-md">← Volver</a>

<?php elseif ($round): ?>
    <div class="card">
        <p style="color:var(--f1-muted);margin-bottom:1.25rem">
            Última carrera disponible en la API: <strong><?= htmlspecialchars($raceName) ?></strong>
            — Temporada <?= htmlspecialchars($season) ?>, Ronda <?= $round ?>
        </p>

        <?php if (!$raceScored): ?>
        <form method="POST" action="<?= BASE_URL ?>/admin/score" style="display:inline">
            <input type="hidden" name="type" value="race">
            <button type="submit" class="btn btn-primary">
                ✅ Puntuar carrera ronda <?= $round ?>
            </button>
        </form>
        <?php else: ?>
        <span class="badge" style="background:var(--f1-accent);color:#000;padding:.5rem 1rem">
            ✓ Carrera ronda <?= $round ?> ya puntuada
        </span>
        <?php endif; ?>

        <?php if ($hasSprint): ?>
            <div style="margin-top:1rem">
            <?php if (!$sprintScored): ?>
            <form method="POST" action="<?= BASE_URL ?>/admin/score" style="display:inline">
                <input type="hidden" name="type" value="sprint">
                <button type="submit" class="btn btn-primary">
                    ⚡ Puntuar sprint ronda <?= $round ?>
                </button>
            </form>
            <?php else: ?>
            <span class="badge" style="background:var(--f1-accent);color:#000;padding:.5rem 1rem">
                ✓ Sprint ronda <?= $round ?> ya puntuado
            </span>
            <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card"><p style="color:var(--f1-muted)">No hay datos de carrera disponibles en la API.</p></div>
<?php endif; ?>
