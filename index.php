<?php
// Allowlist : seules ces valeurs sont acceptées, tout le reste est refusé
$pagesAutorisees = ['accueil', 'contact', 'mentions'];
$page = $_GET['page'] ?? 'accueil';

if (!is_string($page) || !in_array($page, $pagesAutorisees, true)) {
    http_response_code(404);
    $page = 'accueil';
}

include __DIR__ . '/pages/' . $page . '.php';