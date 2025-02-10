<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load .env variables
if (!file_exists(__DIR__ . '/.env')) {
    echo json_encode(['error' => 'Error: .env does not exist']);
    exit();
}
require_once realpath(__DIR__ . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

if (empty($_ENV['DATABASE_DRIVER'])) {
    echo json_encode(['error' => 'DATABASE_DRIVER is missing in .env']);
    exit();
}

// Database configuration based on .env
if ($_ENV['DATABASE_DRIVER'] === 'sqlite') {
    try {
        $db = new SQLite3('sqlite:' . $_ENV['SQLITE_DB_PATH'], SQLITE3_OPEN_CREATE);
    }  catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
    // to be continue...
} elseif ($_ENV['DATABASE_DRIVER'] === 'mysql') {
    $dsn = 'mysql:host=' . $_ENV['MYSQL_HOST'] . ';dbname=' . $_ENV['MYSQL_DATABASE'];
    try {
        $db = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS track (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event VARCHAR(255) INDEX,
            url VARCHAR(255) INDEX,
            session VARCHAR(255) INDEX,
            created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['error' => 'Invalid database driver specified in .env']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}

// Get the event data
$event = $_POST['event'];

// Store the tracking data in the database
if ($event == "button_click") {
    $sql = "INSERT INTO track (event_type, event_data) VALUES ('button_click', '')";
} else if ($event == "page_view") {
    $page = $_POST['page'];
    $sql = "INSERT INTO track (event_type, event_data) VALUES ('page_view', '$page')";
} else {
    $sql = "INSERT INTO track (event_type, event_data) VALUES ('unknown', '')";
}

// Close the database connection
$db->close();
