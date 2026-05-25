<div class="page-header">
    <h1>📅 Calendario <?= date('Y') ?></h1>
    <p>Todas las carreras de la temporada actual.</p>
</div>

<?php if (empty($races)): ?>
    <div class="card"><p>No hay datos de carreras disponibles en este momento.</p></div>
<?php else: ?>
<?php
// Expand sprint weekends into separate event rows
$allEvents = [];
foreach ($races as $r) {
    if (!empty($r['Sprint'])) {
        $allEvents[] = ['type' => 'sprint', 'date' => $r['Sprint']['date'], 'round' => $r['round'], 'raceName' => $r['raceName'], 'race' => $r];
    }
    $allEvents[] = ['type' => 'race', 'date' => $r['date'], 'round' => $r['round'], 'raceName' => $r['raceName'], 'race' => $r];
}
usort($allEvents, fn($a, $b) => strcmp($a['date'], $b['date']));

// Find the next upcoming event index for highlighting
$nextIdx = null;
foreach ($allEvents as $i => $ev) {
    if ($ev['date'] >= $today) { $nextIdx = $i; break; }
}
?>
<div class="races-list">
<?php foreach ($allEvents as $i => $ev): ?>
    <?php
    $isPast  = $ev['date'] < $today;
    $isNext  = ($i === $nextIdx);
    $isSprint = $ev['type'] === 'sprint';
    ?>
    <a href="<?= BASE_URL ?>/races/show/<?= $ev['round'] ?><?= $isSprint ? '?type=sprint' : '' ?>" class="race-row <?= $isPast ? 'past' : 'upcoming' ?>">
        <div class="race-round"><?= $isSprint ? 'S' : 'R' ?><?= $ev['round'] ?></div>
        <div class="race-name-col">
            <strong><?= htmlspecialchars($ev['raceName']) ?></strong>
            <span><?= $isSprint ? '<span style="color:var(--f1-accent);font-size:.75rem;font-weight:700">SPRINT</span>' : htmlspecialchars($ev['race']['Circuit']['circuitName'] ?? '') ?></span>
        </div>
        <div class="race-location"><?= htmlspecialchars($ev['race']['Circuit']['Location']['country'] ?? '') ?></div>
        <div class="race-date"><?= htmlspecialchars($ev['date']) ?></div>
        <div class="race-status"><?= $isPast ? '✅ Finalizada' : ($isNext ? '🔜 Próxima' : '⏳ Próxima') ?></div>
    </a>
<?php endforeach; ?>
</div>
<?php endif; ?>
