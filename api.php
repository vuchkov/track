<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load .env variables
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
        $db = new SQLite3('sqlite:' . $_ENV['SQLITE_DB_PATH'], SQLITE3_OPEN_CREATE);
    }  catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
    // to be continue...
    // @TODO
} elseif ($_ENV['DATABASE_DRIVER'] === 'mysql') {
    $dsn = 'mysql:host=' .$_ENV['MYSQL_HOST']. ';dbname=' .$_ENV['MYSQL_DATABASE']. ';charset=utf8';
    try {
        $db = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        /*$sql = 'SELECT * FROM track LIMIT 10';
        $db->prepare($sql);
        $db->exec($sql);*/
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$event = $input['event'] ?? '';
$url = $input['url'] ?? '';
$session = $input['session'] ?? '';
//$referrer = $input['referrer'] ?? '';
//$userAgent = $input['userAgent'] ?? '';
//$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

// Get the post data.
/*$event = $_POST['event'] ?: 'page_view';
$url = $_POST['url'] ?: '';
$session = $_POST['session'] ?: '';*/

// Validate the post data.
// @TODO

// Store the data in the database
$sql = "INSERT INTO track (event, url, session) VALUES (:event, :url, :session)";
$db->prepare($sql)->execute([
    ':event' => $event,
    ':url' => $url,
    ':session' => $session,
]);
if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit();
}

http_response_code(200);
echo json_encode(["status" => "success"]);
exit();
