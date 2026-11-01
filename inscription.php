<?php
// inscription.php

// require : « inclus ce fichier, et arrête tout s'il manque ». db.php nous fournit $pdo.
require __DIR__ . '/db.php';
// require_once : pareil, mais ne l'inclut qu'une seule fois même si on le demande deux fois.
require_once __DIR__ . '/classes/Utilisateur.php';

$erreur = null;   // vaudra un message si quelque chose se passe mal ; null = pas d'erreur

// Cette page sert à deux choses : AFFICHER le formulaire (méthode GET) et le TRAITER (méthode POST).
// On ne traite donc que si le formulaire vient d'être envoyé.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // try / catch : on tente ; si une erreur (une « exception ») est lancée, on la rattrape plus bas.
    try {
        // $_POST contient ce que le formulaire a envoyé. `?? ''` : « sinon, une chaîne vide »
        // (évite une erreur si le champ est absent).
        $utilisateur = Utilisateur::creer($_POST['email'] ?? '', $_POST['mot_de_passe'] ?? '');

        // bindParam() a besoin de VARIABLES : on range donc les valeurs dans des variables.
        $email  = $utilisateur->getEmail();
        $hache  = $utilisateur->getMotDePasseHache();
        $creeLe = date('c');   // date et heure actuelles, au format ISO 8601

        // Requête préparée : les :noms sont des emplacements, remplis ensuite de façon sûre.
        // On ne concatène JAMAIS une donnée de l'utilisateur dans une requête (injection SQL).
        $sql = "INSERT INTO php_utilisateurs (email, mot_de_passe_hache, cree_le)
                VALUES (:email, :hache, :cree_le)";
        $requete = $pdo->prepare($sql);

        // bindParam(emplacement, variable, type) : lie la variable à l'emplacement, en précisant
        // le type attendu (ici, des chaînes de caractères).
        $requete->bindParam(':email',   $email,  PDO::PARAM_STR);
        $requete->bindParam(':hache',   $hache,  PDO::PARAM_STR);
        $requete->bindParam(':cree_le', $creeLe, PDO::PARAM_STR);
        $requete->execute();   // c'est ici que la ligne est réellement écrite en base

        // Inscription réussie : on redirige le navigateur vers la page de connexion...
        header('Location: connexion.php?inscrit=1');
        exit;   // ...et on arrête le script immédiatement (voir l'encadré plus bas).

    } catch (InvalidArgumentException $e) {
        // Rattrape les `throw` de la classe Utilisateur (e-mail invalide, mot de passe trop court).
        // Ces messages sont écrits pour l'utilisateur : on peut les lui afficher.
        $erreur = $e->getMessage();
    } catch (PDOException $e) {
        // Rattrape une erreur de la base (par exemple : e-mail déjà inscrit, car UNIQUE).
        // Ne JAMAIS afficher $e->getMessage() : il contient la structure de votre base.
        // On l'écrit dans le journal du serveur (sans donnée personnelle) et on affiche un
        // message neutre.
        error_log('Inscription : ' . $e->getMessage());
        $erreur = "Cette adresse est peut-être déjà utilisée.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Inscription — Isoculture</title></head>
<body>
<h1>Créer un compte</h1>

<?php if ($erreur !== null): ?>
    <?php /* htmlspecialchars() neutralise les caractères HTML : protection contre le XSS */ ?>
    <p role="alert">⚠️ <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php /* method="post" : les données partent dans le corps de la requête, pas dans l'URL */ ?>
<form method="post" action="inscription.php">
    <label for="email">Adresse e-mail</label>
    <?php /* name="email" : c'est ce nom qui devient la clé $_POST['email'] */ ?>
    <input type="email" id="email" name="email" required>

    <label for="mdp">Mot de passe (12 caractères minimum)</label>
    <input type="password" id="mdp" name="mot_de_passe" required minlength="12">

    <button type="submit">Créer mon compte</button>
</form>
</body>
</html>
