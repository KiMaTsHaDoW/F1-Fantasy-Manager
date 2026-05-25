<?php
// ─────────────────────────────────────────────────────
// config.php - Configuración general de F1 Fantasy
// Lee variables de entorno inyectadas por Docker.
// Si no existen, usa valores por defecto (desarrollo local).
// ─────────────────────────────────────────────────────

define('APP_NAME',    'F1 Fantasy');
define('APP_VERSION', '1.0.0');

// Base de datos — variables inyectadas por Docker Compose
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'f1fantasy');

// URL base (sin barra final)
define('BASE_URL', getenv('BASE_URL') ?: '');

// API Jolpica (reemplazo de Ergast, misma estructura de URLs)
define('F1_API_BASE', 'https://api.jolpi.ca/ergast/f1');

// Entorno
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Mostrar errores sólo en desarrollo
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Admin — email del usuario administrador
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: '');

// Fantasy — reglas del juego
define('MAX_DRIVERS', 5);
define('MAX_TEAMS',   2);
define('BUDGET',      100.0);

// Sesión
define('SESSION_NAME', 'f1fantasy_session');
date_default_timezone_set('Europe/Madrid');

session_name(SESSION_NAME);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
