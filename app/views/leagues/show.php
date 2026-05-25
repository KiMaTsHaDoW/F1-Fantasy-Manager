<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($league['name']) ?></h1>
        <?php if ($league['description']): ?>
            <p style="color:var(--f1-muted);margin-top:.3rem"><?= htmlspecialchars($league['description']) ?></p>
        <?php endif; ?>
        <div class="badge-row" style="margin-top:.75rem">
            <span class="badge"><?= $league['is_public'] ? '🌍 Pública' : '🔒 Privada' ?></span>
            <span class="badge">👤 <?= $league['member_count'] ?> miembros</span>
            <span class="badge">Creada por <?= htmlspecialchars($league['creator_name']) ?></span>
        </div>
    </div>
    <div class="page-header-actions">
        <?php if ($isMember): ?>
            <?php if ($userTeam): ?>
                <a href="<?= BASE_URL ?>/fantasy?league=<?= $league['id'] ?>" class="btn btn-primary">Mi Equipo</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/fantasy/create?league=<?= $league['id'] ?>" class="btn btn-primary">+ Crear equipo</a>
            <?php endif; ?>
            <?php if (!$isCreator): ?>
                <a href="<?= BASE_URL ?>/leagues/leave/<?= $league['id'] ?>" class="btn btn-secondary"
                   onclick="return confirm('¿Seguro que quieres abandonar esta liga?')">Abandonar</a>
            <?php endif; ?>
        <?php elseif ($league['is_public'] && isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>/leagues/join/<?= $league['id'] ?>" class="btn btn-primary">Unirse</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/leagues" class="btn btn-secondary">← Ligas</a>
    </div>
</div>

<?php if ($isMember): ?>
<div class="card mb-md invite-box">
    <span style="color:var(--f1-muted);font-size:.9rem">🔗 Código de invitación:</span>
    <strong class="invite-code"><?= htmlspecialchars($league['invite_code']) ?></strong>
    <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($league['invite_code']) ?>'); this.textContent='¡Copiado!'; setTimeout(()=>this.textContent='Copiar',1500)" class="btn btn-sm btn-secondary">Copiar</button>
</div>
<?php endif; ?>

<h2 style="margin-bottom:1rem">Clasificación</h2>
<?php if (empty($ranking)): ?>
    <div class="card"><p style="color:var(--f1-muted)">No hay equipos en esta liga todavía.</p></div>
<?php else: ?>
<div class="ranking-table">
    <div class="ranking-header" style="grid-template-columns: 60px 1fr 1fr 120px <?= $isCreator ? '80px' : '' ?>">
        <span>#</span>
        <span>Usuario</span>
        <span>Equipo Fantasy</span>
        <span style="text-align:right">Puntos</span>
        <?php if ($isCreator): ?><span></span><?php endif; ?>
    </div>
    <?php
    $medals = ['🥇','🥈','🥉'];
    foreach ($ranking as $i => $row):
        $extraClass = $i === 0 ? 'ranking-row--first' : ($i === 1 ? 'ranking-row--second' : ($i === 2 ? 'ranking-row--third' : ''));
    ?>
    <div class="ranking-row <?= $extraClass ?>" style="grid-template-columns: 60px 1fr 1fr 120px <?= $isCreator ? '80px' : '' ?>">
        <span class="rank-pos"><?= $medals[$i] ?? $i + 1 ?></span>
        <span style="font-weight:600"><?= htmlspecialchars($row['username']) ?></span>
        <span style="color:var(--f1-muted)"><?= $row['team_name'] ? htmlspecialchars($row['team_name']) : '<em>Sin equipo</em>' ?></span>
        <span class="rank-pts"><?= number_format((float)($row['total_points'] ?? 0), 1) ?> pts</span>
        <?php if ($isCreator && $row['user_id'] != $league['creator_id']): ?>
        <span style="text-align:right">
            <form method="POST" action="<?= BASE_URL ?>/leagues/kick" style="display:inline"
                  onsubmit="return confirm('¿Expulsar a <?= htmlspecialchars(addslashes($row['username'])) ?> de la liga?')">
                <input type="hidden" name="league_id" value="<?= $league['id'] ?>">
                <input type="hidden" name="user_id"   value="<?= $row['user_id'] ?>">
                <button type="submit" class="btn btn-sm" style="background:var(--f1-red);color:#fff;border:none">Expulsar</button>
            </form>
        </span>
        <?php elseif ($isCreator): ?>
        <span></span>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
