<?php

declare(strict_types=1);
require_once __DIR__ . '/utils/helpers.php';
use function App\Api\Utils\handleDbConnection;

$pdo = handleDbConnection();
$data = [];

$stmt = $pdo->query('
    SELECT P.*, PC.name as category_name, COALESCE(ROUND(AVG(PR.rating), 2), 0) as rating_avg  
    FROM product P
    LEFT JOIN product_review PR ON PR.product_id = P.id
    LEFT JOIN product_category PC ON PC.id = P.product_category_id
    GROUP BY P.id
');
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$products_by_category = [];

foreach ($data as $row) {
    $category_id = $row['product_category_id'];

    if (!isset($products_by_category[$category_id])) {
        $products_by_category[$category_id] = [];
    }

    $products_by_category[$category_id][] = $row;
}
?>
<!doctype html>
<html lang="fi">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
            Kitarakeskus - Jokainen meistä ansaitsee vähän paremman kitaran
        </title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="nav.css" />
        <link rel="stylesheet" href="products.css" />
        <link rel="stylesheet" href="footer.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&family=Poppins:wght@300;400;600&display=swap"
            rel="stylesheet"
        />
    </head>
    <body>
        <nav class="nav">
            <div class="nav-left-div">
                <a href="index.php">
                    <img
                        class="logo"
                        src="images/Kitarakeskus.png"
                        alt="Kitarakeskus"
                    />
                </a>
            </div>

            <div class="nav-right-div">
                <a class="nav-basic-link" href="index.php">ETUSIVU</a>
                <a class="nav-basic-link" href="products.php">TUOTTEET</a>
                <a class="nav-basic-link" href="company.php">YRITYS</a>
                <a class="contact-button" href="contact.php">OTA YHTEYTTÄ</a>
            </div>
        </nav>

        <main class="products-content">
            <section class="products-description">
                <article class="products-description-text-container">
                    <h1 class="products-description-text">Tuotteet</h1>
                    <p class="products-description-text">
                        Oletko aloittamassa uutta harrastusta? Oletko jo kokenut
                        konkari? Meiltä löydät kitaraa hyvin monenlaiseen
                        tarkoitukseen ja genreen. Tarvitsitpa sitten hevikeppiä
                        tai perinteisempää kitaraa, olet tullut oikeaan
                        paikkaan.
                    </p>
                    <p class="products-description-text">
                        Tarkasta valikoimamme alta.
                    </p>
                </article>
            </section>

            <?php if (empty($products_by_category)): ?>
            <p>Ei tuotteita</p>
            <?php else: ?>
                <?php foreach ($products_by_category as $data_in_category): ?>
                    <h2 style="padding: 1rem"><?= htmlspecialchars($data_in_category[0]['category_name'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <section class="products-section">
                    <?php foreach ($data_in_category as $product): ?>
                        <article class="product-container">
                            <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <figure class="img-figure">
                                <img
                                    src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="product-img"
                                />
                            </figure>
                            <p>
                                <i><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></i>
                            </p>
                            <div class="product-bottom">
                                <p>Arvostelujen keskiarvo: <?= htmlspecialchars((string)($product['rating_avg'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <div class="product-bottom">
                                <p>Hinta: <?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>€</p>
                                <button class="add-to-cart-btn">
                                    Lisää ostoskoriin
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
        <footer>
            <div class="container py-5 footer">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <img
                            class="logo"
                            src="images/Kitarakeskus.png"
                            alt="Kitarakeskus"
                        />
                        <p style="padding-top: 1rem; font-size: 0.9rem">
                            <i>Kitaroita juuri sinua varten.</i>
                        </p>
                    </div>

                    <div class="col-12 col-md">
                        <p><b>Pikalinkit</b></p>
                        <p><a href="products.php">Tuotteet</a></p>
                        <p><a href="company.php">Yritys</a></p>
                    </div>

                    <div class="col-12 col-md">
                        <p><b>Yhteystiedot</b></p>
                        <p class="address">Kitarakeskus</p>
                        <p class="address">Osoite 1</p>
                        <p class="address">12345</p>
                        <p class="address">Helsinki</p>
                        <p class="address">p. 020 123 2345</p>
                    </div>
                </div>
            </div>

            <div class="container copyright">
                <p>© 2026 Kitarakeskus - Tietosuojaseloste</p>
            </div>
        </footer>
    </body>
</html>
