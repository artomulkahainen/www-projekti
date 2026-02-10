<?php
    session_start();

    $oikeasalasana = "Salasana123";

    if (isset($_POST['salasana'])) {
        if ($_POST['salasana'] === $oikeasalasana) {
            $_SESSION['kirjautunut'] = true;

            header("Location: admin.php");
            exit;
        } else {
            echo "Väärä salasana.";
        }
    }
?>


<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Kirjaudu sisään</h1>

    <form method="post">
        <label>
            Salasana:
            <input type="password" name="salasana">
        </label>
        <br><br>
        <button type="submit">Kirjaudu</button>
    </form>

</body>
</html>