<?php if ($team): ?>
<div class="detail-page">
    <div class="detail-hero">
        <div class="detail-info">
            <h1><?= htmlspecialchars($team['name']) ?></h1>
            <p class="detail-nat">🌍 <?= htmlspecialchars($team['nationality']) ?></p>
            <?php if ($position): ?>
                <div class="badge-row">
                    <span class="badge badge-pos">P<?= $position ?> en constructores</span>
                    <span class="badge badge-pts"><?= $points ?> puntos</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="detail-stats">
        <div class="stat"><span class="stat-label">Nacionalidad</span><span class="stat-value"><?= htmlspecialchars($team['nationality'] ?? '-') ?></span></div>
        <?php if (!empty($team['url'])): ?>
        <div class="stat"><span class="stat-label">Wikipedia</span><span class="stat-value"><a href="<?= htmlspecialchars($team['url']) ?>" target="_blank">Ver perfil</a></span></div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($drivers)): ?>
<h2 class="section-title" style="margin-top:1.5rem">Pilotos</h2>
<div style="display:flex; flex-direction:row; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
    <?php foreach ($drivers as $d): ?>
    <a href="<?= BASE_URL ?>/drivers/show/<?= htmlspecialchars($d['id']) ?>"
       style="display:flex; flex-direction:column; width:150px; flex-shrink:0;
              background:var(--f1-gray); border:1px solid var(--f1-border); border-radius:8px;
              padding:1rem; text-decoration:none; color:var(--f1-text);
              transition:all .2s ease;">
        <?php if ($d['headshot']): ?>
            <img src="<?= htmlspecialchars($d['headshot']) ?>" alt=""
                 style="width:100%; height:110px; object-fit:cover; object-position:top center; border-radius:6px; margin-bottom:.6rem; display:block;">
        <?php else: ?>
            <div style="width:100%; height:80px; border-radius:6px; border:2px solid <?= htmlspecialchars($d['team_color']) ?>;
                        display:flex; align-items:center; justify-content:center;
                        font-size:1.1rem; font-weight:800; color:var(--f1-muted); margin-bottom:.6rem;">
                <?= htmlspecialchars($d['code']) ?>
            </div>
        <?php endif; ?>
        <span style="font-size:1.8rem; font-weight:900; line-height:1; color:<?= htmlspecialchars($d['team_color']) ?>"><?= htmlspecialchars($d['number']) ?></span>
        <span style="font-size:.8rem; color:var(--f1-muted); margin-top:.3rem;"><?= htmlspecialchars($d['forename']) ?></span>
        <span style="font-size:.95rem; font-weight:700; color:var(--f1-text);"><?= htmlspecialchars($d['surname']) ?></span>
        <span style="font-size:.7rem; font-weight:700; color:var(--f1-muted); letter-spacing:.1em; margin-top:.2rem;"><?= htmlspecialchars($d['code']) ?></span>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<a href="<?= BASE_URL ?>/teams" class="btn btn-secondary mt-md">← Volver a equipos</a>

<?php else: ?>
    <p>Equipo no encontrado.</p>
<?php endif; ?>
