<?php
// Seeder: inserta el usuario admin por defecto si no existe.
// Se ejecuta una vez al arrancar el contenedor web.

$host = getenv('DB_HOST') ?: 'mariadb';
$port = (int)(getenv('DB_PORT') ?: 3306);
$user = getenv('DB_USER') ?: 'f1user';
$pass = getenv('DB_PASS') ?: 'f1pass';
$db   = getenv('DB_NAME') ?: 'f1fantasy';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    fwrite(STDERR, "[seeder] Error de conexión: " . $conn->connect_error . "\n");
    exit(1);
}

$hash = password_hash('admin123456', PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "INSERT IGNORE INTO users (username, email, password, role, created_at)
     VALUES ('admin', 'admin@f1manager.com', ?, 'admin', NOW())"
);
$stmt->bind_param('s', $hash);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "[seeder] Usuario admin creado correctamente.\n";
} else {
    echo "[seeder] Usuario admin ya existe, omitido.\n";
}

$stmt->close();
$conn->close();
