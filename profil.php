<?php
// profil.php
require __DIR__ . '/session.php';
require __DIR__ . '/db.php';
require_once __DIR__ . '/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}
$id = (int) $_SESSION['utilisateur_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maj = $pdo->prepare("UPDATE php_utilisateurs SET nom_affichage = :nom WHERE id = :id");
    $maj->execute([':nom' => $_POST['nom_affichage'] ?? '', ':id' => $id]);
    header('Location: profil.php');
    exit;
}

$lecture = $pdo->prepare("SELECT nom_affichage FROM php_utilisateurs WHERE id = :id");
$lecture->execute([':id' => $id]);
$nom = $lecture->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Mon profil</title></head>
<body>
<h1>Mon profil</h1>
<p>Nom affiché : <?= e($nom) ?></p>
<form method="post">
    <input type="text" name="nom_affichage">
    <button type="submit">Enregistrer</button>
</form>
<p><a href="membres.php">Voir les membres</a></p>
</body>
</html>
