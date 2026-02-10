<?php
    session_start();

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
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
</head>
<body>

    <p>Olet kirjautunut.</p>

    <a href="admin.php?logout=1">Kirjaudu ulos</a>

</body>
</html>