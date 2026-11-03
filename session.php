<?php
// session.php
ini_set('session.use_strict_mode', '1');    // Rejette les identifiants inventés
ini_set('session.use_only_cookies', '1');   // Bloque les identifiants dans l'URL
session_start();
?>