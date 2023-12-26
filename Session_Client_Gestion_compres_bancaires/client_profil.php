<?php
session_start();
if (!isset($_SESSION['id_client'])) {
    header('Location: connexion_client.php');
}
require 'Client.php';

try {
    $id_client = $_SESSION['id_client'];

    // Récupération des informations de l'agent connecté
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id_client = :id_client");
    $stmt->bindParam(':id_client', $id_client);
    $stmt->execute();
    $client = $stmt->fetch();


    ///////////////////////////////////////////////////////////////////////////////////////////



    // Récupération de la liste des comptes
    $stmt = $conn->prepare("SELECT * FROM compte_bancaire where  id_client = :id_client");
    $stmt->bindParam(':id_client', $client['id_client']);
    $stmt->execute();
    $comptes_bancaires = $stmt->fetchAll();

    // Affichage du profil du client et de la liste de ces comptes bancaires
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Profil Client</title>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="../fontAwesome/css/font-awesome.css">
    </head>
    <body>
        <div class="container">
        <h1>Profil Client</h1>
        <p>Bienvenue, <?php echo $client['nom_client']. " " .$client['prenom_client']; ?></p>
        <?php $solde=0 ; if(count($comptes_bancaires) >0):?>
            <?php foreach($comptes_bancaires as $compte):?>
                <?php $solde += $compte['solde']; ?>
            <?php endforeach?>
        <?php endif;?>
        <a class='btn btn-primary float-right' href="<?php echo pathinfo($_SERVER['REQUEST_URI'])['dirname']; ?>/deconnexion.php" ><i class="fa fa-sign-out" aria-hidden="true"></i></a> <!-- <<==== Deconnexion button ]] -->
        <span class="btn btn-success float-right mx-5 font-italic font-weight-bold ">Solde totale = <?=$solde?> &euro;</span> <!-- <<========== Solde Total ]] -->

        <h2>Liste de comtes bancaires</h2>
        <?php if (count($comptes_bancaires) === 0) { ?>
            <p>Aucun compte bancaire enregistré pour ce client.</p>
            <?php } else { ?>
                <table class="table table-striped table-sm" >
                    <thead>
                        <tr>
                            <th scop="row">Numéro de compte</th>
                            <th>Solde</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($comptes_bancaires as $compte) { ?>
                            <tr>
                                <td><?php echo $compte['numero_compte']; ?></td>
                                <td><?php echo $compte['solde']; ?> &euro;</td>
                                <td>
                                    <button class="btn btn-outline" onclick="openTransferForm(<?= $compte['numero_compte'] ?>,'Transfert');">
                                        <i class="fa fa-exchange" aria-hidden="true"></i>
                                    </button>
                                    
                                    <button class="btn btn-outline text-success" onclick="openPretForm(<?= $compte['numero_compte'] ?>, 'Demande pret')">
                                        <i class="fa fa-money" aria-hidden="true"></i>
                                    </button>
                                    
                                    <button class="btn btn-outline text-warning" onclick="demandeCarnetCheque(<?= $compte['numero_compte'] ?>);">
                                        <i class="fa fa-book" aria-hidden="true"></i>
                                    </button>

                                    <button class="btn btn-outline text-info" onclick="listeOperation(<?= $id_client ?>,<?= $compte['numero_compte'] ?>);">
                                        <i class="fa fa-folder-open" aria-hidden="true"></i>
                                    </button>
                                    
                                </td>
                            </tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
<?php } 
//////////////////////////////////////////////////////////////////////////////////////////////////////////


?>
            
            <div class="row">
                <div class="col col-12 border-top p-2" id='responseFormTransfert'></div>
                <div class="col col-12 col-md-12 mt-3" id="responseListOperation">
                    <?php if(isset($_GET['success_transfer'])): ?>
                        <div class="alert alert-success text-center p-3">
                            <button class="btn close text-danger float-right" onclick='closeAlertOfTransfer()' >&times;</button>
                            <div><?=$_GET['success_transfer']?></div>
                        </div>
                    <?php elseif(isset($_GET['error_transfer'])): ?>
                        <div class="alert alert-danger text-center p-3">
                            <button class="btn close text-danger float-right" onclick='closeAlertOfTransfer()' >&times;</button>
                            <div><?=$_GET['error_transfer']?></div>
                        </div>
                    <?php elseif(isset($_GET['pret_existe'])):?>
                        <div class="alert alert-info text-center p-3">
                            <button class="btn close text-danger float-right" onclick='closeAlertOfTransfer()' >&times;</button>
                            <div><?=$_GET['pret_existe']?></div>
                        </div>
                    <?php elseif(isset($_GET['pret_en_cours'])):?>
                        <div class="alert alert-success text-center p-3">
                            <button class="btn close text-danger float-right" onclick='closeAlertOfTransfer()' >&times;</button>
                            <div><?=$_GET['pret_en_cours']?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    </div>
    <script src="../bootstrap/js/bootstrap.js"></script>
    <script>
        function listeOperation() {

            let id=arguments[0]
            let ncompte= arguments[1]

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("responseListOperation").innerHTML=this.response;
                }
            };
            xmlhttp.open("GET", "listeoperations.php?id_client=" + encodeURIComponent(id) + "&numero_compte=" + encodeURIComponent(ncompte), true);
            xmlhttp.send();
        }

        function closeListeOperation() {
            document.getElementById("responseListOperation").innerHTML="";
        }

        function openTransferForm() {
            let ncompte = arguments[0]
            let toperation = arguments[1]

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("responseFormTransfert").innerHTML=this.response;
                }
            };
            xmlhttp.open("GET", "transfert.php?numero_compte=" + encodeURIComponent(ncompte)+ "&type_operation=" + encodeURIComponent(toperation), true);
            xmlhttp.send();
        }

        function closeFormTransfer() {
            document.getElementById('responseFormTransfert').innerHTML='';
        }

        function closeAlertOfTransfer() {
            window.location.search = '';
        }

        // Demande Carnet de cheque -------------------------------------------------
        function demandeCarnetCheque() {            
            let ncompte = arguments[0]
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("responseFormTransfert").innerHTML=this.response;
                }
            };
            xmlhttp.open("GET", "demande_carnetcheque.php?numero_compte=" + encodeURIComponent(ncompte), true);
            xmlhttp.send();
        }

        // Demande de pret ----------------------------------------------
        function openPretForm() {   
            let ncompte = arguments[0]
            let toperation = arguments[1]
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("responseFormTransfert").innerHTML=this.response;
                }
            };
            xmlhttp.open("GET", "demande_pret.php?numero_compte=" + encodeURIComponent(ncompte)+ "&type_operation=" + encodeURIComponent(toperation), true);
            xmlhttp.send();
        }
    </script>
    </body>
    </html>
    <?php
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    } 
    ?>
