<?php
// membres.php
require __DIR__ . '/db.php';
require_once __DIR__ . '/fonctions.php';

$noms = $pdo->query("SELECT nom_affichage FROM php_utilisateurs WHERE nom_affichage IS NOT NULL")
            ->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Membres</title></head>
<body>
<h1>Membres</h1>
<ul>
<?php foreach ($noms as $nom): ?>
    <li><?= e($nom) ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
