<?php
// espace.php
session_start();   // sans cet appel, $_SESSION est vide : on ne retrouverait pas l'utilisateur

// isset() vérifie qu'une variable existe. Si l'id n'est pas en session, personne n'est connecté.
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');   // on renvoie le visiteur vers le formulaire
    exit;   // ← indispensable : sans lui, la page continue de s'exécuter
}
?>
<h1>Mon espace</h1>
<p>Votre carte Isoculture s'affichera ici — séance n°4 (semestre 4).</p>
<p><a href="deconnexion.php">Se déconnecter</a></p>
