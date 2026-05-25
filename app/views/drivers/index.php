<div class="page-header">
    <div>
        <h1>🏎️ Pilotos <?= date('Y') ?></h1>
        <p>Todos los pilotos de la temporada actual de Fórmula 1.</p>
    </div>
    <div class="page-header-actions">
        <button id="sort-btn" class="btn btn-secondary" onclick="toggleSort('drivers-grid', 'sort-btn')">
            Ordenar por precio ↕
        </button>
    </div>
</div>

<div class="drivers-grid" id="drivers-grid">
<?php foreach ($drivers as $d): ?>
    <a class="driver-card" href="<?= BASE_URL ?>/drivers/show/<?= urlencode($d['id']) ?>"
       data-price="<?= $d['price'] ?>"
       style="border-top: 3px solid <?= htmlspecialchars($d['team_color']) ?>">
        <div class="driver-number" style="color: <?= htmlspecialchars($d['team_color']) ?>"><?= htmlspecialchars($d['number']) ?></div>
        <div class="driver-code"><?= htmlspecialchars($d['code']) ?></div>
        <div class="driver-name">
            <span class="driver-first"><?= htmlspecialchars($d['forename']) ?></span>
            <span class="driver-last"><?= htmlspecialchars($d['surname']) ?></span>
        </div>
        <div class="driver-meta">
            <span class="driver-nat">🌍 <?= htmlspecialchars($d['nationality']) ?></span>
            <span class="driver-pts"><?= $d['points'] ?> pts</span>
        </div>
        <div class="driver-price">💰 <?= number_format($d['price'], 1) ?>M€</div>
    </a>
<?php endforeach; ?>
</div>

<script>
(function () {
    let sortDir = 0; // 0=original, 1=desc, 2=asc

    const origOrder = {};
    document.querySelectorAll('#drivers-grid .driver-card').forEach((el, i) => {
        origOrder[el.dataset.price + '_' + i] = i;
        el.dataset.origIndex = i;
    });

    window.toggleSort = function (gridId, btnId) {
        sortDir = (sortDir + 1) % 3;
        const grid = document.getElementById(gridId);
        const btn  = document.getElementById(btnId);
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
