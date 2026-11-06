<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$erreur = '';
$mode = $_GET['mode'] ?? 'connexion';

if (!in_array($mode, ['connexion', 'inscription'], true)) {
    $mode = 'connexion';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'inscription') {
        $mode = 'inscription';
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $pseudo = trim((string)($_POST['pseudo'] ?? ''));
        $mdp = (string)($_POST['mot_de_passe'] ?? '');
        $confirmation = (string)($_POST['confirmation'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = 'Saisis une adresse mail valide.';
        } elseif (mb_strlen($pseudo) < 3 || mb_strlen($pseudo) > 50) {
            $erreur = 'Le nom d’utilisateur doit contenir entre 3 et 50 caractères.';
        } elseif (strlen($mdp) < 8) {
            $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($mdp !== $confirmation) {
            $erreur = 'Les deux mots de passe ne correspondent pas.';
        } else {
            $check = $pdo->prepare(
                'SELECT id FROM `user`
                 WHERE email = :email OR pseudo = :pseudo
                 LIMIT 1'
            );
            $check->execute(['email' => $email,'pseudo' => $pseudo]);
            if ($check->fetch()) {
                $erreur = 'Cette adresse mail ou ce nom d’utilisateur est déjà utilisé.';
            } else {
                try {
                    $insert = $pdo->prepare(
                        'INSERT INTO `user` (email, mdp, pseudo, administrateur)
                         VALUES (:email, :mdp, :pseudo, 0)'
                    );
                    $insert->execute(['email' => $email, 'mdp' => password_hash($mdp, PASSWORD_DEFAULT),
                    'pseudo' => $pseudo]);
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$pdo->lastInsertId();
                    $_SESSION['pseudo'] = $pseudo;
                    $_SESSION['admin'] = false;
                    header('Location: index.php');
                    exit;
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $erreur = 'Une erreur est survenue lors de la création du compte.';
                }
            }
        }
    } elseif ($action === 'connexion') {
        $mode = 'connexion';
        $identifiant = trim((string)($_POST['identifiant'] ?? ''));
        $mdp = (string)($_POST['mot_de_passe'] ?? '');
        if ($identifiant === '' || $mdp === '') {
            $erreur = 'Renseigne ton pseudo ou ton mail et ton mot de passe.';
        } else {
            $stmt = $pdo->prepare(
                'SELECT id, email, pseudo, mdp, administrateur
                 FROM `user`
                 WHERE email = :email OR pseudo = :pseudo
                 LIMIT 1'
            );
            $stmt->execute(['email' => strtolower($identifiant),'pseudo' => $identifiant]);
            $user = $stmt->fetch();
            $valide = $user && password_verify($mdp, $user['mdp']);
            if ($user && !$valide && hash_equals((string)$user['mdp'], $mdp)) {
                $valide = true;
                $upgrade = $pdo->prepare(
                    'UPDATE `user` SET mdp = :mdp WHERE id = :id'
                );
                $upgrade->execute(['mdp' => password_hash($mdp, PASSWORD_DEFAULT), 'id' => $user['id']]);
            }
            if ($user && $valide) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['admin'] = (bool)$user['administrateur'];
                header('Location: index.php');
                exit;
            } else {
                $erreur = 'Identifiant ou mot de passe incorrect.';
            }
        }
    } else {
        $erreur = 'Action non reconnue.';
    }
}
$titrePage = $mode === 'inscription' ? 'Créer un compte' : 'Connexion';
require __DIR__ . '/header.php';
?>

<section class="auth-page">
    <div class="auth-container">
        <h1 id="auth-title">
            <?= $mode === 'inscription' ? 'Créer un compte' : 'Connexion' ?>
        </h1>
        <p id="auth-intro">
            <?= $mode === 'inscription'
                ? 'Rejoins la communauté Mon Blog.'
                : 'Connecte-toi pour accéder à ton compte.' ?>
        </p>
        <?php if ($erreur !== ''): ?>
            <p class="error-message">
                <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>
        <?php if ($mode === 'connexion'): ?>
            <form method="post" action="login.php">
                <input type="hidden" name="action" value="connexion">
                <label for="identifiant">Pseudo ou adresse mail</label>
                <input type="text" id="identifiant" name="identifiant" required autocomplete="username">
                <label for="mdp-connexion">Mot de passe</label>
                <input type="password" id="mdp-connexion" name="mot_de_passe" required autocomplete="current-password">
                <button type="submit">Se connecter</button>
            </form>
            <p>Pas encore de compte ?</p>
            <a href="login.php?mode=inscription">Créer un compte</a>
        <?php endif; ?>
        <?php if ($mode === 'inscription'): ?>
            <form method="post" action="login.php?mode=inscription">
                <input type="hidden" name="action" value="inscription">
                <label for="email">Adresse mail</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <label for="pseudo">Nom d'utilisateur</label>
                <input type="text" id="pseudo" name="pseudo" required minlength="3"  maxlength="50"
                autocomplete="username"
                value="<?= htmlspecialchars($_POST['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <label for="mdp-inscription">Mot de passe</label>
                <input type="password" id="mdp-inscription" name="mot_de_passe" required minlength="8"
                autocomplete="new-password">
                <label for="confirmation">Confirmer le mot de passe</label>
                <input type="password" id="confirmation" name="confirmation" required autocomplete="new-password">
                <button type="submit">Créer mon compte</button>
            </form>
            <p>Tu as déjà un compte ?</p>
            <a href="login.php?mode=connexion">Se connecter</a>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>