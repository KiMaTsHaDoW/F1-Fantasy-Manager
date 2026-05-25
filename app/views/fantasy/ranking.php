<div class="page-header">
    <h1>🏆 Clasificación Global Fantasy</h1>
    <p>Los mejores equipos de toda la comunidad.</p>
</div>

<?php if (empty($ranking)): ?>
    <div class="card"><p>Aún no hay equipos registrados. ¡Sé el primero!</p>
    <a href="<?= BASE_URL ?>/auth/register" class="btn btn-primary">Crear cuenta</a></div>
<?php else: ?>
<div class="card">
    <table class="standings-table full-table">
        <thead>
            <tr><th>Pos</th><th>Gestor</th><th>Equipo</th><th>Prespuesto usado</th><th>Puntos</th></tr>
        </thead>
        <tbody>
        <?php foreach ($ranking as $i => $r): ?>
            <tr <?= ($i < 3) ? 'class="top-'.($i+1).'"' : '' ?>>
                <td class="pos">
                    <?php if ($i === 0): ?>🥇
                    <?php elseif ($i === 1): ?>🥈
                    <?php elseif ($i === 2): ?>🥉
                    <?php else: ?><?= $i+1 ?><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['username']) ?></td>
                <td><?= htmlspecialchars($r['team_name']) ?></td>
                <td><?= number_format($r['budget_used'], 1) ?>M€</td>
                <td class="pts"><?= number_format($r['total_points'], 1) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!isset($_SESSION['user_id'])): ?>
<div class="card mt-lg text-center">
    <p>¿Quieres aparecer aquí? <a href="<?= BASE_URL ?>/auth/register">Crea tu cuenta gratis</a></p>
</div>
<?php endif; ?>
