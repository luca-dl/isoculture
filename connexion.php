<?php
// connexion.php
require __DIR__ . '/db.php';
require_once __DIR__ . '/classes/Utilisateur.php';

// session_start() : active la session PHP. Elle permet de « se souvenir » de l'utilisateur
// d'une page à l'autre (via $_SESSION). À appeler avant tout affichage.
session_start();
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On normalise l'e-mail saisi de la même façon que lors de l'inscription.
    $email = strtolower(trim($_POST['email'] ?? ''));

    // 1. On cherche la ligne de la base qui correspond à cet e-mail.
    $requete = $pdo->prepare("SELECT * FROM php_utilisateurs WHERE email = :email");
    $requete->bindParam(':email', $email, PDO::PARAM_STR);
    $requete->execute();
    // fetch() renvoie la ligne trouvée sous forme de tableau, ou `false` s'il n'y en a pas.
    $ligne = $requete->fetch(PDO::FETCH_ASSOC);

    // 2. On vérifie le mot de passe, seulement si le compte existe.
    $ok = false;
    if ($ligne !== false) {
        // On reconstruit un objet Utilisateur à partir de la ligne de la base.
        // (int) convertit l'id (qui arrive sous forme de texte) en entier.
        $utilisateur = new Utilisateur($ligne['email'], $ligne['mot_de_passe_hache'], (int) $ligne['id']);
        // C'est la méthode de la classe qui compare : on ne touche jamais au hachage ici.
        $ok = $utilisateur->verifierMotDePasse($_POST['mot_de_passe'] ?? '');
    }

    if ($ok) {
        // 3. Connexion réussie : on mémorise l'id dans la session. Tant qu'elle existe,
        //    les autres pages savent que cette personne est connectée.
        $_SESSION['utilisateur_id'] = $utilisateur->getId();

        // On note la date de cette connexion (utile pour la durée de conservation).
        $maintenant = date('c');
        $id         = $utilisateur->getId();

        $maj = $pdo->prepare("UPDATE php_utilisateurs SET derniere_connexion = :d WHERE id = :id");
        $maj->bindParam(':d',  $maintenant, PDO::PARAM_STR);
        $maj->bindParam(':id', $id,         PDO::PARAM_INT);   // PARAM_INT : un entier
        $maj->execute();

        // On envoie l'utilisateur vers son espace, et on arrête le script.
        header('Location: espace.php');
        exit;
    }

    // Si on arrive ici : compte inconnu OU mot de passe faux.
    // Message identique dans les deux cas : on ne révèle pas quelles adresses existent.
    $erreur = "Adresse e-mail ou mot de passe incorrect.";
    // Le journal dit qu'il y a eu un échec, mais ni qui, ni avec quel mot de passe.
    error_log("Échec de connexion (tentative sur un compte)");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Connexion — Isoculture</title></head>
<body>
<h1>Connexion</h1>

<?php /* ?inscrit=1 est ajouté par inscription.php après une inscription réussie */ ?>
<?php if (isset($_GET['inscrit'])): ?>
    <p>Compte créé : vous pouvez vous connecter.</p>
<?php endif; ?>

<?php if ($erreur !== null): ?>
    <p role="alert">⚠️ <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="post" action="connexion.php">
    <label for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" required>

    <label for="mdp">Mot de passe</label>
    <input type="password" id="mdp" name="mot_de_passe" required>

    <button type="submit">Se connecter</button>
</form>
</body>
</html>
