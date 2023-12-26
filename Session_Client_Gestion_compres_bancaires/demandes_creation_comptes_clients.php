<?php
// informations de connexion
require 'test.php';

// création d'une instance PDO pour la connexion à la base de données
try {
   

    // vérification de la soumission du formulaire
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // récupération des valeurs du formulaire
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $email = $_POST["email"];
        $mot_de_passe = $_POST["mot_de_passe"];
        $mot_de_passe_confirm = $_POST["mot_de_passe_confirm"];
        $adresse = $_POST["adresse"];
        $telephone = $_POST["telephone"];
        $piece_identite = $_POST["piece_identite"];
        $numero = $_POST["numero_piece_identite"];

        // Vérification que les mots de passe sont identiques
        if ($mot_de_passe !== $mot_de_passe_confirm) {
            echo "Les mots de passe ne correspondent pas !";
        } else {
            // insertion des valeurs dans la table demandes_creation_comptes_clients
            $stmt = $conn->prepare("INSERT INTO demandes_creation_comptes_clients (nom, prenom, email, mot_de_passe_client, adresse, telephone, piece_identite, numero_piece_identite) VALUES (:nom, :prenom, :email, :mot_de_passe, :adresse, :telephone, :piece_identite, :numero_piece_identite)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':piece_identite', $piece_identite);
            $stmt->bindParam(':numero_piece_identite', $numero);
            $stmt->execute(); ?>
            <script>
                alerte("Demande de création de compte client enregistrée avec succès !");
            </script>
            <?php
        }
    }
} catch(PDOException $e) {
    // afficher un message en cas d'erreur de la connexion
    echo "Échec de la connexion à la base de données : " . $e->getMessage();
}
?>
