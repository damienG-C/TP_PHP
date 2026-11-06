<?php
declare(strict_types=1);

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$type = $_POST['type'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    if ($type === 'article') {

        // Vérifier que l'utilisateur est l'auteur de l'article
        $stmt = $pdo->prepare(
            'SELECT id_auteur FROM article WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if ( !$article || ( (int)$article['id_auteur'] !== (int)$_SESSION['user_id'] && empty($_SESSION['admin']))) {
        http_response_code(403);
        exit('Vous ne pouvez pas supprimer cet article.');
    }

        // Supprimer les commentaires puis l'article
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            'DELETE FROM commentaire WHERE article = :id'
        );
        $stmt->execute(['id' => $id]);

        $stmt = $pdo->prepare(
            'DELETE FROM article WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $pdo->commit();

        header('Location: index.php');
        exit;

    } elseif ($type === 'commentaire') {

        // Vérifier que l'utilisateur est l'auteur du commentaire
        $stmt = $pdo->prepare(
            'SELECT id_auteur, article FROM commentaire WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $commentaire = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commentaire ||((int)$commentaire['id_auteur'] !== (int)$_SESSION['user_id']&& empty($_SESSION['admin']))) {
        http_response_code(403);
        exit('Vous ne pouvez pas supprimer ce commentaire.');
    }

        // Supprimer le commentaire
        $stmt = $pdo->prepare(
            'DELETE FROM commentaire WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        header('Location: article.php?id=' . (int)$commentaire['article']);
        exit;
    }

    header('Location: index.php');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    exit('Une erreur est survenue lors de la suppression.');
}
