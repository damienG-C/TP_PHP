ader

<?php
$titrePage = $titrePage ?? '';
$estConnecte = !empty($_SESSION['user_id']);
$estAdmin = !empty($_SESSION['admin']);
$pseudo = $_SESSION['pseudo'] ?? '';

if ($estConnecte && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titrePage, ENT_QUOTES, 'UTF-8') ?> - Mon Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="index.php">Mon Blog</a>
        <nav>
            <a href="index.php">Accueil</a>
            <?php if ($estConnecte): ?>
                <a href="crea_article.php">Créer un article</a>
                <?php if ($estAdmin): ?>
                    <a href="admin/categories.php">Catégories</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
        <div class="user-zone">
            <?php if ($estConnecte): ?>
                <span>Connecté : <strong><?= htmlspecialchars((string)$pseudo, ENT_QUOTES, 'UTF-8') ?></strong></span>
                <form method="post" action="logout.php" class="logout-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit">Déconnexion</button>
                </form>
            <?php else: ?>
                <a href="login.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="container">