<?php

declare(strict_types=1);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=blog;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur co bdd : ' . $e->getMessage());
}

?>