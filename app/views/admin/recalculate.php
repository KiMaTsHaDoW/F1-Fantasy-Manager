<div class="page-header">
    <h1>⚙️ Recalcular Precios de Mercado</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($result === null): ?>
<div class="card" style="max-width:560px">
    <h2 style="margin-bottom:.75rem;font-size:1.1rem">Ejecutar recalculación</h2>

    <?php if ($raceName): ?>
    <p style="color:var(--f1-muted);margin-bottom:.75rem;font-size:.9rem">
        Última carrera: <strong style="color:var(--f1-text)"><?= htmlspecialchars($raceName) ?></strong> — Ronda <?= $round ?>
    </p>
    <?php endif; ?>

    <?php if ($alreadyDone): ?>
    <div class="alert alert-error" style="margin-bottom:1rem">
        Los precios de esta ronda ya fueron recalculados. Solo se puede recalcular una vez por carrera.
    </div>
    <a href="<?= BASE_URL ?>/" class="btn btn-secondary">Volver al inicio</a>

    <?php else: ?>
    <p style="color:var(--f1-muted);margin-bottom:1.25rem;font-size:.9rem">
        Cargará los resultados de la última carrera, aplicará la fórmula de precios y guardará los nuevos valores.
    </p>
    <div style="background:var(--f1-dark);border:1px solid var(--f1-border);border-radius:8px;padding:1rem;margin-bottom:1.25rem;font-size:.85rem;color:var(--f1-muted)">
        <strong style="color:var(--f1-text)">Fórmula:</strong><br>
        ΔP_perf = 0.05 × (puntos_reales − precio × 0.7)<br>
        ΔP_mkt &nbsp;= 1.5 × (compras − ventas) / usuarios<br>
        ΔP_total = clamp(ΔP_perf + ΔP_mkt, −1.5, +1.5)<br>
        Constructores: ΔP × 0.8 (amortiguador)<br>
        Límites: 4.0M€ — 35.0M€
    </div>
    <form method="POST" action="<?= BASE_URL ?>/admin/recalculate">
        <button type="submit" class="btn btn-primary">Recalcular ahora</button>
        <a href="<?= BASE_URL ?>/" class="btn btn-secondary">Cancelar</a>
    </form>
    <?php endif; ?>
</div>

<?php else: ?>

<div class="alert alert-success">
    Precios actualizados correctamente para <strong><?= htmlspecialchars($raceName) ?></strong> (Ronda <?= $round ?>).
</div>

<div class="card">
    <h2 style="margin-bottom:1rem;font-size:1.1rem">Resumen de cambios (<?= count($result) ?> ítems)</h2>
    <div class="ranking-table">
        <div class="ranking-header" style="grid-template-columns:1fr 80px 80px 80px 80px">
            <span>Ítem</span><span>Tipo</span><span style="text-align:right">Precio ant.</span><span style="text-align:right">Δ</span><span style="text-align:right">Nuevo</span>
        </div>
        <?php foreach ($result as $id => $r): ?>
        <div class="ranking-row" style="grid-template-columns:1fr 80px 80px 80px 80px">
            <span><?= htmlspecialchars($id) ?></span>
            <span style="color:var(--f1-muted);font-size:.8rem"><?= $r['type'] === 'driver' ? 'Piloto' : 'Escudería' ?></span>
            <span style="text-align:right;color:var(--f1-muted)"><?= number_format($r['old'], 1) ?>M</span>
            <span style="text-align:right;color:<?= $r['delta'] >= 0 ? 'var(--f1-success)' : 'var(--f1-danger)' ?>">
                <?= ($r['delta'] >= 0 ? '+' : '') . number_format($r['delta'], 2) ?>M
            </span>
            <span style="text-align:right;font-weight:700;color:var(--f1-accent)"><?= number_format($r['new'], 1) ?>M</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div style="margin-top:1rem">
    <a href="<?= BASE_URL ?>/" class="btn btn-secondary">Inicio</a>
</div>
<?php endif; ?>
