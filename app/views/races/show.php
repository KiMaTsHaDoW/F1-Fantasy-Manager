<div class="page-header">
    <?php if ($race): ?>
    <h1><?= $type === 'sprint' ? '⚡' : '🏁' ?> <?= htmlspecialchars($race['raceName']) ?><?= $type === 'sprint' ? ' — Sprint' : '' ?></h1>
    <p><?= htmlspecialchars($race['Circuit']['circuitName'] ?? '') ?> &mdash; <?= htmlspecialchars($race['Circuit']['Location']['country'] ?? '') ?> &mdash; <?= htmlspecialchars($type === 'sprint' ? ($race['Sprint']['date'] ?? $race['date']) : $race['date']) ?></p>
    <?php else: ?>
    <h1>Carrera <?= htmlspecialchars($round) ?></h1>
    <?php endif; ?>
</div>

<?php if (empty($results)): ?>
    <div class="card"><p>Resultados no disponibles todavía para esta <?= $type === 'sprint' ? 'sprint' : 'carrera' ?>.</p></div>
<?php else: ?>
<div class="card">
    <h2>Resultados<?= $type === 'sprint' ? ' Sprint' : '' ?></h2>
    <table class="standings-table full-table">
        <thead>
            <tr><th>Pos</th><th>Piloto</th><th>Equipo</th><th>Tiempo/Estado</th><th>Puntos</th></tr>
        </thead>
        <tbody>
        <?php foreach ($results as $r): ?>
            <tr>
                <td class="pos"><?= $r['position'] ?></td>
                <td>
                    <strong><?= htmlspecialchars($r['Driver']['familyName']) ?></strong>
                    <small><?= htmlspecialchars($r['Driver']['givenName']) ?></small>
                </td>
                <td><?= htmlspecialchars($r['Constructor']['name']) ?></td>
                <td><?= htmlspecialchars($r['Time']['time'] ?? $r['status'] ?? '-') ?></td>
                <td class="pts"><?= $r['points'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<div class="action-buttons mt-md">
    <a href="<?= BASE_URL ?>/races" class="btn btn-secondary">← Volver al calendario</a>
    <?php if ($type === 'sprint'): ?>
        <a href="<?= BASE_URL ?>/races/show/<?= $round ?>" class="btn btn-secondary">Ver carrera</a>
    <?php elseif (!empty($race['Sprint'])): ?>
        <a href="<?= BASE_URL ?>/races/show/<?= $round ?>?type=sprint" class="btn btn-secondary">Ver sprint</a>
    <?php endif; ?>
</div>
