<?php
declare(strict_types=1);
namespace App\Api\Utils;

use PDO;

function handleDbConnection() {
    $host     = getenv('DB_HOST');
    $port     = getenv('DB_PORT');
    $dbname   = getenv('DB_NAME');
    $user     = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    $dburi = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    return new PDO($dburi, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

?>