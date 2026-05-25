#!/bin/bash
set -e

echo "[entrypoint] Esperando a que MariaDB acepte conexiones..."
until php -r "
    \$c = @new mysqli(
        getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'),
        getenv('DB_NAME'), (int)getenv('DB_PORT')
    );
    exit(\$c->connect_error ? 1 : 0);
" 2>/dev/null; do
    sleep 1
done
echo "[entrypoint] Base de datos lista."

php /var/www/html/config/seed.php

exec apache2-foreground
