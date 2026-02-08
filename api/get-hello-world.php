<?php

declare(strict_types=1);
require_once __DIR__ . '/utils/helpers.php';
use function App\Api\Utils\handleDbConnection;
use function App\Api\Utils\jsonResponse;

$pdo = handleDbConnection();
$data = null;

if (!$data) {
    jsonResponse([ 'status' => 'not_ok', 'data' => $data], 400);
}

$stmt = $pdo->query('SELECT 1'); 
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse([ 'status' => 'ok', 'data' => $data], 200);


?>