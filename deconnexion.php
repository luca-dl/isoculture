<?php
// deconnexion.php
require __DIR__ . '/session.php';
$_SESSION = [];        // 1. on vide les données de la session
session_destroy();     // 2. on détruit la session elle-même
header('Location: index.php');   // 3. retour à l'accueil
exit;
