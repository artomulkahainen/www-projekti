<?php
declare(strict_types=1);
require_once __DIR__ . '/utils/helpers.php';
use function App\Api\Utils\handleDbConnection;

session_start();

if (!isset($_SESSION['kirjautunut']) || $_SESSION['kirjautunut'] !== true) {
    header("Location: login.php");
    exit;
}
	
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['kirjautunut']) || $_SESSION['kirjautunut'] !== true) {
    header("Location: login.php");
    exit;
}

$pdo = handleDbConnection();
$data = [];

$stmt = $pdo->query('
    SELECT P.id as pid, P.*, PC.name as category_name, COALESCE(ROUND(AVG(PR.rating), 2), 0) as rating_avg  
    FROM product P
    LEFT JOIN product_review PR ON PR.product_id = P.id
    LEFT JOIN product_category PC ON PC.id = P.product_category_id
    GROUP BY P.id
');
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Status</h1>
    <p>Olet kirjautunut.</p>
    <a href="admin.php?logout=1">Kirjaudu ulos</a>

    <h1>Tuotteet</h1>
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>name</th>
                <th>price</th>
                <th>category</th>
                <th>rating avg</th>
                <th>action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $product): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$product['pid'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$product['price'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$product['rating_avg'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <form action="delete-product.php" method="POST">
                            <input type="hidden" name="productid"
                                        value="<?= htmlspecialchars((string)$product['pid'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit">Poista</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


</body>
</html>