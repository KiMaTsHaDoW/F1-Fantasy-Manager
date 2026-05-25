<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="icon" href="<?= BASE_URL ?>/images/f1-icon.svg" type="image/svg+xml">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo logo-img" href="<?= BASE_URL ?>/">
            <img src="<?= BASE_URL ?>/images/logo1.png" alt="Fantasy F1 Manager">
        </a>
        <nav class="main-nav">
            <a href="<?= BASE_URL ?>/">Inicio</a>
            <a href="<?= BASE_URL ?>/drivers">Pilotos</a>
            <a href="<?= BASE_URL ?>/teams">Equipos</a>
            <a href="<?= BASE_URL ?>/races">Calendario</a>
            <a href="<?= BASE_URL ?>/leagues">Ligas</a>
            <a href="<?= BASE_URL ?>/fantasy/ranking">Ranking</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/profile" class="btn-nav">👤 <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Perfil') ?></a>
                <a href="<?= BASE_URL ?>/auth/logout" class="btn-nav btn-nav-outline">Salir</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/auth/login" class="btn-nav">Iniciar sesión</a>
                <a href="<?= BASE_URL ?>/auth/register" class="btn-nav btn-nav-outline">Registrarse</a>
            <?php endif; ?>
        </nav>
        <button class="nav-toggle" onclick="document.querySelector('.main-nav').classList.toggle('open')">☰</button>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <?php if (!empty($apiError)): ?>
            <div class="alert alert-error" style="display:flex;align-items:center;gap:.5rem">
                ⚠️ La API de F1 no está disponible en este momento. Los datos mostrados pueden estar desactualizados.
            </div>
        <?php endif; ?>
        <?php if (isset($flash) && $flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> F1 Fantasy &mdash; Datos: <a href="https://ergast.com/mrd/" target="_blank">Ergast API</a></p>
    </div>
</footer>
<script src="<?= BASE_URL ?>/js/app.js"></script>

<?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
<div id="admin-bar" class="admin-bar">
    <button class="admin-bar-toggle" onclick="document.getElementById('admin-bar').classList.toggle('open')" title="Panel Admin">
        ⚙
    </button>
    <div class="admin-bar-menu">
        <span class="admin-bar-label">Admin</span>
        <a href="<?= BASE_URL ?>/admin/recalculate" class="admin-bar-btn">🔄 Recalcular precios</a>
    </div>
</div>
<?php endif; ?>

</body>
</html>
