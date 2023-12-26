<?php
session_start();
require 'Client.php';



try {
    $id_client = $_SESSION['id_client'];

    // Récupération des informations du client connecté
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id_client = :id_client");
    $stmt->bindParam(':id_client', $id_client);
    $stmt->execute();
    $client = $stmt->fetch();

    if (isset($_POST['numero_compte_destinataire']) && isset($_POST['montant_transfert']) && isset($_POST['numero_compte_emetteur']) && isset($_POST['type_operation'])) {
        // Récupération des données du formulaire
        $numero_compte_destinataire = $_POST['numero_compte_destinataire'];
        $montant_transfert = $_POST['montant_transfert'];
        $numero_compte_emetteur = $_POST['numero_compte_emetteur'];
        $type_operation = $_POST['type_operation'];


        // Vérification que le compte émetteur a suffisamment de fonds pour effectuer le transfert
        $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte_emetteur");
        $stmt->bindParam(':numero_compte_emetteur', $numero_compte_emetteur);
        $stmt->execute();
        $compte_emetteur = $stmt->fetch();

        $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte_destinataire");
        $stmt->bindParam(':numero_compte_destinataire', $numero_compte_destinataire);
        $stmt->execute();
        $compte_destinataire = $stmt->fetch();

        if (($compte_emetteur['solde'] >= $montant_transfert) and ($compte_destinataire)) {


            $nouveau_solde_destinataire = $compte_destinataire['solde'] + $montant_transfert;
            $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde WHERE numero_compte = :numero_compte_destinataire");
            $stmt->bindParam(':solde', $nouveau_solde_destinataire);
            $stmt->bindParam(':numero_compte_destinataire', $numero_compte_destinataire);
            $stmt->execute();

            
            // Mise à jour du solde du compte émetteur
            $nouveau_solde_emetteur = $compte_emetteur['solde'] - $montant_transfert;
            $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde WHERE numero_compte = :numero_compte_emetteur");
            $stmt->bindParam(':solde', $nouveau_solde_emetteur);
            $stmt->bindParam(':numero_compte_emetteur', $numero_compte_emetteur);
            $stmt->execute();

            // Mise à jour du solde du compte destinataire

            // Enregistrement de l'opération dans la table "operation"


            $date_operation = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO operation (type_operation, montant, date_operation, numero_compte) VALUES (:type_operation, :montant, :date_operation, :numero_compte_emetteur)");
            $stmt->bindParam(':type_operation', $type_operation);
            $stmt->bindParam(':montant', $montant_transfert);
            $stmt->bindParam(':date_operation', $date_operation);
            $stmt->bindParam(':numero_compte_emetteur', $numero_compte_emetteur);
            $stmt->execute();

            // Affichage d'un message de confirmation à l'utilisateur
            $msg = "Le transfert de compte numero: $numero_compte_emetteur ,le montant: $montant_transfert € vers le compte numero: $numero_compte_destinataire a été effectué avec succès";
            header("Location: client_profil.php?success_transfer=$msg");
        } else {
            $error =  "Solde du compte numero: $numero_compte_emetteur insuffisant !";
            header("Location: client_profil.php?error_transfer=$error");
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Transfert d'argent</title>
    </head>
    <body>
        <div class="container border border-primary rounded p-3">
            <button class="btn close float-right text-danger" onclick='closeFormTransfer();'>&times;</button>
            <h4>Transfert d'argent <span class='badge badge-info text-italic'>du compte numero:  <?= $_GET['numero_compte']; ?></span></h4>
            <form class='container' action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            

            <?php 
                $numero_compte_emetteur = $_GET['numero_compte'];
                $type_operation = $_GET['type_operation']; 
            ?>


                <input type="hidden" name="numero_compte_emetteur" value="<?php echo $numero_compte_emetteur; ?>">
                <input type="hidden" name="type_operation" value="<?php echo $type_operation; ?>">
                <div class="row align-items-end">
                    <div class="col col-12 col-md-5 col-sm-12 form-group">
                        <label for="numero_compte_destinataire">Numéro de compte destinataire:</label>
                        <input class='form-control' type="text" name="numero_compte_destinataire" required>
                    </div>
                    <div class="col col-12 col-md-5 col-sm-12 form-group">
                        <label for="montant_transfert">Montant à transférer:</label>
                        <input class='form-control' type="number" name="montant_transfert"  required>
                    </div>
                    <div class="col col-1 col-md-1 form-group">
                        <button class='btn btn-primary' type="submit">Envoyer</button>
                    </div>

                </div>
            </form>
        </div>
    </body>
    </html>

<?php
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
