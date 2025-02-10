<?php
session_cache_expire(10); // Expire after 10 minutes
session_start();
$sid = session_id();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}

// Load DB variables from .env:
if (!file_exists(__DIR__ . '/.env')) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: .env does not exist']);
    exit();
}

require_once realpath(__DIR__ . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

if (empty($_ENV['DATABASE_DRIVER'])) {
    http_response_code(500);
    echo json_encode(['error' => 'DATABASE_DRIVER is missing in .env']);
    exit();
}

if ($_ENV['DATABASE_DRIVER'] === 'sqlite') {
    try {
        //@TODO: Add SQLite (optional)
        //$db = new SQLite3('sqlite:' . $_ENV['SQLITE_DB_PATH']);
    }  catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
} elseif ($_ENV['DATABASE_DRIVER'] === 'mysql') {
    $dsn = 'mysql:host=' .$_ENV['MYSQL_HOST']. ';dbname=' .$_ENV['MYSQL_DATABASE']. ';charset=utf8';
    try {
        $db = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid database driver specified in .env']);
    exit();
}

// Get the post data.
$input = json_decode(file_get_contents('php://input'), true);
$event = $input['event'] ?: '';
$url = $input['url'] ?: '';
//$referrer = $input['referrer'] ?: '';
//$userAgent = $input['userAgent'] ?: '';
//$ipAddress = $_SERVER['REMOTE_ADDR'] ?: '';

// Validate the post data.
$isValid = in_array($event, ['page_view', 'button_click', TRUE]);
$isValid = $isValid && filter_var($url, FILTER_VALIDATE_URL);
if (!$isValid) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid post data']);
    exit();
}

// Store the data in the database
$sql = "INSERT INTO track (event, url, session) VALUES (:event, :url, :session)";
$db->prepare($sql)->execute([
    ':event' => $event,
    ':url' => $url,
    ':session' => $sid,
]);
if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit();
}

http_response_code(200);
echo json_encode(["status" => "success"]);
exit();
