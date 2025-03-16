<?php
// Database credentials
$host = 'localhost'; // Change this if your database is hosted elsewhere
$dbname = 'parcel_centre'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// PDO connection string
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

// PDO options (optional)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_EMULATE_PREPARES => false, // Disable emulation of prepared statements
];

// Attempt database connection
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Handle database connection error
    die("Error: Failed to connect to database. " . $e->getMessage());
}

// Define other tables or include additional setup if needed

// Example: Define parcels table
define('TABLE_PARCELS', 'parcels'); // Define a constant for the parcels table name

// Example: Check if parcels table exists and create it if not
$tableExists = $pdo->query("SELECT 1 FROM " . TABLE_PARCELS . " LIMIT 1");

if (!$tableExists) {
    $createTableSql = "CREATE TABLE " . TABLE_PARCELS . " (
        parcel_id int(11) NOT NULL AUTO_INCREMENT,
        user_id int(11) NOT NULL,
        parcelID varchar(50) NOT NULL,
        arrival_date date NOT NULL,
        tracking_number varchar(50) NOT NULL,
        status enum('picked_up', 'not_picked_up') NOT NULL DEFAULT 'not_picked_up',
        PRIMARY KEY (parcel_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";

    try {
        $pdo->exec($createTableSql);
        echo "Table " . TABLE_PARCELS . " created successfully.";
    } catch (PDOException $e) {
        die("Error creating table " . TABLE_PARCELS . ": " . $e->getMessage());
    }
}
?>
