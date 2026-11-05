<?php

$titrePage = $titrePage ?? '';
$estConnecte  = !empty($_SESSION['user_id']);
$estAdmin   = !empty($_SESSION['admin']);
$pseudo    = $_SESSION['pseudo'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titrePage) ?> - Mon Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="index.php">Mon Blog</a>

        <nav>
            <a href="index.php">Accueil</a>
            <?php if ($estConnecte): ?>
                <a href="article_new.php">Nouvel article</a>
                <?php if ($estAdmin): ?>
                    <a href="admin/categories.php">Catégories</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="user-zone">
            <?php if ($estConnecte): ?>
                <span>Connecté : <strong><?= htmlspecialchars($pseudo) ?></strong></span>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="container">