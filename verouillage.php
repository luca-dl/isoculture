<?php
// verrouillage.php — limitation des tentatives de connexion (atelier 7).
// À copier tel quel à la racine du projet.

// Hachage factice : celui d'un mot de passe quelconque, calculé une fois pour toutes. Il sert à dépenser
// le même temps de calcul quand le compte n'existe pas (voir la modification 2 dans connexion.php).
const HASH_FACTICE = '$2y$10$tS9UNk9hez8IbNfbIetLReKeGMWZrlqTa5D9ZrCJQbkPuS4mOoisO';

// Clé de comptage : un HMAC (haché AVEC une clé secrète), jamais la valeur en clair. Un simple SHA-256
// d'une adresse IPv4 se retrouverait par force brute (il n'existe que 2^32 adresses) ; sans la clé, non.
function cle_de_comptage(string $valeur): string
{
    // La clé n'est pas dans le code : on la lit dans l'environnement du serveur (variable SEL_APPLICATIF).
    $sel = getenv('SEL_APPLICATIF');

    if ($sel === false || $sel === '') {
        // Variable absente. En local, on se rabat sur une valeur de développement. En ligne (HTTPS), on refuse :
        // utiliser en silence une valeur connue de tous rendrait le hachage inutile.
        $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        if ($https) {
            throw new RuntimeException("La variable d'environnement SEL_APPLICATIF n'est pas définie sur le serveur.");
        }
        $sel = 'sel-de-developpement';
    }

    return hash_hmac('sha256', $valeur, $sel);
}

// Vrai si cet e-mail (5 échecs) ou cette adresse IP (20 échecs) a trop échoué ces 15 dernières minutes.
function est_verrouille(PDO $pdo, string $email): bool
{
    $maintenant = time();

    // Durée de conservation : une heure. Tout ce qui est plus ancien est supprimé à chaque appel,
    // pour ne jamais garder ces pseudonymes plus longtemps que nécessaire.
    $pdo->prepare("DELETE FROM php_tentatives WHERE horodatage < :limite")
        ->execute([':limite' => $maintenant - 3600]);

    $parEmail = $pdo->prepare("SELECT COUNT(*) FROM php_tentatives WHERE email_hache = :cle AND horodatage > :depuis");
    $parEmail->execute([':cle' => cle_de_comptage($email), ':depuis' => $maintenant - 900]);

    $parIp = $pdo->prepare("SELECT COUNT(*) FROM php_tentatives WHERE ip_hachee = :cle AND horodatage > :depuis");
    $parIp->execute([':cle' => cle_de_comptage($_SERVER['REMOTE_ADDR'] ?? ''), ':depuis' => $maintenant - 900]);

    return (int) $parEmail->fetchColumn() >= 5 || (int) $parIp->fetchColumn() >= 20;
}

// Note un échec de connexion. À appeler pour un compte inconnu ET pour un mauvais mot de passe.
function enregistrer_echec(PDO $pdo, string $email): void
{
    $pdo->prepare("INSERT INTO php_tentatives (email_hache, ip_hachee, horodatage) VALUES (:e, :i, :h)")
        ->execute([
            ':e' => cle_de_comptage($email),
            ':i' => cle_de_comptage($_SERVER['REMOTE_ADDR'] ?? ''),
            ':h' => time(),
        ]);
}
