<?php
session_start();
require 'Agent.php';
$id_client = $_GET['id_client'];
$numero_compte = $_GET['numero_compte'];
$type_operation = $_GET['type_operation'];
try {
    $id_agent = $_SESSION['id_agent'];

    // Récupération des informations de l'agent connecté
    $stmt = $conn->prepare("SELECT * FROM agents WHERE id_agent = :id_agent");
    $stmt->bindParam(':id_agent', $id_agent);
    $stmt->execute();
    $agent = $stmt->fetch();


    if (isset($_POST['id_client']) && isset($_POST['numero_compte']) && isset($_POST['montant_transfert']) && isset($_POST['numero_compte_destinataire']) && isset($_POST['type_operation']))
    {
        // Récupération des données du formulaire
        $montant_transfert = $_POST['montant_transfert'];
        $numero_compte = $_POST['numero_compte'];
        $id_client = $_POST['id_client'];
        $numero_compte_destinataire = $_POST['numero_compte_destinataire'];
        $type_operation=$_POST["type_operation"];

        // Vérification que le compte a suffisamment de fonds pour effectuer le transfert
        $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte");
        $stmt->bindParam(':numero_compte', $numero_compte);
        $stmt->execute();
        $compte_bancaire = $stmt->fetch();

        $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte_destinataire");
        $stmt->bindParam(':numero_compte_destinataire', $numero_compte_destinataire);
        $stmt->execute();
        $compte_bancaire_destinataire = $stmt->fetch();

        if($compte_bancaire['solde'] >= $montant_transfert && $compte_bancaire_destinataire){

            // Mise à jour du solde du compte emetteur
            $nouveau_solde_emetteur = $compte_bancaire['solde'] - $montant_transfert;
            $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde WHERE numero_compte = :numero_compte");
            $stmt->bindParam(':solde', $nouveau_solde_emetteur);
            $stmt->bindParam(':numero_compte', $numero_compte);
            $stmt->execute();

            // Mise à jour du solde du compte destinataire
            
            $nouveau_solde_destinataire = $compte_bancaire_destinataire['solde'] + $montant_transfert;
            $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde WHERE numero_compte = :numero_compte_destinataire");
            $stmt->bindParam(':solde', $nouveau_solde_destinataire);
            $stmt->bindParam(':numero_compte_destinataire', $numero_compte_destinataire);
            $stmt->execute();

            // Enregistrement de l'opération dans la table operation
            $date_operation = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO operation (id_operation, type_operation, montant, date_operation, numero_compte) VALUES (default, :type_operation, :montant, :dateop, :numero_compte)");
            $stmt->bindParam(':type_operation', $type_operation);
            $stmt->bindParam(':montant', $montant_transfert);
            $stmt->bindParam(':dateop', $date_operation);
            $stmt->bindParam(':numero_compte', $numero_compte);
            $stmt->execute();

            // Affichage d'un message de confirmation à l'utilisateur
            $message = "Le transfert de <strong>$montant_transfert</strong>  vers le compte <strong>$numero_compte_destinataire</strong> a été effectué avec succès";
            header("Location: agent_profil.php?id=$id_client&Ops=$type_operation&date=$date_operation&msg=$message");
        }
        else{
            echo ("Solde insuffisant ");
            header("Location: comptes_bancaires_client.php?id=$id_client");
        }
    }
            ?>
            <!DOCTYPE html>
                <html>
                <head>
                    <title>Transfert d'argent</title>
                </head>
                <body>
                    <div class="container border-left border-danger">
                        <h4>Transfert d'argent</h4>
                        <form action="Transfert.php" method="post">
                            <input type="hidden" name="id_client" value="<?php echo $id_client; ?>">
                            <input type="hidden" name="numero_compte" value="<?php echo $numero_compte; ?>">
                            <input type="hidden" name="type_operation" value="<?php echo $type_operation; ?>">
                            <div class="form-group">
                                <label for="numero_compte_destinataire">Numero compte  Destinataire :</label>
                                <input class="form-control" type="text" name="numero_compte_destinataire" id="numero_compte_destinataire" required>
                            </div>
                            <div class="form-group">
                                <label for="montant_transfert">montant_transfert :</label>
                                <input class="form-control" type="number" name="montant_transfert" id="montant_transfert" required>
                            </div>
                            <button class="btn btn-primary" type="submit">Envoyer</button>
                        </form>
                    </div>
                </body>
                </html>

            <?php
        
    
}

    catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        }