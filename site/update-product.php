<?php

declare(strict_types=1);
require_once __DIR__ . '/utils/helpers.php';
use function App\Api\Utils\handleDbConnection;

session_start();

if (!isset($_SESSION['kirjautunut']) || $_SESSION['kirjautunut'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = handleDbConnection();

    $pid = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);

    if ($pid && $name && $image_url && $price > 0 && $category_id > 0) {
        $stmt = $pdo->prepare("
            UPDATE product
            SET name=:name, description=:description, image_url=:image_url, price=:price, product_category_id=:category_id
            WHERE id=:pid
        ");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image_url' => $image_url,
            ':price' => $price,
            ':category_id' => $category_id,
            ':pid' => $pid
        ]);
    }
}

header("Location: admin.php");
exit;