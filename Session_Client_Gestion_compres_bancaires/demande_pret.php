<?php
session_start();
require 'Client.php';



try {


    $id_client = $_SESSION['id_client'];
    $etat_demande = "en_attente";
    
    // Récupération des informations du client connecté
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id_client = :id_client");
    $stmt->bindParam(':id_client', $id_client);
    $stmt->execute();
    $client = $stmt->fetch();

    
    if (isset($_POST['numero_compte']) && isset($_POST['montant'])  && isset($_POST['type_operation'])) {
            // Récupération des données du formulaire
        $numero_compte = $_POST['numero_compte'];
        $montant = $_POST['montant'];
        $type_operation = $_POST['type_operation'];




        $stmt = $conn->prepare("SELECT * FROM demande_pret WHERE numero_compte = :numero_compte");
        $stmt->bindParam(':numero_compte', $numero_compte);
        $stmt->execute();
        $demande_pret = $stmt->fetch();

    
        if ($demande_pret)

        {
            // deja demandee demande_carnetcheque

            $response =  "Deja demande pret en cours ... ";
            
            header("Location: client_profil.php?pret_existe=$response");
        }
        else 
        {



        $stmt = $conn->prepare("INSERT INTO demande_pret (etat_demande,montant,numero_compte) VALUES ( :etat_demande,:montant, :numero_compte)");
        $stmt->bindParam(':etat_demande', $etat_demande);
        $stmt->bindParam(':numero_compte', $numero_compte);
        $stmt->bindParam(':montant', $montant);

        $stmt->execute();
    

        
        
        // Enregistrement de l'opération dans la table "operation"
        
        
        $date_operation = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO operation (type_operation, montant, date_operation, numero_compte) VALUES (:type_operation, :montant, :date_operation, :numero_compte_emetteur)");
        $stmt->bindParam(':type_operation', $type_operation);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':date_operation', $date_operation);
        $stmt->bindParam(':numero_compte_emetteur', $numero_compte);
        $stmt->execute();

        $pret_success = "Demande pret fait avec success !";
        header("Location: client_profil.php?pret_en_cours=$pret_success");
        exit();
        
        }
            
    }        
 ?>
 
 

 <!DOCTYPE html>
    <html>
    <head>
        <!-- <title>demande pret</title> -->
    </head>
    <body>
        <button class="btn close text-danger float-right" onclick="closeFormTransfer();">&times;</button>
        <h4>Demande pret <span class='badge badge-info'>N&deg;<?=$_GET['numero_compte']?></span></h4>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
        

        <?php 
            $numero_compte = $_GET['numero_compte'];
            $type_operation = $_GET['type_operation'];
        ?>
            <input type="hidden" name="numero_compte" value="<?php echo $numero_compte; ?>">
            <input type="hidden" name="type_operation" value="<?php echo $type_operation; ?>">
            <div class="row align-items-end">
                <div class="col col-12 col-md-10 col-sm-12 form-group">
                    <label for="montant">montant du pret:</label>
                    <input class='form-control' type="number" min='0' name="montant" id='montant' required>
                </div>
                <div class="col col-12 col-md-1 form-group">
                    <button class='btn btn-primary' type="submit">Envoyer</button>
                </div>
            </div>
            
        </form>
    </body>
    </html>


    
 <?php   
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
