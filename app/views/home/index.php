<section class="hero">
    <div class="hero-content">
        <h1>Bienvenido a <span class="accent">F1 Fantasy</span></h1>
        <p>Crea tu equipo de ensueño con los mejores pilotos y escuderías de la Fórmula 1.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/auth/register" class="btn btn-primary">Empezar gratis</a>
                <a href="<?= BASE_URL ?>/drivers" class="btn btn-secondary">Ver pilotos</a>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/fantasy" class="btn btn-primary">Ver mi equipo</a>
        <?php endif; ?>
    </div>
</section>

<div class="grid-2 mt-lg">
    <!-- Clasificación pilotos -->
    <div class="card">
        <div class="card-header">
            <h2>🏆 Clasificación de Pilotos</h2>
            <a href="<?= BASE_URL ?>/drivers" class="link-more">Ver todos →</a>
        </div>
        <table class="standings-table">
            <thead><tr><th>Pos</th><th>Piloto</th><th>Pts</th></tr></thead>
            <tbody>
            <?php foreach ($standings as $i => $s): ?>
                <tr>
                    <td class="pos"><?= $i+1 ?></td>
                    <td>
                        <strong><?= htmlspecialchars($s['Driver']['familyName']) ?></strong>
                        <span class="nat"><?= htmlspecialchars($s['Driver']['nationality'] ?? '') ?></span>
                    </td>
                    <td class="pts"><?= $s['points'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Clasificación constructores -->
    <div class="card">
        <div class="card-header">
            <h2>🏎️ Clasificación de Equipos</h2>
            <a href="<?= BASE_URL ?>/teams" class="link-more">Ver todos →</a>
        </div>
        <table class="standings-table">
            <thead><tr><th>Pos</th><th>Equipo</th><th>Pts</th></tr></thead>
            <tbody>
            <?php foreach ($constructorStandings as $i => $s): ?>
                <tr>
                    <td class="pos"><?= $i+1 ?></td>
                    <td><strong><?= htmlspecialchars($s['Constructor']['name']) ?></strong></td>
                    <td class="pts"><?= $s['points'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($nextRace): ?>
<div class="card mt-lg next-race-card">
    <h2>📍 Próxima Carrera</h2>
    <div class="race-info">
        <div class="race-name"><?= htmlspecialchars($nextRace['raceName']) ?></div>
        <div class="race-details">
            <span>🗓️ <?= htmlspecialchars($nextRace['date']) ?></span>
            <span>📌 <?= htmlspecialchars($nextRace['Circuit']['circuitName'] ?? '') ?></span>
            <span>🌍 <?= htmlspecialchars($nextRace['Circuit']['Location']['country'] ?? '') ?></span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>/races" class="btn btn-secondary">Ver calendario completo</a>
</div>
<?php endif; ?>

<?php if (!empty($lastRace['Results'])): ?>
<div class="card mt-lg">
    <div class="card-header">
        <h2>🏁 Última Carrera: <?= htmlspecialchars($lastRace['raceName'] ?? '') ?></h2>
        <a href="<?= BASE_URL ?>/races/show/<?= $lastRace['round'] ?? '' ?>" class="link-more">Ver resultados →</a>
    </div>
    <table class="standings-table">
        <thead><tr><th>Pos</th><th>Piloto</th><th>Equipo</th><th>Puntos</th></tr></thead>
        <tbody>
        <?php foreach (array_slice($lastRace['Results'], 0, 5) as $r): ?>
            <tr>
                <td class="pos"><?= $r['position'] ?></td>
                <td><strong><?= htmlspecialchars($r['Driver']['familyName']) ?></strong></td>
                <td><?= htmlspecialchars($r['Constructor']['name']) ?></td>
                <td class="pts"><?= $r['points'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
