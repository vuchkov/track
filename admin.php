<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();
$dsn = 'mysql:host=' .$_ENV['MYSQL_HOST']. ';dbname=' .$_ENV['MYSQL_DATABASE']. ';charset=utf8';
try {
    $db = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die($e->getMessage());
}

// Get query parameters
$pageURL = '';
if (!empty($_GET['pageURL']))
    $pageURL = filter_var(trim($_GET['pageURL']), FILTER_VALIDATE_URL)
        ? trim($_GET['pageURL']) : '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

// Fetch unique visits from the database
$sql = "SELECT `url`, `session`, COUNT(DISTINCT `session`) AS unique_visits 
        FROM `track` 
        WHERE `event`='page_view'";

$params = [];
if (!empty($startDate) && !empty($endDate)) {
    $sql .= " AND `created` BETWEEN :startDate AND :endDate";
    $params[':startDate'] = $startDate;
    $params[':endDate'] = $endDate;
}
if (!empty($pageURL)) {
    $sql .= " AND url = :pageURL";
    $params[':pageURL'] = $pageURL;
}

$sql .= " GROUP BY url";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Tracker Dashboard</title>
    <link rel="shortcut icon" href="favicon.ico">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<h1>Traffic Tracker Dashboard</h1>
<form method="GET">
    <label>
        Page URL:
        <input type="text" name="pageURL" value="<?= htmlspecialchars($pageURL) ?>">
    </label>
    <label>
        Start Date:
        <input type="date" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
    </label>
    <label>
        End Date:
        <input type="date" name="endDate" value="<?= htmlspecialchars($endDate) ?>">
    </label>
    <button type="submit">Filter</button>
</form>

<table>
    <thead>
    <tr>
        <th>Page URL</th>
        <th>Unique Visits</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['url']) ?></td>
            <td><?= htmlspecialchars($row['unique_visits']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>