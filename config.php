<?php
declare(strict_types=1); //forcer le typage

session_start();

//Affichage des erreurs pour le développement, à retirer pour le rendu
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//Connexion à la base
require_once __DIR__ . '/pdo.php';


//Redirige vers la page de connexion si personne n'est connecté
function authentification(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit(0);
    }
}
?>