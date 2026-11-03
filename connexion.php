<?php
// connexion.php
require_once __DIR__ . '/fonctions.php';
require __DIR__ . '/db.php';
require_once __DIR__ . '/classes/Utilisateur.php';
require_once __DIR__ . '/verrouillage.php';

require __DIR__ . '/session.php';

$erreur = null;
$email = ''; // Initialisation vide par défaut pour le formulaire HTML

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (est_verrouille($pdo, $email)) {
        http_response_code(429);
        exit("Trop de tentatives. Réessayez dans 15 minutes.");
    }

    $requete = $pdo->prepare("SELECT * FROM php_utilisateurs WHERE email = :email");
    $requete->execute([':email' => $email]);
    $ligne = $requete->fetch(PDO::FETCH_ASSOC);

    $ok = false;
    if ($ligne !== false) {
        $utilisateur = new Utilisateur($ligne['email'], $ligne['mot_de_passe_hache'], (int) $ligne['id']);
        $ok = $utilisateur->verifierMotDePasse($mot_de_passe);
    } else {
        password_verify($mot_de_passe, HASH_FACTICE);
    }

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['utilisateur_id'] = $utilisateur->getId();

        $maintenant = date('c');
        $id         = $utilisateur->getId();

        $maj = $pdo->prepare("UPDATE php_utilisateurs SET derniere_connexion = :d WHERE id = :id");
        $maj->bindParam(':d',  $maintenant, PDO::PARAM_STR);
        $maj->bindParam(':id', $id,         PDO::PARAM_INT);
        $maj->execute();

        header('Location: espace.php');
        exit;
    }

    enregistrer_echec($pdo, $email);
    
    $erreur = "Adresse e-mail ou mot de passe incorrect.";
    error_log("Échec de connexion (tentative sur un compte)");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Connexion — Isoculture</title></head>
<body>
<h1>Connexion</h1>

<?php if (isset($_GET['inscrit'])): ?>
    <p>Compte créé : vous pouvez vous connecter.</p>
<?php endif; ?>

<?php if ($erreur !== null): ?>
    <p role="alert">⚠️ <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Échec pour l'adresse : <?= e($email) ?></p>
<?php endif; ?>

<form method="post" action="connexion.php">
    <label for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

    <label for="mdp">Mot de passe</label>
    <input type="password" id="mdp" name="mot_de_passe" required>

    <button type="submit">Se connecter</button>
</form>
</body>
</html>