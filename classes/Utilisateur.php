<?php
// classes/Utilisateur.php
// Une classe = un plan. Ce fichier décrit ce qu'est un « Utilisateur » ; c'est tout.

class Utilisateur
{
    // PROPRIÉTÉS : ce que chaque Utilisateur « sait ».
    // `private` : seul le code de cette classe peut les lire ou les modifier.
    // Le `?` devant int signifie « un entier, ou null » (null tant que la base n'a pas donné d'id).
    private ?int $id;
    private string $email;
    private string $motDePasseHache;   // le HACHAGE du mot de passe, jamais le mot de passe lui-même

    // CONSTRUCTEUR : appelé automatiquement par `new Utilisateur(...)`.
    // `?int $id = null` : paramètre facultatif, qui vaut null si on ne le donne pas.
    public function __construct(string $email, string $motDePasseHache, ?int $id = null)
    {
        // Validation dans le constructeur : l'objet ne peut pas naître incohérent.
        // filter_var() renvoie false si la chaîne n'a pas la forme d'une adresse e-mail.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // `throw` interrompt la création de l'objet et signale l'erreur à l'appelant.
            throw new InvalidArgumentException("Adresse e-mail invalide.");
        }

        // `$this` = « cet objet-ci ». On range chaque valeur dans la propriété correspondante.
        // trim() enlève les espaces autour ; strtolower() met en minuscules (Paul@x.fr = paul@x.fr).
        $this->email = strtolower(trim($email));
        $this->motDePasseHache = $motDePasseHache;
        $this->id = $id;
    }

    /**
     * Fabrique un Utilisateur à partir d'un mot de passe en clair.
     * Le clair entre ici, il n'en ressort pas : il n'est jamais stocké dans une propriété.
     */
    // `static` : on l'appelle sur la classe (Utilisateur::creer(...)), sans objet existant.
    // `: self` : la méthode renvoie un objet de cette même classe.
    public static function creer(string $email, string $motDePasseClair): self
    {
        // strlen() compte les caractères : on refuse un mot de passe trop court.
        if (strlen($motDePasseClair) < 12) {
            throw new InvalidArgumentException("Le mot de passe doit faire au moins 12 caractères.");
        }

        // password_hash() fabrique le hachage (avec son sel). `new self(...)` construit
        // un Utilisateur : le constructeur ci-dessus est alors appelé.
        return new self($email, password_hash($motDePasseClair, PASSWORD_DEFAULT));
    }

    /**
     * Le SEUL point du programme où l'on vérifie un mot de passe.
     * L'appelant ne voit jamais le hachage.
     */
    public function verifierMotDePasse(string $clair): bool
    {
        // La fonction PHP de vérification recalcule l'empreinte du texte saisi et la compare au
        // hachage stocké. Elle renvoie true (c'est le bon mot de passe) ou false.
        return password_verify($clair, $this->motDePasseHache);
    }

    // GETTERS : des méthodes qui permettent de LIRE une propriété `private` de l'extérieur.
    // Il n'y a volontairement aucun « setter » : personne ne modifie l'objet après sa création.
    public function getEmail(): string { return $this->email; }
    public function getId(): ?int      { return $this->id; }

    /** Utilisé uniquement par la couche de persistance, au moment de l'INSERT. */
    public function getMotDePasseHache(): string { return $this->motDePasseHache; }
}
