<?php
// deconnexion.php
require __DIR__ . '/session.php';

$_SESSION = [];          // Vide les données
session_destroy();       // Détruit le fichier de session côté serveur

header('Location: connexion.php');
exit;
?>
