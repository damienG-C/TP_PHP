<?php
declare(strict_types=1);

require_once 'config.php';
require_once 'fonctions_articles.php';

authentification();

//Traitement
$pseudos      = getPseudosAuteurs($pdo);
$pseudoFiltre = lireFiltrePseudo();
$erreurPseudo = '';

if ($pseudoFiltre !== '' && !in_array($pseudoFiltre, $pseudos, true)) {
    $erreurPseudo = 'Cet auteur n\'existe pas ou n\'a publié aucun article.';
    $pseudoFiltre = '';
}

$articles = getArticles($pdo, $pseudoFiltre);

//Affichage
$titrePage = 'Accueil';
require 'header.php';
?>

<h1>Accueil</h1>

<form method="get" class="filter-form">
    <label for="pseudo">Filtrer par auteur :</label>
    <select name="pseudo" id="pseudo">
        <option value="">Tous les auteurs</option>
        <?php foreach ($pseudos as $p): ?>
            <option value="<?= htmlspecialchars($p) ?>" <?= $p === $pseudoFiltre ? 'selected' : '' ?>>
                <?= htmlspecialchars($p) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
    <?php if ($pseudoFiltre !== ''): ?>
        <a href="index.php">Réinitialiser</a>
    <?php endif; ?>
    <?php if ($erreurPseudo !== ''): ?>
        <p class="error"><?= htmlspecialchars($erreurPseudo) ?></p>
    <?php endif; ?>
</form>

<?php if (empty($articles)): ?>
    <p class="empty">Aucun article<?= $pseudoFiltre !== '' ? ' pour « ' . htmlspecialchars($pseudoFiltre) . ' »' : '' ?>.</p>
<?php else: ?>
    <?php foreach ($articles as $a): ?>
        <article class="card">
            <h2>
                <a href="article.php?id=<?= (int)$a['id'] ?>">
                    <?= htmlspecialchars($a['titre']) ?>
                </a>
            </h2>
            <p class="meta">
                Par
                <a href="index.php?pseudo=<?= urlencode($a['pseudo']) ?>">
                    <?= htmlspecialchars($a['pseudo']) ?>
                </a>
                · <?= (int)$a['nb_commentaires'] ?> commentaire(s)
            </p>
            <?php if (!empty($a['categories'])): ?>
                <p class="categories"><?= htmlspecialchars($a['categories']) ?></p>
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars(extrait($a['description']))) ?></p>
            <a class="read-more" href="article.php?id=<?= (int)$a['id'] ?>">Lire la suite</a>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php require 'footer.php'; ?>