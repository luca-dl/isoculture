<?php
// index.php - affiche les visites des lieux culturels
require __DIR__ . '/db.php';

$sql = 'SELECT l.nom, l.categorie, l.ville, v.date_visite, v.frequentation
        FROM php_lieu l
        JOIN php_visite v ON v.lieu_id = l.id
        ORDER BY l.nom, v.date_visite';

$visites = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Projet Dev Web</title>
</head>
<body>
    <h1>Fréquentation des lieux culturels</h1>

    <table>
        <caption>Visites par lieu et par date</caption>
        <thead>
            <tr>
                <th scope="col">Lieu</th>
                <th scope="col">Catégorie</th>
                <th scope="col">Ville</th>
                <th scope="col">Date de visite</th>
                <th scope="col">Fréquentation (milliers)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($visites as $ligne): ?>
            <tr>
                <td><?= htmlspecialchars($ligne['nom']) ?></td>
                <td><?= htmlspecialchars($ligne['categorie']) ?></td>
                <td><?= htmlspecialchars($ligne['ville']) ?></td>
                <td><?= htmlspecialchars($ligne['date_visite']) ?></td>
                <td><?= htmlspecialchars(number_format($ligne['frequentation'], 1, ',', ' ')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
