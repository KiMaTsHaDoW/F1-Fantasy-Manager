# F1 Fantasy Manager - Database Setup

## Files Included

### 1. **schema.sql**
- Complete database schema with all tables and relationships
- Creates the `f1_fantasy_manager` database
- Sets up 11 tables:
  - `escuderias` - Formula 1 Teams/Constructors
  - `usuarios` - Users (Admin/Usuario roles)
  - `pilotos` - Formula 1 Drivers
  - `ligas` - Leagues (Public/Private)
  - `equipos` - User Fantasy Teams
  - `equipo_pilotos` - User Team Drivers (Many-to-Many)
  - `carreras` - Races/Grand Prix
  - `resultados` - Race Results
  - `puntuaciones_usuario` - User Points by Race

### 2. **setup.php**
- PHP script to automatically create the database and tables
- Validates schema file exists
- Provides verification output

### 3. **Config/Database.php**
- Database connection handler
- Singleton pattern for connection management
- Prepared statements support
- Error handling

## Setup Instructions

### Option A: Using PHP Setup Script

**Requirements:**
- PHP 8.2+
- MySQL 8.0+
- Local MySQL server running

**Steps:**

1. **Configure Database Credentials** (if needed)
   
   Edit `Database/setup.php` if your MySQL credentials are different:
   ```php
   $DB_HOST = 'localhost';      // MySQL server host
   $DB_USER = 'root';           // MySQL username
   $DB_PASSWORD = '';           // MySQL password
   ```

2. **Run the Setup Script**
   
   ```bash
   cd F1-Fantasy-Manager
   php Database/setup.php
   ```

3. **Expected Output**
   ```
   ================================
   F1 Fantasy Manager - Database Setup
   ================================
   
   [1/3] Connecting to MySQL server...
   ✓ Connected successfully
   
   [2/3] Reading schema.sql file...
   ✓ Schema file loaded
   
   [3/3] Creating database and tables...
   ✓ Database and tables created successfully
   
   ================================
   Verification
   ================================
   Tables created in database 'f1_fantasy_manager':
   
     1. escuderias
     2. usuarios
     3. pilotos
     4. ligas
     5. equipos
     6. equipo_pilotos
     7. carreras
     8. resultados
     9. puntuaciones_usuario
   
   ✓ Database setup completed successfully!
   ```

### Option B: Manual Setup (MySQL Client)

```bash
mysql -u root -p < Database/schema.sql
```

Or paste the contents of `schema.sql` directly into MySQL Workbench or phpMyAdmin.

## Database Schema Overview

### Entity Relationships

```
usuarios (1) ──── (N) equipos
usuarios (1) ──── (N) ligas (as admin)

escuderias (1) ──── (N) pilotos

ligas (1) ──── (N) equipos
equipos (N) ──── (N) pilotos (through equipo_pilotos)

carreras (1) ──── (N) resultados
pilotos (1) ──── (N) resultados

carreras (1) ──── (N) puntuaciones_usuario
equipos (1) ──── (N) puntuaciones_usuario
```

## Configuration

### Using Database.php in Your Application

```php
<?php
use Config\Database;

// Get connection
$conn = Database::getConnection();

// Execute query
$result = Database::query("SELECT * FROM usuarios");

// Use prepared statements
$stmt = Database::prepare(
    "SELECT * FROM pilotos WHERE id_escuderia = ?",
    "i",
    1
);
$stmt->execute();
$result = $stmt->get_result();

// Close connection
Database::closeConnection();
?>
```

### Custom Credentials

```php
<?php
use Config\Database;

Database::setCredentials(
    'localhost',           // host
    'f1_user',            // user
    'secure_password',    // password
    'f1_fantasy_manager', // database name
    3306                  // port
);

$conn = Database::getConnection();
?>
```

## Troubleshooting

### Error: "Connection failed: Access denied"
- Check MySQL credentials in `setup.php` or `Config/Database.php`
- Ensure MySQL server is running
- Verify user permissions

### Error: "Schema file not found"
- Ensure `Database/schema.sql` exists
- Check file path is correct relative to script location

### Error: "Table 'f1_fantasy_manager' doesn't exist"
- Run setup script again
- Check for MySQL error messages during setup
- Verify database was created: `SHOW DATABASES;`

## Next Steps

1. **Populate Initial Data**
   - Add F1 2025 teams (escuderias)
   - Add drivers (pilotos)
   - Add upcoming races (carreras)

2. **Create Models**
   - Model classes are already created in `/Models` directory

3. **Develop Controllers**
   - Implement CRUD operations for each entity

4. **Build Views**
   - Create Bootstrap-based UI in `/Views` directory

## Support

For database structure modifications, edit `Database/schema.sql` before running setup.
For connection issues, check `Config/Database.php` configuration.
