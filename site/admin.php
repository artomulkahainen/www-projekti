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

// Tuotteen lisäys
$add_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);

    if ($name && $image_url && $price > 0 && $category_id > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO product (name, description, image_url, price, product_category_id)
            VALUES (:name, :description, :image_url, :price, :category_id)
        ");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image_url' => $image_url,
            ':price' => $price,
            ':category_id' => $category_id,
        ]);
        $add_message = "Tuote '$name' lisätty onnistuneesti";
    } else {
        $add_message = "Täytä kaikki kentät";
    }
}

$stmt = $pdo->query('
    SELECT P.id as pid, P.*, PC.name as category_name, COALESCE(ROUND(AVG(PR.rating), 2), 0) as rating_avg  
    FROM product P
    LEFT JOIN product_review PR ON PR.product_id = P.id
    LEFT JOIN product_category PC ON PC.id = P.product_category_id
    GROUP BY P.id
');
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cat_stmt = $pdo->query('SELECT id, name FROM product_category ORDER BY name');
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Tuotteen hakeminen muokkausta varten
$edit_product = null;
if (isset($_GET['edit'])) {
    $pid = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id=:pid");
    $stmt->execute([':pid' => $pid]);
    $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="admin.css" />

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
                        <form method="GET" action="admin.php">
                            <input type="hidden" name="edit" value="<?= (int)$product['pid'] ?>">
                            <button type="submit">Muokkaa</button>
                        </form>
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

    <h2>Lisää uusi tuote</h2>

    <?php if ($add_message): ?>
        <p><?= htmlspecialchars($add_message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="admin.php">
        <input type="hidden" name="add_product" value="1">
        <p>
            <label>Nimi:<br>
                <input type="text" name="name" required>
            </label>
        </p>
        <p>
            <label>Kuvaus:<br>
                <textarea name="description"></textarea>
            </label>
        </p>
        <p>
            <label>Kuvan URL:<br>
                <input type="url" name="image_url" required>
            </label>
        </p>
        <p>
            <label>Hinta:<br>
                <input type="number" name="price" step="0.01" min="0" required>
            </label>
        </p>
        <p>
            <label>Kategoria:<br>
                <select name="category_id" required>
                    <option value="">-- Valitse --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>
        <button type="submit">Lisää tuote</button>
    </form>

    <?php if (isset($edit_product)): ?>
        <h2>Muokkaa tuotetta</h2>
        <form method="POST" action="update-product.php">
            <input type="hidden" name="product_id" value="<?= (int)$edit_product['id'] ?>">

            <p>
                <label>Nimi:<br>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit_product['name'], ENT_QUOTES, 'UTF-8') ?>">
                </label>
            </p>
            <p>
                <label>Kuvaus:<br>
                    <textarea name="description"><?= htmlspecialchars($edit_product['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </label>
            </p>
            <p>
                <label>Kuvan URL:<br>
                    <input type="url" name="image_url" required value="<?= htmlspecialchars($edit_product['image_url'], ENT_QUOTES, 'UTF-8') ?>">
                </label>
            </p>
            <p>
                <label>Hinta:<br>
                    <input type="number" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($edit_product['price'], ENT_QUOTES, 'UTF-8') ?>">
                </label>
            </p>
            <p>
                <label>Kategoria:<br>
                    <select name="category_id" required>
                        <option value="">-- Valitse --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" <?= $edit_product['product_category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
            <button type="submit">Päivitä tuote</button>
        </form>
    <?php endif; ?>

</body>
</html>