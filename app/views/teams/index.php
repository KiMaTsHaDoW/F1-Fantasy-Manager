<div class="page-header">
    <div>
        <h1>🏁 Escuderías <?= date('Y') ?></h1>
        <p>Todos los equipos de la temporada actual de Fórmula 1.</p>
    </div>
    <div class="page-header-actions">
        <button id="sort-btn-teams" class="btn btn-secondary" onclick="toggleSortTeams()">
            Ordenar por precio ↕
        </button>
    </div>
</div>

<div class="teams-grid" id="teams-grid">
<?php foreach ($teams as $t): ?>
    <a class="team-card" href="<?= BASE_URL ?>/teams/show/<?= urlencode($t['id']) ?>"
       data-price="<?= $t['price'] ?>"
       style="border-top: 3px solid <?= htmlspecialchars($t['color']) ?>">
        <h3><?= htmlspecialchars($t['name']) ?></h3>
        <p class="team-nat">🌍 <?= htmlspecialchars($t['nationality']) ?></p>
        <div class="team-stats">
            <span>⭐ <?= $t['points'] ?> pts</span>
            <span>💰 <?= number_format($t['price'], 1) ?>M€</span>
        </div>
    </a>
<?php endforeach; ?>
</div>

<script>
(function () {
    let sortDir = 0;
    document.querySelectorAll('#teams-grid .team-card').forEach((el, i) => { el.dataset.origIndex = i; });

    window.toggleSortTeams = function () {
        sortDir = (sortDir + 1) % 3;
        const grid = document.getElementById('teams-grid');
        const btn  = document.getElementById('sort-btn-teams');
        const cards = [...grid.children];

        if (sortDir === 0) {
            cards.sort((a, b) => +a.dataset.origIndex - +b.dataset.origIndex);
            btn.textContent = 'Ordenar por precio ↕';
        } else if (sortDir === 1) {
            cards.sort((a, b) => +b.dataset.price - +a.dataset.price);
            btn.textContent = 'Precio: mayor → menor ↓';
        } else {
            cards.sort((a, b) => +a.dataset.price - +b.dataset.price);
            btn.textContent = 'Precio: menor → mayor ↑';
        }

        cards.forEach(c => grid.appendChild(c));
    };
})();
</script>
