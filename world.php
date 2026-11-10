<?php
header('Content-Type: text/html; charset=utf-8');

$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

$country = $_GET['country'] ?? '';
if (!is_string($country)) {
    http_response_code(400);
    exit('Please provide a country name.');
}
$country = trim($country);

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $stmt = $conn->prepare('SELECT * FROM countries WHERE name LIKE :country ORDER BY name');
    $stmt->execute(['country' => '%' . $country . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('Unable to load countries. Please try again later.');
}

function escapeHtml($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<?php if (empty($results)): ?>
<p>No countries found.</p>
<?php else: ?>
<ul>
<?php foreach ($results as $row): ?>
  <li><?= escapeHtml($row['name']) ?> is ruled by <?= escapeHtml($row['head_of_state']) ?>.</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
