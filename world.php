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
$lookup = $_GET['lookup'] ?? '';
if (!is_string($lookup)) {
    http_response_code(400);
    exit('Please provide a valid lookup type.');
}
$isCities = $lookup === 'cities';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    if ($isCities) {
        $sql = 'SELECT cities.name, cities.district, cities.population
                FROM cities
                INNER JOIN countries ON cities.country_code = countries.code
                WHERE countries.name LIKE :country
                ORDER BY countries.name, cities.name';
    } else {
        $sql = 'SELECT * FROM countries WHERE name LIKE :country ORDER BY name';
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute(['country' => '%' . $country . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('Unable to load results. Please try again later.');
}

function escapeHtml($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<?php if (empty($results)): ?>
<p>No <?= $isCities ? 'cities' : 'countries' ?> found.</p>
<?php else: ?>
<div class="table-scroll" role="region" aria-label="Search results" tabindex="0">
<table>
  <thead>
    <tr>
<?php if ($isCities): ?>
      <th scope="col">Name</th>
      <th scope="col">District</th>
      <th scope="col">Population</th>
<?php else: ?>
      <th scope="col">Country Name</th>
      <th scope="col">Continent</th>
      <th scope="col">Independence Year</th>
      <th scope="col">Head of State</th>
<?php endif; ?>
    </tr>
  </thead>
  <tbody>
<?php foreach ($results as $row): ?>
    <tr>
      <td><?= escapeHtml($row['name']) ?></td>
<?php if ($isCities): ?>
      <td><?= escapeHtml($row['district']) ?></td>
      <td><?= escapeHtml(number_format((int) $row['population'])) ?></td>
<?php else: ?>
      <td><?= escapeHtml($row['continent']) ?></td>
      <td><?= escapeHtml($row['independence_year'] ?? 'N/A') ?></td>
      <td><?= escapeHtml($row['head_of_state'] ?: 'N/A') ?></td>
<?php endif; ?>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>
