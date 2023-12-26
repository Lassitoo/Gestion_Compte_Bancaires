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

    
    if (isset($_POST['id_client']) && isset($_POST['numero_compte']) && isset($_POST['montant_depose'])&& isset($_POST['type_operation']))
    {
            // Récupération du montant déposé
            $montant_depose = $_POST['montant_depose'];
            $numero_compte = $_POST['numero_compte'];
            $id_client = $_POST['id_client'];
            $type_operation=$_POST["type_operation"];
            // Mise à jour du solde du compte bancaire

            $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte");
            $stmt->bindParam(':numero_compte', $numero_compte);
            $stmt->execute();
            $compte_bancaire = $stmt->fetch();
            
            $nouveau_solde = $compte_bancaire['solde'] + $montant_depose;
            $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde WHERE numero_compte = :numero_compte");
            $stmt->bindParam(':solde', $nouveau_solde);
            $stmt->bindParam(':numero_compte', $numero_compte);
            $stmt->execute();

            $date_operation = date('Y-m-d H:i:s');
            
            $stmt = $conn->prepare("INSERT into OPERATION (id_operation,type_operation,montant,date_operation,numero_compte) values(default,:type_operation,:montant,:dateop,:numero_compte)");
            $stmt->bindParam(':type_operation',$type_operation );
            $stmt->bindParam(':montant', $montant_depose);
            $stmt->bindParam(':dateop', $date_operation);
            $stmt->bindParam(':numero_compte', $numero_compte);
            $stmt->execute();

                     
            // Redirection vers la liste des comptes bancaires du client
            header("Location: agent_profil.php?id=$id_client&Ops=$type_operation&date=$date_operation");

            exit();
        }

        // Affichage du formulaire de dépôt
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Dépôt sur le compte </title>
        </head>
        <body>
            <div class="container border-left border-primary">
                <h4>Dépôt sur le compte numero <?php echo $numero_compte; ?></h4>
                <form action="depot.php" method="post">
                    <input type="hidden" name="id_client" value="<?php echo $id_client; ?>">
                    <input type="hidden" name="type_operation" value="<?php echo $type_operation; ?>">
                    <input type="hidden" name="numero_compte" value="<?php echo $numero_compte; ?>">
                    <div class="form-group">
                        <label for="montant_depose">Montant déposé :</label>
                        <input class="form-control" type="number" name="montant_depose" id="montant_depose" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Valider</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
catch(PDOException $e) {
    $error =  $e->getMessage();
    // Redirection vers la liste des comptes bancaires du client
    header("Location: agent_profil.php?errorOps=$error&Ops=$type_operation");
           
    }
    