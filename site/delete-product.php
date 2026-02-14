<?php
declare(strict_types=1);
require_once __DIR__ . '/utils/helpers.php';
use function App\Api\Utils\handleDbConnection;


session_start();

if (!isset($_SESSION['kirjautunut']) || $_SESSION['kirjautunut'] !== true) {
    header("Location: login.php");
    exit;
}
	
$pdo = handleDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productid = (int)$_POST['productid'] ?? '';

    if ($productid > 0) {
        try {
            $stmt = $pdo->prepare(
                'DELETE FROM product WHERE id = :productid;'
            );
            $stmt->execute([':productid' => $productid]);
        } catch (Exception $e) {
            print('Failed to delete product...');
        }
    }
}

header('Location: admin.php');
?>