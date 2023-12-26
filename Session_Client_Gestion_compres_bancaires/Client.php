<?php
require 'test.php';

class Client {
    public $id_client;
    public $nom_client;
    public $prenom_client;
    public $email_client;
    public $mot_de_passe_client;
    public $telephone_client;
    public $adresse_client;
    public $status_client;

    public function __construct($id_client, $nom_client, $prenom_client,$email_client, $mot_de_passe_client, $telephone_client,$adresse_client,$status_client ){
        $this->id_client = $id_client;
        $this->nom_client = $nom_client;
        $this->prenom_client=$prenom_client;
        $this->email_client= $email_client;
        $this->mot_de_passe_client = $mot_de_passe_client;
        $this->telephone_client = $telephone_client;
        $this->adresse_client = $adresse_client;
        $this->status_client=$status_client; 
    }

    public static function verifier_identification($email_client, $mot_de_passe_client) {
        global $conn;

        // Recherche de l'agent dans la base de données avec l'email et le mot de passe
        $stmt = $conn->prepare("SELECT * FROM clients WHERE email_client = :email_client AND mot_de_passe_client = :mot_de_passe_client");
        $stmt->bindParam(':email_client', $email_client);
        $stmt->bindParam(':mot_de_passe_client', $mot_de_passe_client);
        $stmt->execute();
        $resultat = $stmt->fetch();
        
        if ($resultat !== false) {
            // Création d'un objet Agent avec les données de la base de données
            $client = new Client($resultat['id_client'], $resultat['nom_client'], $resultat['prenom_client'], $resultat['email_client'],$resultat['mot_de_passe_client'], $resultat['telephone_client'],$resultat['adresse_client'],$resultat['status_client']);
            return $client;
        } else {
            return false;
        }
    }
}
?>
