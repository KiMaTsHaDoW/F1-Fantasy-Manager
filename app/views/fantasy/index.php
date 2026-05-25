<div class="page-header">
    <div>
        <h1>🏆 <?= htmlspecialchars($team['team_name']) ?></h1>
        <p style="color:var(--f1-muted)">Liga: <strong style="color:var(--f1-text)"><?= htmlspecialchars($league['name']) ?></strong>
           &mdash; Presupuesto: <strong style="color:var(--f1-text)"><?= number_format($team['budget_used'], 1) ?>M€ / <?= BUDGET ?>M€</strong>
           &mdash; Puntos totales: <strong style="color:var(--f1-accent)"><?= number_format($team['total_points'], 1) ?></strong>
        </p>
    </div>
</div>

<?php
// Totales de la jornada para este equipo
$jornada_total = 0;
foreach ($myDrivers      as $d) $jornada_total += ($driverPtsRace[$d['id']] ?? 0) + ($driverPtsSprint[$d['id']] ?? 0);
foreach ($myConstructors as $t) $jornada_total += ($constrPtsRace[$t['id']] ?? 0) + ($constrPtsSprint[$t['id']] ?? 0);
$hasSprint = !empty($driverPtsSprint) || !empty($constrPtsSprint);
?>

<?php if ($lastRaceName): ?>
<div class="card mb-md" style="border-left:3px solid var(--f1-red)">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
        <span style="color:var(--f1-muted);font-size:.9rem">Última jornada — <strong style="color:var(--f1-text)"><?= htmlspecialchars($lastRaceName) ?></strong><?= $hasSprint ? ' (carrera + sprint)' : '' ?></span>
        <span style="font-size:1.4rem;font-weight:800;color:var(--f1-accent)">+<?= number_format($jornada_total, 1) ?> pts</span>
    </div>
</div>
<?php endif; ?>

<div class="grid-2">
    <div class="card">
        <h2 style="margin-bottom:1rem">Pilotos (<?= count($myDrivers) ?>/<?= MAX_DRIVERS ?>)</h2>
        <ul class="selection-list">
        <?php foreach ($myDrivers as $d):
            $ptsRace   = $driverPtsRace[$d['id']]   ?? 0;
            $ptsSprint = $driverPtsSprint[$d['id']] ?? 0;
            $ptsTotal  = $ptsRace + $ptsSprint;
        ?>
            <li class="selection-item">
                <span class="sel-code" style="background:<?= htmlspecialchars($d['team_color']) ?>"><?= htmlspecialchars($d['code']) ?></span>
                <span class="sel-name"><?= htmlspecialchars($d['forename'] . ' ' . $d['surname']) ?></span>
                <span class="sel-price" style="color:var(--f1-muted)"><?= number_format($d['price'], 1) ?>M€ &nbsp;·&nbsp; <?= number_format($driverSelPts[$d['id']] ?? 0, 1) ?> pts</span>
                <?php if ($lastRaceName): ?>
                <span class="sel-jornada">
                    <?php if ($hasSprint): ?>
                        <span title="Carrera"><?= number_format($ptsRace, 1) ?></span>
                        <span style="color:var(--f1-muted);font-size:.75rem">+</span>
                        <span title="Sprint" style="color:var(--f1-muted)"><?= number_format($ptsSprint, 1) ?></span>
                        <span style="color:var(--f1-muted);font-size:.75rem">=</span>
                    <?php endif; ?>
                    <strong style="color:<?= $ptsTotal > 0 ? 'var(--f1-accent)' : 'var(--f1-muted)' ?>"><?= number_format($ptsTotal, 1) ?> pts</strong>
                </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="card">
        <h2 style="margin-bottom:1rem">Constructores (<?= count($myConstructors) ?>/<?= MAX_TEAMS ?>)</h2>
        <ul class="selection-list">
        <?php foreach ($myConstructors as $t):
            $ptsRace   = $constrPtsRace[$t['id']]   ?? 0;
            $ptsSprint = $constrPtsSprint[$t['id']] ?? 0;
            $ptsTotal  = $ptsRace + $ptsSprint;
        ?>
            <li class="selection-item">
                <span class="sel-code" style="background:<?= htmlspecialchars($t['color']) ?>"> </span>
                <span class="sel-name"><?= htmlspecialchars($t['name']) ?></span>
                <span class="sel-price" style="color:var(--f1-muted)"><?= number_format($t['price'], 1) ?>M€ &nbsp;·&nbsp; <?= number_format($constrSelPts[$t['id']] ?? 0, 1) ?> pts</span>
                <?php if ($lastRaceName): ?>
                <span class="sel-jornada">
                    <?php if ($hasSprint): ?>
                        <span title="Carrera"><?= number_format($ptsRace, 1) ?></span>
                        <span style="color:var(--f1-muted);font-size:.75rem">+</span>
                        <span title="Sprint" style="color:var(--f1-muted)"><?= number_format($ptsSprint, 1) ?></span>
                        <span style="color:var(--f1-muted);font-size:.75rem">=</span>
                    <?php endif; ?>
                    <strong style="color:<?= $ptsTotal > 0 ? 'var(--f1-accent)' : 'var(--f1-muted)' ?>"><?= number_format($ptsTotal, 1) ?> pts</strong>
                </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card mt-lg">
    <div class="action-buttons">
        <a href="<?= BASE_URL ?>/fantasy/edit?league=<?= $league['id'] ?>" class="btn btn-primary">Editar equipo</a>
        <a href="<?= BASE_URL ?>/fantasy/ranking" class="btn btn-secondary">Ver clasificación</a>
        <a href="<?= BASE_URL ?>/drivers" class="btn btn-secondary">Ver pilotos</a>
    </div>
</div>
