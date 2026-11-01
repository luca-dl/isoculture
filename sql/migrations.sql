-- 2026-09-15 : ajout du site web officiel de chaque lieu
ALTER TABLE php_lieu ADD COLUMN site_web TEXT;

-- AAAA-MM-JJ : table des comptes utilisateurs (transférée de SQLite vers Neon par export)
CREATE TABLE php_utilisateurs (
    id                 INTEGER PRIMARY KEY AUTOINCREMENT,
    email              TEXT NOT NULL UNIQUE,
    mot_de_passe_hache TEXT NOT NULL,
    cree_le            TEXT NOT NULL,
    derniere_connexion TEXT
);
