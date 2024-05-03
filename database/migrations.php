<?php
// Require the necessary files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Use the DatabaseConnection utility class
use App\Utils\DatabaseConnection;

// Get the database name from the configuration
$database = DB_DATABASE;

try {
    // Check if the database exists, create it if it doesn't
    if (!DatabaseConnection::databaseExists($database)) {
        DatabaseConnection::createDatabase($database);
    } else {
        echo "Database '$database' already exists." . PHP_EOL;
    }

    // Get the PDO instance (this will also select the database)
    $pdo = DatabaseConnection::getPDO();


    // Create the Subscriber table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subscriber (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subs_name VARCHAR(255) NOT NULL
        )
    ");

    // Create the Box table with foreign key constraint
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS box (
            id INT AUTO_INCREMENT PRIMARY KEY,
            box_name VARCHAR(255) NOT NULL,
            prayer_zone VARCHAR(255) NOT NULL,
            subs_id INT,
            FOREIGN KEY (subs_id) REFERENCES subscriber(id)
        )
    ");

    // Create the Song table with foreign key constraints
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS song (
            id INT AUTO_INCREMENT PRIMARY KEY,
            song_title VARCHAR(255) NOT NULL,
            subs_id INT,
            box_id INT,
            prayer_zone VARCHAR(255) NOT NULL,
            prayer_time_seq INT,
            prayer_time_date DATE,  
            prayer_time TIME,
            FOREIGN KEY (subs_id) REFERENCES subscriber(id),
            FOREIGN KEY (box_id) REFERENCES box(id)
        )
    ");

    // Insert sample data into Subscriber table
    $pdo->exec("
        INSERT INTO subscriber (subs_name)
        VALUES
            ('The Café'),
            ('My Restaurant')
    ");

    // Insert sample data into Box table
    $pdo->exec("
        INSERT INTO box (box_name, prayer_zone, subs_id)
        VALUES
        ('Johor Bahru Tower', 'JHR01', 1),
        ('Larkin Mosque', 'JHR02', 1),
        ('Pasir Gudang Station', 'JHR03', 1),
        ('Kulai Interchange', 'JHR04', 1),
        ('Alor Setar Tower', 'KDH01', 2),
        ('Jitra Mosque', 'KDH02', 2),
        ('Kuala Kedah Station', 'KDH03', 2),
        ('Langkawi Ferry Terminal', 'KDH04', 2),
        ('Sungai Petani Tower', 'KDH05', 2),
        ('Kulim Interchange', 'KDH06', 2),
        ('Baling Mosque', 'KDH07', 2),
        ('Kota Bharu Tower', 'KTN01', 1),
        ('Gua Musang Station', 'KTN03', 1),
        ('Bandaraya Melaka Tower', 'MLK01', 1),
        ('Seremban Tower', 'NGS01', 1),
        ('Port Dickson Station', 'NGS02', 1)
    ");

    echo "Database migrations and sample data insertion completed successfully!" . PHP_EOL;
} catch (PDOException $e) {
    echo "Database migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
