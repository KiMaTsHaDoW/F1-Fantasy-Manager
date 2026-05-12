<?php

/**
 * Database Setup Script
 * This script creates the F1 Fantasy Manager database and tables
 * 
 * Usage:
 * 1. Configure database connection parameters below
 * 2. Run: php Database/setup.php
 */

// Database Configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASSWORD = '';
$DB_NAME = 'f1_fantasy_manager';

echo "================================\n";
echo "F1 Fantasy Manager - Database Setup\n";
echo "================================\n\n";

try {
    // Connect to MySQL without selecting a database
    echo "[1/3] Connecting to MySQL server...\n";
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD);

    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    echo "✓ Connected successfully\n\n";

    // Read the schema file
    echo "[2/3] Reading schema.sql file...\n";
    $schema_file = __DIR__ . '/schema.sql';
    
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found: $schema_file");
    }

    $sql_queries = file_get_contents($schema_file);
    echo "✓ Schema file loaded\n\n";

    // Execute SQL queries
    echo "[3/3] Creating database and tables...\n";
    
    if ($mysqli->multi_query($sql_queries)) {
        do {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->next_result());
        
        echo "✓ Database and tables created successfully\n\n";
    } else {
        throw new Exception("Error executing SQL: " . $mysqli->error);
    }

    // Verify database creation
    echo "================================\n";
    echo "Verification\n";
    echo "================================\n";
    
    $result = $mysqli->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$DB_NAME'");
    
    if ($result && $result->num_rows > 0) {
        echo "Tables created in database '$DB_NAME':\n\n";
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            echo "  " . (++$count) . ". " . $row['TABLE_NAME'] . "\n";
        }
        echo "\n✓ Database setup completed successfully!\n";
    } else {
        throw new Exception("Database or tables not found after creation");
    }

    $mysqli->close();

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
