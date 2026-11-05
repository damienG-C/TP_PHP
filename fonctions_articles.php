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
    $sql = 'SELECT a.id, a.titre, a.description, a.pseudo,
                   GROUP_CONCAT(DISTINCT c.nom ORDER BY c.nom SEPARATOR \', \') AS categories,
                   (SELECT COUNT(*) FROM commentaire co WHERE co.article = a.id) AS nb_commentaires
            FROM article a
            LEFT JOIN article_categorie ac ON ac.article_id = a.id
            LEFT JOIN categorie c ON c.id = ac.categorie_id';

    $params = [];
    if ($pseudo !== '') {
        $sql .= ' WHERE a.pseudo = :pseudo';
        $params['pseudo'] = $pseudo;
    }

    $sql .= ' GROUP BY a.id, a.titre, a.description, a.pseudo
              ORDER BY a.id DESC';

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