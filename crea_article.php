<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
authentification();

$erreur = '';
$titre = '';
$description = '';
$categorieId = '';

$categories = $pdo->query('SELECT id, nom FROM categorie ORDER BY nom')
                  ->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim((string)($_POST['titre'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $categorieId = trim((string)($_POST['categorie'] ?? ''));
    $token = (string)($_POST['csrf_token'] ?? '');

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $erreur = 'Le formulaire a expiré. Recharge la page et réessaie.';
    } elseif ($titre === '' || mb_strlen($titre) > 50) {
        $erreur = 'Le titre est obligatoire et ne doit pas dépasser 50 caractères.';
    } elseif ($description === '' || mb_strlen($description) > 1000) {
        $erreur = 'Le contenu est obligatoire et ne doit pas dépasser 1 000 caractères.';
    } elseif ($categorieId !== '' && !ctype_digit($categorieId)) {
        $erreur = 'La catégorie sélectionnée est invalide.';
    } else {
        $categorieValide = true;
        if ($categorieId !== '') {
            $checkCategorie = $pdo->prepare('SELECT id FROM categorie WHERE id = :id');
            $checkCategorie->execute(['id' => (int)$categorieId]);
            $categorieValide = (bool)$checkCategorie->fetchColumn();
        }

        if (!$categorieValide) {
            $erreur = 'La catégorie sélectionnée n’existe pas.';
        } else {
            try {
                $insert = $pdo->prepare(
                    'INSERT INTO article (id_auteur, titre, description, pseudo, categorie)
                     VALUES (:id_auteur, :titre, :description, :pseudo, :categorie)'
                );
                $insert->execute([
                    'id_auteur' => (int)$_SESSION['user_id'],
                    'titre' => $titre,
                    'description' => $description,
                    'pseudo' => (string)$_SESSION['pseudo'],
                    'categorie' => $categorieId === '' ? null : (int)$categorieId,
                ]);
                header('Location: article.php?id=' . (int)$pdo->lastInsertId());
                exit;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $erreur = 'Une erreur est survenue pendant la publication. Réessaie.';
            }
        }
    }
}

$titrePage = 'Créer un article';
require __DIR__ . '/header.php';
?>
<section class="auth-page">
    <div class="auth-container">
        <h1>Créer un article</h1>
        <p>Partage une publication avec la communauté.</p>

        <?php if ($erreur !== ''): ?>
            <p class="error-message"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="crea_article.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <label for="titre">Titre de l'article</label>
            <input type="text" id="titre" name="titre" maxlength="50" required
                   value="<?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?>">

            <label for="description">Contenu</label>
            <textarea id="description" name="description" rows="10" maxlength="1000" required><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>

            <label for="categorie">Catégorie</label>
            <select id="categorie" name="categorie">
                <option value="">Aucune catégorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= (int)$categorie['id'] ?>"
                        <?= (string)$categorie['id'] === $categorieId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categorie['nom'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Publier l'article</button>
        </form>
        <a href="index.php">Annuler et retourner à l'accueil</a>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
