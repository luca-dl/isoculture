#### Données personnelles

#### Traitements
| Traitement | Finalité | Base légale | Durée de conservation |
| --- | --- | --- | --- |
| Comptes utilisateurs | Permettre l'accès à l'espace personnel | Exécution du service demandé | 12 mois après la dernière connexion |
| Journal de connexion | Sécurité, détection d'abus | Intérêt légitime | 12 mois |

#### Données collectées et justification
- `email` — identifiant de connexion. Sans elle, aucune connexion possible.
- `mot_de_passe_hache` — authentification. Haché avec `password_hash()`, jamais réversible.
- `cree_le` — point de départ du calcul de conservation.
- `derniere_connexion` — application de la règle des 12 mois.

#### Données volontairement NON collectées
Nom, prénom, date de naissance, téléphone, adresse postale, adresse IP en clair :
aucune fonctionnalité d'Isoculture n'en dépend.

#### Hébergement
Base de données : Neon, région Europe (Francfort). Application : Render, région Frankfurt.

#### Droits des personnes
Toute demande d'accès, de rectification ou d'effacement est à adresser à <contact@example.test>.
