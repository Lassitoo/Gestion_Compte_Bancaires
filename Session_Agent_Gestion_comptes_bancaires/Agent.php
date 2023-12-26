<?php
require 'test.php';

class Agent {
    public $id_agent;
    public $nom_agent;
    public $email;
    public $mot_de_passe_agent;
    public $telephone_agent;

    public function __construct($id_agent, $nom_agent, $email, $mot_de_passe_agent, $telephone_agent){
        $this->id_agent = $id_agent;
        $this->nom_agent = $nom_agent;
        $this->email = $email;
        $this->mot_de_passe_agent = $mot_de_passe_agent;
        $this->telephone_agent = $telephone_agent;
    }

    public static function verifier_identification($email, $mot_de_passe) {
        global $conn;

        // Recherche de l'agent dans la base de données avec l'email et le mot de passe
        $stmt = $conn->prepare("SELECT * FROM agents WHERE email_agent = :email AND mot_de_passe_agent = :mot_de_passe");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->execute();
        $resultat = $stmt->fetch();
        
        if ($resultat !== false) {
            // Création d'un objet Agent avec les données de la base de données
            $agent = new Agent($resultat['id_agent'], $resultat['nom_agent'], $resultat['email_agent'], $resultat['mot_de_passe_agent'], $resultat['telephone_agent']);
            return $agent;
        } else {
            return false;
        }
    }
    

    public function approuver_demande($id_demande) {
        global $conn;

        // récupération des informations de la demande à approuver
        $stmt = $conn->prepare("SELECT * FROM demandes_creation_comptes_clients WHERE id_demande = :id_demande");
        $stmt->bindParam(':id_demande', $id_demande);
        $stmt->execute();
        $demande = $stmt->fetch();

        // insertion des informations de la demande approuvée dans la table clients
        $stmt = $conn->prepare("INSERT INTO clients (nom, prenom, email, mot_de_passe_client, adresse, telephone, piece_identite, numero_piece_identite) VALUES (:nom, :prenom, :email, :mot_de_passe, :adresse, :telephone, :piece_identite, :numero_piece_identite)");
        $stmt->bindParam(':nom', $demande['nom']);
        $stmt->bindParam(':prenom', $demande['prenom']);
        $stmt->bindParam(':email', $demande['email']);
        $stmt->bindParam(':mot_de_passe', $demande['mot_de_passe_client']);
        $stmt->bindParam(':adresse', $demande['adresse']);
        $stmt->bindParam(':telephone', $demande['telephone']);
        $stmt->bindParam(':piece_identite', $demande['piece_identite']);
        $stmt->bindParam(':numero_piece_identite', $demande['numero_piece_identite']);
        $stmt->execute();

        // suppression de la demande approuvée de la table demandes_creation_comptes_clients
        $stmt = $conn->prepare("DELETE FROM demandes_creation_comptes_clients WHERE id_demande = :id_demande");
        $stmt->bindParam(':id_demande', $id_demande);
        $stmt->execute();

        echo "Demande approuvée et compte client créé avec succès !";
    }
}
?>
