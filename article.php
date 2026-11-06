<?php
declare(strict_types=1);

require_once 'config.php';
require_once 'fonctions_articles.php';

authentification();

// --- Article demandé (?id=...) ---
$idArticle = lireIdArticle();
$article   = $idArticle > 0 ? getArticle($pdo, $idArticle) : null;

if ($article === null) {
    http_response_code(404);
    $titrePage = 'Article introuvable';
    require 'header.php';
    ?>
    <h1>Article introuvable</h1>
    <p><a href="index.php">Retour à la liste des articles</a></p>
    <?php
    require 'footer.php';
    exit(0);
}

// --- Ajout d'un commentaire (formulaire en POST) ---
$texte  = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texte  = lireTexteCommentaire();
    $erreur = validerCommentaire($texte);

    if ($erreur === '') {
        ajouterCommentaire(
            $pdo,
            $idArticle,
            (int)$_SESSION['user_id'],
            (string)$_SESSION['pseudo'],
            $texte
        );
        // Redirection : évite le renvoi du formulaire si on recharge la page
        header('Location: article.php?id=' . $idArticle);
        exit(0);
    }
}

$commentaires = getCommentaires($pdo, $idArticle);

// --- Affichage ---
$titrePage = $article['titre'];
require 'header.php';
?>

<article class="card">
    <h1><?= htmlspecialchars($article['titre']) ?></h1>
    <p class="meta">
        Par
        <a href="index.php?pseudo=<?= urlencode($article['pseudo']) ?>">
            <?= htmlspecialchars($article['pseudo']) ?>
        </a>
    </p>
    <?php if (!empty($article['categorie'])): ?>
        <p class="categories"><?= htmlspecialchars($article['categorie']) ?></p>
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($article['description'])) ?></p>
</article>

<a class="back-link" href="index.php">&larr; Retour aux articles</a>

<section class="comments">
    <h2>Commentaires (<?= count($commentaires) ?>)</h2>

    <?php if (empty($commentaires)): ?>
        <p class="empty">Aucun commentaire pour le moment.</p>
    <?php else: ?>
        <?php foreach ($commentaires as $c): ?>
            <div class="comment">
                <p class="meta"><strong><?= htmlspecialchars($c['pseudo']) ?></strong></p>
                <p><?= nl2br(htmlspecialchars($c['description'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<section class="comment-form">
    <h2>Ajouter un commentaire</h2>
    <form method="post" action="article.php?id=<?= $idArticle ?>">
        <label for="commentaire">Votre commentaire :</label>
        <textarea name="commentaire" id="commentaire" rows="4" maxlength="1000" required
                  <?= $erreur !== '' ? 'autofocus' : '' ?>><?= htmlspecialchars($texte) ?></textarea>
        <?php if ($erreur !== ''): ?>
            <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>
        <button type="submit">Publier</button>
    </form>
</section>

<?php require 'footer.php'; ?>