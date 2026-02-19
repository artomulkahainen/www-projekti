<?php

declare(strict_types=1);
require_once __DIR__ . '/utils/helpers.php';
use function App\Api\Utils\handleDbConnection;

$pdo = handleDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)$_POST['rating'] ?? '';
    $productid = (int)$_POST['productid'] ?? '';

    if ($productid > 0 && $rating > 0 && $rating < 6) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO product_review (product_id, rating) VALUES (:productid, :rating)'
            );
            $stmt->execute([':productid' => $productid, ':rating' => $rating]);
        } catch (Exception $e) {
            print('Failed to store rating...');
        }
    }
}

header('Location: products.php');
?>