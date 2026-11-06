<?php
declare(strict_types=1);

/**
 * Lit le filtre pseudo et le nettoie s'il est absent ou invalide
 */
function lireFiltrePseudo(): string
{
    $pseudo = $_GET['pseudo'] ?? '';

    return is_string($pseudo) ? trim($pseudo) : '';
}

/**
 * Pseudos des auteurs ayant publié au moins un article.
 *
 * @return string[]
 */
function getPseudosAuteurs(PDO $pdo): array
{
    return $pdo->query('SELECT DISTINCT pseudo FROM article ORDER BY pseudo')
               ->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Articles avec leurs catégories et leur nombre de commentaires.
 * Si $pseudo n'est pas vide, ne garde que les articles de cet auteur.
 *
 * @return array<int, array<string, mixed>>
 */

function getArticles(PDO $pdo, string $pseudo = ''): array
{
    $sql = 'SELECT a.id, a.id_auteur, a.titre, a.description, a.pseudo,
                   c.nom AS categories,
                   (SELECT COUNT(*) FROM commentaire co WHERE co.article = a.id) AS nb_commentaires
            FROM article a
            LEFT JOIN categorie c ON c.id = a.categorie';

    $params = [];
    if ($pseudo !== '') {
        $sql .= ' WHERE a.pseudo = :pseudo';
        $params['pseudo'] = $pseudo;
    }

    $sql .= ' ORDER BY a.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $articles;
}


/**
 * Début d'un texte, pour l'aperçu dans la liste.
 */
function extrait(string $texte, int $longueur = 250): string
{
    return mb_strimwidth($texte, 0, $longueur, '…');
}

/**
 * Lit l'identifiant d'article, vaut 0 si absent ou invalide.
 */
function lireIdArticle(): int
{
    $brut = $_GET['id'] ?? '';

    return is_string($brut) && ctype_digit($brut) ? (int)$brut : 0;
}

/**
 * Un article avec le nom de sa catégorie, ou null s'il n'existe pas.
 *
 * @return array<string, mixed>|null
 */

function getArticle(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT a.id, a.id_auteur, a.titre, a.description, a.pseudo,
                c.nom AS categorie
         FROM article a
         LEFT JOIN categorie c ON c.id = a.categorie
         WHERE a.id = :id'
    );

    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $article === false ? null : $article;
}


/**
 * Commentaires d'un article, du plus ancien au plus récent.
 *
 * @return array<int, array<string, mixed>>
 */

function getCommentaires(PDO $pdo, int $idArticle): array
{
    $stmt = $pdo->prepare(
        'SELECT id, id_auteur, pseudo, description
         FROM commentaire
         WHERE article = :id
         ORDER BY id ASC'
    );

    $stmt->execute(['id' => $idArticle]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $commentaires;
}


/**
 * Lit le texte du formulaire de commentaire ('' si absent ou invalide).
 */
function lireTexteCommentaire(): string
{
    $brut = $_POST['commentaire'] ?? '';

    return is_string($brut) ? trim($brut) : '';
}

/**
 * Vérifie un commentaire. Renvoie le message d'erreur, ou '' si tout va bien.
 */
function validerCommentaire(string $texte): string
{
    if ($texte === '') {
        return 'Le commentaire ne peut pas être vide.';
    }
    if (mb_strlen($texte) > 1000) {
        return 'Le commentaire ne doit pas dépasser 1000 caractères.';
    }

    return '';
}

/**
 * Enregistre un commentaire.
 */
function ajouterCommentaire(PDO $pdo, int $idArticle, int $idAuteur, string $pseudo, string $texte): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO commentaire (id_auteur, pseudo, article, description)
         VALUES (:id_auteur, :pseudo, :article, :description)'
    );
    $stmt->execute([
        'id_auteur'   => $idAuteur,
        'pseudo'      => $pseudo,
        'article'     => $idArticle,
        'description' => $texte,
    ]);
}
?>