<?php

require_once __DIR__ . '/../.env'; // Load environment variables

// Database configuration based on .env
if ($_ENV['DATABASE_DRIVER'] === 'sqlite') {
    $dbName = 'sqlite:' . $_ENV['SQLITE_DB_PATH'];
    $db = new SQLite3($dbName);
} elseif ($_ENV['DATABASE_DRIVER'] === 'mysql') {
    $dsn = 'mysql:host=' . $_ENV['MYSQL_HOST'] . ';dbname=' . $_ENV['MYSQL_DATABASE'];
    try {
        $pdo = new PDO($dsn, $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Create MySQL database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . $_ENV['MYSQL_DATABASE'];
        $pdo->exec($sql);
        // Create tracking table if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS track (
      id INT AUTO_INCREMENT PRIMARY KEY,
      event VARCHAR(255) INDEX,
      session VARCHAR(255) INDEX,
      user VARCHAR(255) INDEX,
      created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )";
        $pdo->exec($sql);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
} else {
    die("Invalid database driver specified in .env");
}

$dbName = 'mydatabase.db'; // Name of the SQLite database file

// Create a new SQLite3 object


// Check for errors during database creation
if(!$db){
    echo "Error: Could not open or create database: " . $db->lastErrorMsg();
} else {
    echo "Opened database successfully\n";

    // Create the table if it doesn't exist
    $createTableQuery = "CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT NOT NULL,
      email TEXT UNIQUE NOT NULL
  )";

    // Execute the CREATE TABLE query
    $result = $db->query($createTableQuery);

    // Check for errors during table creation
    if(!$result){
        echo "Error creating table: " . $db->lastErrorMsg();
    } else {
        echo "Table created successfully\n";
    }
}

// Database connection (replace with your credentials)
$servername = "your_servername";
$username = "your_username";
$password = "your_password";
$dbname = "your_dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the event data
$event = $_POST['event'];

// Store the tracking data in the database
if ($event == "button_click") {
    $sql = "INSERT INTO tracking (event_type, event_data) VALUES ('button_click', '')"; // You might add more data here later
} else if ($event == "page_view") {
    $page = $_POST['page'];
    $sql = "INSERT INTO tracking (event_type, event_data) VALUES ('page_view', '$page')";
} else {
    $sql = "INSERT INTO tracking (event_type, event_data) VALUES ('unknown', '')";
}


if ($conn->query($sql) === TRUE) {
    echo "Tracking data recorded successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$db->close();
