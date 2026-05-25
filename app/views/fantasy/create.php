<div class="page-header">
    <h1>🏗️ Crear equipo — <?= htmlspecialchars($league['name']) ?></h1>
    <p>Selecciona <strong><?= MAX_DRIVERS ?> pilotos</strong> y <strong><?= MAX_TEAMS ?> equipos</strong> con un presupuesto de <strong><?= BUDGET ?>M€</strong>.</p>
</div>

<div class="budget-bar card">
    <div class="budget-info">
        <span>💰 Presupuesto: <strong id="budget-remaining"><?= BUDGET ?></strong>M€ restantes</span>
        <span>Pilotos: <strong id="drivers-count">0</strong>/<?= MAX_DRIVERS ?></span>
        <span>Equipos: <strong id="teams-count">0</strong>/<?= MAX_TEAMS ?></span>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/fantasy/store" id="fantasy-form">
    <input type="hidden" name="league_id" value="<?= $league['id'] ?>">
    <div class="form-group">
        <label for="team_name">Nombre de tu equipo</label>
        <input type="text" id="team_name" name="team_name" required placeholder="Ej: Scuderia Veloce" maxlength="60">
    </div>

    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.5rem">
        <h2 class="section-title" style="margin:0">Selecciona <?= MAX_DRIVERS ?> pilotos</h2>
        <button type="button" id="sort-drivers-btn" class="btn btn-secondary btn-sm"
                data-sort-state="none"
                onclick="toggleSort('drivers-grid','sort-drivers-btn')">Ordenar por precio ↕</button>
    </div>
    <div class="selection-grid" id="drivers-grid">
    <?php foreach ($drivers as $d): ?>
        <label class="pick-card" data-price="<?= $d['price'] ?>" data-type="driver">
            <input type="checkbox" name="drivers[]" value="<?= htmlspecialchars($d['id']) ?>" class="pick-cb driver-cb">
            <div class="pick-number"><?= htmlspecialchars($d['number']) ?></div>
            <div class="pick-code"><?= htmlspecialchars($d['code']) ?></div>
            <div class="pick-fullname"><?= htmlspecialchars($d['forename'] . ' ' . $d['surname']) ?></div>
            <div class="pick-nat"><?= htmlspecialchars($d['nationality']) ?></div>
            <div class="pick-price">💰 <?= number_format($d['price'], 1) ?>M€</div>
            <div class="pick-selected">✓ Seleccionado</div>
        </label>
    <?php endforeach; ?>
    </div>

    <div style="display:flex;align-items:center;gap:1rem;margin-top:var(--space-lg);margin-bottom:.5rem">
        <h2 class="section-title" style="margin:0">Selecciona <?= MAX_TEAMS ?> equipos</h2>
        <button type="button" id="sort-teams-btn" class="btn btn-secondary btn-sm"
                data-sort-state="none"
                onclick="toggleSort('teams-grid','sort-teams-btn')">Ordenar por precio ↕</button>
    </div>
    <div class="selection-grid" id="teams-grid">
    <?php foreach ($teams as $t): ?>
        <label class="pick-card" data-price="<?= $t['price'] ?>" data-type="constructor">
            <input type="checkbox" name="constructors[]" value="<?= htmlspecialchars($t['id']) ?>" class="pick-cb team-cb">
            <div class="pick-name"><?= htmlspecialchars($t['name']) ?></div>
            <div class="pick-nat"><?= htmlspecialchars($t['nationality']) ?></div>
            <div class="pick-price">💰 <?= number_format($t['price'], 1) ?>M€</div>
            <div class="pick-selected">✓ Seleccionado</div>
        </label>
    <?php endforeach; ?>
    </div>

    <div class="form-submit mt-lg">
        <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">Crear equipo</button>
        <a href="<?= BASE_URL ?>/" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
(function() {
    // — Ordenación por precio —
    const sortState = { 'drivers-grid': 0, 'teams-grid': 0 };

    document.querySelectorAll('#drivers-grid .pick-card, #teams-grid .pick-card')
        .forEach((el, i) => { el.dataset.origIndex = i; });

    window.toggleSort = function(gridId, btnId) {
        sortState[gridId] = (sortState[gridId] + 1) % 3;
        const dir   = sortState[gridId];
        const grid  = document.getElementById(gridId);
        const btn   = document.getElementById(btnId);
        const cards = [...grid.querySelectorAll('.pick-card')];

        if (dir === 0) {
            cards.sort((a, b) => +a.dataset.origIndex - +b.dataset.origIndex);
            btn.textContent = 'Ordenar por precio ↕';
        } else if (dir === 1) {
            cards.sort((a, b) => +b.dataset.price - +a.dataset.price);
            btn.textContent = 'Precio: mayor → menor ↓';
        } else {
            cards.sort((a, b) => +a.dataset.price - +b.dataset.price);
            btn.textContent = 'Precio: menor → mayor ↑';
        }
        cards.forEach(c => grid.appendChild(c));
    };

    // — Selección y presupuesto —
    const BUDGET = <?= BUDGET ?>;
    const MAX_DRIVERS = <?= MAX_DRIVERS ?>;
    const MAX_TEAMS = <?= MAX_TEAMS ?>;
    let spent = 0, driversCount = 0, teamsCount = 0;

    function updateUI() {
        document.getElementById('budget-remaining').textContent = (BUDGET - spent).toFixed(1);
        document.getElementById('drivers-count').textContent = driversCount;
        document.getElementById('teams-count').textContent = teamsCount;
        const valid = driversCount === MAX_DRIVERS && teamsCount === MAX_TEAMS && spent <= BUDGET;
        document.getElementById('submit-btn').disabled = !valid;
    }

    document.querySelectorAll('.pick-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            const card = this.closest('.pick-card');
            const price = parseFloat(card.dataset.price);
            const type = card.dataset.type;
            const isDriver = type === 'driver';

            if (this.checked) {
                const max = isDriver ? MAX_DRIVERS : MAX_TEAMS;
                const count = isDriver ? driversCount : teamsCount;
                if (count >= max || spent + price > BUDGET) {
                    this.checked = false;
                    card.classList.add('shake');
                    setTimeout(() => card.classList.remove('shake'), 400);
                    return;
                }
                spent += price;
                if (isDriver) driversCount++; else teamsCount++;
                card.classList.add('selected');
            } else {
                spent -= price;
                if (isDriver) driversCount--; else teamsCount--;
                card.classList.remove('selected');
            }
            updateUI();
        });
    });

    updateUI();
})();
</script>
