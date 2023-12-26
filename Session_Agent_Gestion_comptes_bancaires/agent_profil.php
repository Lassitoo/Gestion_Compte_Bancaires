<?php
session_start();
if (!isset($_SESSION['id_agent'])) {
    header('Location: connexion_agent.php');
}

require 'Agent.php';

try {
    $id_agent = $_SESSION['id_agent'];

    // Récupération des informations de l'agent connecté
    $stmt = $conn->prepare("SELECT * FROM agents WHERE id_agent = :id_agent");
    $stmt->bindParam(':id_agent', $id_agent);
    $stmt->execute();
    $agent = $stmt->fetch();


    ///////////////////////////////////////////////////////////////////////////////////////////


    // Vérification des demandes de création de compte client en attente
    if (isset($_POST['approuver_demande'])) {
        $id_demande = $_POST['id_demande'];

        // Vérifier si l'agent est autorisé à approuver la demande
        $stmt = $conn->prepare("SELECT * FROM demandes_creation_comptes_clients WHERE id_demande = :id_demande  AND etat = 'en_attente'");
        $stmt->bindParam(':id_demande', $id_demande);
        $stmt->execute();
        $demande = $stmt->fetch();

        if ($demande) {
            // Approuver la demande en mettant à jour l'état dans la base de données
            $stmt = $conn->prepare("UPDATE demandes_creation_comptes_clients SET etat = 'approuvee' WHERE id_demande = :id_demande");
            $stmt->bindParam(':id_demande', $id_demande);
            $stmt->execute();

            // Créer le compte client dans la table clients
            $stmt = $conn->prepare("INSERT INTO clients (nom_client, prenom_client, email_client, telephone_client) VALUES (:nom, :prenom, :email, :telephone)");
            $stmt->bindParam(':nom', $demande['nom']);
            $stmt->bindParam(':prenom', $demande['prenom']);
            $stmt->bindParam(':email', $demande['email']);
            $stmt->bindParam(':telephone', $demande['telephone']);
            $stmt->execute();
        }
    }

    // Récupération de la liste des clients
    $stmt = $conn->prepare("SELECT * FROM clients");
    $stmt->execute();
    $clients = $stmt->fetchAll();

    // Affichage du profil de l'agent et de la liste des clients
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Profil de l'agent</title>
        <link rel="stylesheet" href="fontAwesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    </head>
    <body>
        <div class="container">

        <h1>Profil de l'agent</h1>
        <!-- Deconnexion -->
        <a href="deconnexion.php" style="float:right;" class="btn btn-primary">Deconnexion</a> 

        <p>Bienvenue, <?php echo $agent['nom_agent']; ?></p>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Clients</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false">Création de compte en attente</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Clients ayant Carnet cheque</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Carnet cheque</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab4-tab" data-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false">Liste des Prêts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab5-tab" data-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false">Demandes Prêt</a>
            </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <!-- Creation compte -->
                    <div>
                        <?php if (count($clients) === 0) { ?>
                        <p>Aucun client enregistré.</p>
                        <?php } else { ?>
                            <!-- Recherche -->
                            <div class="row justify-content-center mt-2 p-2">
                                <div class="col col-md-6">
                                    <input class="form-control text-center" onkeyup="filterTable()" type="search" placeholder="Recherche" name="search" id="search">
                                </div>
                            </div>

                            <table class="table table-sm" id="client-table">
                                <thead>
                                    <tr>
                                        <th scope="col" >Nom</th>
                                        <th scope="col" >Prénom</th>
                                        <th scope="col" >Email</th>
                                        <th scope="col" >Téléphone</th>
                                        <th scope="col" >Comptes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($clients as $client) : ?>
                                    <?php if (empty($client['status_client']) ):?>
                                    <tr id="<?= $client['id_client']?>">
                                        <td scope="row"><?= $client['nom_client']; ?></td>
                                        <td><?= $client['prenom_client']; ?></td>
                                        <td><?= $client['email_client']; ?></td>
                                        <td><?= $client['telephone_client']; ?></td>
                                        <td>
                                            <div id="compte-tools">
                                                <button class="btn btn-outline text-primary" type="button" onclick="creerComptes('<?= $client['id_client']?>')">
                                                    <i class="fa fa-plus-square" aria-hidden="true"></i>
                                                </button>
                                                                                      
                                                <button class="btn btn-outline" type="button" onclick="showComptes(<?=$client['id_client']?>)">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>
                                                
                                                <button class="btn btn-outline text-danger" type="button" onclick="deleteCompte(<?=$client['id_client']?>)">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                                <!-- modificaiton -->
                                                <button class="btn btn-outline text-secondary" type="button" onclick="editCompte(<?=$client['id_client']?>)">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div id="edit-tools">
                                                <button class="btn btn-outline text-danger" type="button" onclick="notConfirmEdit(<?=$client['id_client']?>)">
                                                    <i class="fa fa-remove" aria-hidden="true"></i>
                                                </button>
                                                
                                                <button class="btn btn-outline text-primary" type="button" onclick="confirmEdit(<?=$client['id_client']?>)">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif;?>
                                <?php endforeach; ?>
                        </tbody>
                        </table>
                        <?php } ?>
                    </div>
                    <div>
                        <div class="text-center">
                            <?php if(isset($_GET['error_in_creation'])): ?>
                                <div class='alert alert-danger'> Vous avez saisie un numero de compte existant !</div>
                            <?php endif ?>

                            <?php if(isset($_GET['creation_succes'])): ?>
                                <div class='alert alert-success'> Creation de compte avec success</div>
                            <?php endif ?>
                                    
                            <div id="alert-delete-box" class=''><!-- lieu de notification de supression de client --></div>

                            <div id="alert-update-box" class=''><!-- lieu de notification de modification de client --></div>

                            <?php if(isset($_GET['Ops']) && isset($_GET['id'])): ?>
                                <!-- Recherche le Nom et prenom de client by id -->
                                <?php
                                    foreach($clients as $client){
                                        if ($client['id_client'] == $_GET['id']) {
                                            $_clientName = $client['prenom_client'] .' '. $client['nom_client'];
                                        }
                                    }
                                ?>
                                <div id ='AlertBox'>
                                    <?php if($_GET['Ops']=='Depot'): ?> <!-- Afficher la success de l'operation Depot -->
                                        <div class='alert alert-success'>
                                            Operation <i>Depot</i> fait avec Success pour Client :<strong> <?= $_clientName ?> </strong> a <?= $_GET['date']?> [<i> Sera fermer apres : <strong id="timer" data-timer=5></strong></i> ]
                                        </div>
                                    <?php endif?>

                                    <?php if($_GET['Ops']=='Retrait' && !isset($_GET['errorSolde'])): ?> <!-- Afficher la success de l'operation Retrait -->
                                        <div class='alert alert-success'> Operation <i>Retrait</i> fait avec Success pour Client :<strong> <?= $_clientName ?> </strong> a <?= $_GET['date']?> [<i> Sera fermer apres : <strong id="timer" data-timer=5></strong></i> ]</div>
                                    <?php elseif($_GET['Ops']=='Retrait' && isset($_GET['errorSolde'])): ?> <!-- Erreur, si le solde inferieur au some sera recuperer -->
                                        <div class='alert alert-danger'> Operation <i>Retrait</i>, il s'agit d'une erreur : <strong> <?= $_GET['errorSolde'] ?> </strong>  [<i> Sera fermer apres : <strong id="timer" data-timer=15></strong></i> ]</div>
                                    <?php endif?>

                                    <?php if($_GET['Ops']=='Transfert'): ?> <!-- Afficher la success de l'operation Transfert -->
                                        <div class='alert alert-success'> Operation <i>Transfert</i> fait avec Success pour Client :<strong> <?= $_clientName ?> </strong> a <?= $_GET['date']?> [<i> Sera fermer apres : <strong id="timer" data-timer=5></strong></i> ]</div>
                                        <div class="alert alert-secondary"><?= $_GET['msg']?></div>
                                    <?php endif?>
                                </div>
                            <?php endif ?>
                            <?php if(isset($_GET['errorOps']) && isset($_GET['Ops'])): ?>
                                <div class="alert alert-warning"><?=$_GET['errorOps']?> <?=$_GET['Ops']?></div>
                            <?php endif ?>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button class="close btn btn-outline-danger" id="cacheBtn" type="button" onclick="caherBlock()" aria-label="Close" style="float:right;">
                                    <span aria-hidden="true" >&times;</span>
                                </button>
                                <div class="row" id="compteBancaireClient" >
                                    <!-- Content -->
                                </div>

                            </div>
                            <div class="col">
                                <button class="close btn btn-outline-danger" id="cacheBtn2nd" type="button" onclick="caher2ndBlock()" aria-label="Close" style="float:right;">
                                    <span aria-hidden="true" >&times;</span>
                                </button>
                                <div class="row" id="operationsRequests">
                                    <!-- Some Content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                <?php
                    // Récupération des demandes de création de compte en attente
                    $stmt = $conn->prepare("SELECT * FROM demandes_creation_comptes_clients WHERE etat = 'en_attente'");
                    $stmt->execute();
                    $demandes = $stmt->fetchAll();
                    if (count($demandes) === 0) {
                    echo "<p>Aucune demande en attente.</p>";
                    } else {
                    // Affichage des demandes en attente sous forme de tableau
                    ?>
                    <table class="table table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $demande) { ?>
                                <tr>
                                    <td><?php echo $demande['nom']; ?></td>
                                    <td><?php echo $demande['prenom']; ?></td>
                                    <td><?php echo $demande['email']; ?></td>
                                    <td><?php echo $demande['telephone']; ?></td>
                                    <td>
                                        <form method="POST">
                                        <input type="hidden" name="id_demande" value="<?php echo $demande['id_demande']; ?>">
                                        <button class="btn btn-primary" type="submit" name="approuver_demande">Approuver</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php }
                ?>
                </div>
                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                <?php
                    // Récupération des Demandes Carnet cheque en attente
                    $stmt = $conn->prepare("SELECT * FROM demande_carnetcheque WHERE etat_demande != 'en_attente'");
                    $stmt->execute();
                    $carnets = $stmt->fetchAll();
                    if (count($carnets) === 0) {
                    echo "<p>Aucune demande en attente.</p>";
                    } else {
                    // Affichage des demandes en attente sous forme de tableau
                    ?>
                    <table class="table table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>numero_demande</th>
                                <th>etat_demande</th>
                                <th>numero_compte</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($carnets as $carnet) { ?>
                            <tr>
                                <td><?php echo $carnet['numero_demande']; ?></td>
                                <td><?php echo $carnet['etat_demande']; ?></td>
                                <td><?php echo $carnet['numero_compte']; ?></td>
                                <td>
                                    <form method="POST" action="agent_profil.php">
                                        <input type="hidden" name="numero_car" value="<?php echo $carnet['numero_demande']; ?>">
                                        <button class="btn btn-primary" type="submit" name="expiration_carnet">expirer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table> 
                    <?php
                        if (isset($_POST['expiration_carnet'])) {
                            $numero_car = $_POST['numero_car'];
                            $en_attente = 'en_attente';
                            // Vérifier si l'agent est autorisé à approuver la demande
                            $stmt = $conn->prepare("DELETE FROM demande_carnetcheque WHERE numero_demande = :numero_car AND etat_demande != :en_attente");
                            $stmt->bindParam(':numero_car', $numero_car);
                            $stmt->bindParam(':en_attente', $en_attente);
                            $stmt->execute();
                            $demande = $stmt->fetch();
                            
                        }
                    ?>
                    <?php } 
                    ?>

                </div>
                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                <?php
                    // Récupération des Demandes Carnet cheque en attente
                    $stmt = $conn->prepare("SELECT * FROM demande_carnetcheque WHERE etat_demande = 'en_attente'");
                    $stmt->execute();
                    $demandescarnet = $stmt->fetchAll();
                    if (count($demandescarnet) === 0) {
                    echo "<p>Aucune demande en attente.</p>";
                    } else {
                    // Affichage des demandes en attente sous forme de tableau
                    ?>
                    <table class="table table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>numero_demande</th>
                                <th>etat_demande</th>
                                <th>numero_compte</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandescarnet as $demandecar) { ?>
                            <tr>
                                <td><?php echo $demandecar['numero_demande']; ?></td>
                                <td><?php echo $demandecar['etat_demande']; ?></td>
                                <td><?php echo $demandecar['numero_compte']; ?></td>
                                <td>
                                    <form method="POST" action="agent_profil.php">
                                    <input type="hidden" name="numero_demande" value="<?php echo $demandecar['numero_demande']; ?>">
                                    <button class="btn btn-primary" type="submit" name="approuver_demande_carnet">Approuver</button>
                                    </form>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <?php

                                            if (isset($_POST['approuver_demande_carnet'])) {
                                                $numero_demande = $_POST['numero_demande'];
                                                $en_attente = 'en_attente';
                                                // Vérifier si l'agent est autorisé à approuver la demande
                                                $stmt = $conn->prepare("SELECT * FROM demande_carnetcheque WHERE numero_demande = :numero_demande AND etat_demande = :en_attente");
                                                $stmt->bindParam(':numero_demande', $numero_demande);
                                                $stmt->bindParam(':en_attente', $en_attente);
                                                $stmt->execute();
                                                $demande = $stmt->fetch();

                                                if ($demande) {
                                                    $numero_demande = $demande['numero_demande'];
                                                    // Approuver la demande en mettant à jour l'état dans la base de données
                                                    $stmt = $conn->prepare("UPDATE demande_carnetcheque SET etat_demande = 'approuvee' WHERE numero_demande = :numero_demande");
                                                    $stmt->bindParam(':numero_demande', $numero_demande);
                                                    $stmt->execute();

                                                    
                                                }
                                            }
                                        ?>
                                </td>
                            </tr>
                    <?php } 
                ?>

                </div>
                <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                <?php
                    // Récupération des demandes de prêts en attente
                    $stmt = $conn->prepare("SELECT * FROM demande_pret WHERE etat_demande = 'approuvee'");
                    $stmt->execute();
                    $prets = $stmt->fetchAll();
                    if (count($prets) === 0) {
                    echo "<p>Aucun prêt en attente.</p>";
                    } else {
                    // Affichage des prêts sous forme de tableau
                ?>
                    <table class="table table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Nom du client</th>
                                <th>État de la demande</th>
                                <th>Montant</th>
                                <th>Numéro de compte</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prets as $pret) {
                                $numero_compte_pret = $pret['numero_compte'];
                                $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte");
                                $stmt->bindParam(':numero_compte', $numero_compte_pret);
                                $stmt->execute();
                                $comptes_prets = $stmt->fetch();

                                $id_client_pret = $comptes_prets['id_client'];
                                $stmt = $conn->prepare("SELECT * FROM clients WHERE id_client = :id_client");
                                $stmt->bindParam(':id_client', $id_client_pret);
                                $stmt->execute();
                                $clients = $stmt->fetch();

                                $nom_client_pret = $clients['nom_client'];
                            ?>

                            <tr>
                                <td><?php echo $nom_client_pret; ?></td>    
                                <td><?php echo $pret['etat_demande']; ?></td>
                                <td><?php echo $pret['montant']; ?></td>
                                <td><?php echo $pret['numero_compte']; ?></td>
                                <td>
                                    <form method="POST" action="agent_profil.php">
                                        <input type="hidden" name="numero_de_pret" value="<?php echo $pret['numero_demande']; ?>">
                                        <button class="btn btn-primary" type="submit" name="payer_pret">Payer le prêt</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php
                        if (isset($_POST['payer_pret'])) {
                        $numero_pret = $_POST['numero_de_pret'];
                        $etat = 'Payé';

                        // Vérifier si l'agent est autorisé à approuver la demande
                        $stmt = $conn->prepare("SELECT * FROM demande_pret WHERE numero_demande = :numero_demande");
                        $stmt->bindParam(':numero_demande', $numero_pret);
                        $stmt->execute();
                        $pret_approuvee = $stmt->fetch();

                        $numero_pret = $pret_approuvee['numero_demande'];

                        // Approuver la demande en mettant à jour l'état dans la base de données
                        $stmt = $conn->prepare("UPDATE demande_pret SET etat_demande = 'Payé' WHERE numero_demande = :numero_demande");
                        $stmt->bindParam(':numero_demande', $numero_pret);
                        $stmt->execute();

                        // Mettre à jour le solde du compte bancaire
                        $montant_pret = $pret_approuvee['montant'];
                        $solde_apres_payement = $comptes_prets['solde'] - $montant_pret;
                        $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde_apres_payement WHERE numero_compte = :numero_compte");
                        $stmt->bindParam(':solde_apres_payement', $solde_apres_payement);
                        $stmt->bindParam(':numero_compte', $pret_approuvee['numero_compte']);
                        $stmt->execute();

                        // Enregistrer l'opération dans la table des opérations
                        $type_operation = "Paiement de prêt";
                        $date_operation = date('Y-m-d H:i:s');
                        $stmt = $conn->prepare("INSERT INTO operation (type_operation, montant, date_operation, numero_compte) VALUES (:type_operation, :montant, :date_operation, :numero_compte)");
                        $stmt->bindParam(':type_operation', $type_operation);
                        $stmt->bindParam(':montant', $montant_pret);
                        $stmt->bindParam(':date_operation', $date_operation);
                        $stmt->bindParam(':numero_compte', $pret_approuvee['numero_compte']);
                        $stmt->execute();
                        }   
                    ?>
                    <?php 
                        } 
                    ?>


                </div>
                <div class="tab-pane fade" id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
                <?php
                    // Récupération des Demandes Carnet cheque en attente
                    $stmt = $conn->prepare("SELECT * FROM demande_pret WHERE etat_demande = 'en_attente'");
                    $stmt->execute();
                    $demandesprets = $stmt->fetchAll();
                    if (count($demandesprets) === 0) {
                    echo "<p>Aucune demande de pret  en attente.</p>";
                    } else {
                    // Affichage des demandes de pret en attente sous forme de tableau
                ?>
                <table class="table table-sm table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>numero_demande</th>
                            <th>etat_demande</th>
                            <th>montant</th>
                            <th>numero_compte</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandesprets as $demandespret) { ?>
                        <tr>
                            <td><?php echo $demandespret['numero_demande']; ?></td>
                            <td><?php echo $demandespret['etat_demande']; ?></td>
                            <td><?php echo $demandespret['montant']; ?></td>
                            <td><?php echo $demandespret['numero_compte']; ?></td>
                            <td>
                                <form method="POST" action="agent_profil.php">
                                    <input type="hidden" name="numero_demande_pret" value="<?php echo $demandespret['numero_demande']; ?>">
                                    <button class="btn btn-primary" type="submit" name="approuver_demande_pret">Approuver Pret</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php

                    if (isset($_POST['approuver_demande_pret'])) {
                        $numero_demande_pret = $_POST['numero_demande_pret'];
                        $en_attente = 'en_attente';
                        // Vérifier si l'agent est autorisé à approuver la demande
                        $stmt = $conn->prepare("SELECT * FROM demande_pret WHERE numero_demande = :numero_demande AND etat_demande = :en_attente");
                        $stmt->bindParam(':numero_demande', $numero_demande_pret);
                        $stmt->bindParam(':en_attente', $en_attente);
                        $stmt->execute();
                        $demande = $stmt->fetch();

                        if ($demande) {
                            $numero_demande_pret = $demande['numero_demande'];
                            // Approuver la demande en mettant à jour l'état dans la base de données
                            $stmt = $conn->prepare("UPDATE demande_pret SET etat_demande = 'approuvee' WHERE numero_demande = :numero_demande");
                            $stmt->bindParam(':numero_demande', $numero_demande_pret);
                            $stmt->execute();

                            $montant_pret=$demande['montant'];
                            $numero_compte=$demande['numero_compte'];
                            $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte");
                            $stmt->bindParam(':numero_compte', $numero_compte);
                            $stmt->execute();
                            $compte = $stmt->fetch();
                            $pret="pret";
                            if($compte){
                                $solde=$compte['solde']+$montant_pret;
                                $stmt = $conn->prepare("UPDATE compte_bancaire SET solde = :solde WHERE numero_compte = :numero_compte");
                                $stmt->bindParam(':numero_compte', $numero_compte);
                                $stmt->bindParam(':solde', $solde);
                                $stmt->execute();
                                
                            }
                            $date_operation = date('Y-m-d H:i:s');
                                
                            $stmt = $conn->prepare("INSERT into OPERATION (id_operation,type_operation,montant,date_operation,numero_compte) values(default,:type_operation,:montant,:dateop,:numero_compte)");
                            $stmt->bindParam(':type_operation',$pret );
                            $stmt->bindParam(':montant', $montant_pret);
                            $stmt->bindParam(':dateop', $date_operation);
                            $stmt->bindParam(':numero_compte', $numero_compte);
                            $stmt->execute();
                        }
                    }
                }

                ?>
                </div>
            </div>


    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script language="javascript" src="bootstrap/js/bootstrap.js"></script>

    <script>


                            //                 L'objet XMLHttpRequest
                            // L'objet XMLHttpRequest peut être utilisé pour demander des données à un serveur Web.

                            // L'objet XMLHttpRequest est un rêve pour les développeurs , car vous pouvez :

                            // Mettre à jour une page Web sans recharger la page
                            // Demander des données à un serveur - après le chargement de la page
                            // Recevoir des données d'un serveur - après le chargement de la page
                            // Envoyer des données à un serveur - en arrière-plan

        document.getElementById("cacheBtn").style.display = "none"; // cacher le bouton qui ferme le container 1
        document.getElementById("cacheBtn2nd").style.display = "none"; // cacher le bouton qui ferme le container 2
        document.getElementById("alert-delete-box").style.display = "none"; // cache le block de l'alert de suppression
        document.getElementById("alert-update-box").style.display = "none"; // cache le block de l'alert de modification

        function editToolsToggle(stat){  // Cacher ou Afficher (1) les outils de modification
            var tr = document.getElementById("client-table").getElementsByTagName('tr');
            for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[4];
                    if(td){
                        div = td.getElementsByTagName('div')[1];
                        if (stat === 1) // cacher
                            div.style.display = "none";
                        else // afficher
                            div.style.display = "";
                    }
            }
        }
        function compteToolsToggle(stat){  // Cacher ou Afficher (1) les outils de gestion de compte
            var tr = document.getElementById("client-table").getElementsByTagName('tr');
            for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[4];
                    if(td){
                        div = td.getElementsByTagName('div')[0];
                        if (stat === 1) // cacher
                            div.style.display = "none";
                        else // afficher
                            div.style.display = "";
                    }
            }
        }
        editToolsToggle(1); // cacher
        // compteToolsToggle();

        function countdown() {
            let timeleft = document.getElementById('timer').getAttribute('data-timer');
            document.getElementById('timer').innerHTML=timeleft;
            var downloadTimer = setInterval( function(){
                if(timeleft <= 0){
                    clearInterval(downloadTimer);
                    document.getElementById("AlertBox").innerHTML=''; // supprimer l'alert apres quelque second
                    window.location.search = ''; // supprimer la partie d'URL contient les params
                } else {
                    document.getElementById("timer").innerHTML = timeleft;
                }
                timeleft -= 1;
            }, 1000);
        }
        countdown();

        function countdownParams(block, timeleft, reload) { // fonction pour fermer block selon une temps donnee
            var downloadTimer = setInterval( function(){
                if(timeleft <= 0){
                    clearInterval(downloadTimer);
                    document.getElementById(block).innerHTML=''; // supprimer l'alert apres quelque second
                    document.getElementById(block).style.display="none";
                    if (reload === true)
                        location.reload();
                }
                timeleft -= 1;
            }, 1000);
        }
        function deleteRowOfTableById(id, tableId) {
            var tr = document.getElementById(tableId).getElementsByTagName('tr');
            var tableSelected=document.getElementById(tableId);
            for (i = 1; i < tr.length; i++) {
                if (tr[i].id == id){
                    tableSelected.deleteRow(i);
                    continue
                }
            }
        }

        

        function showComptes(id) {
            caher2ndBlock(); // vider le block des operations
            var content = document.getElementById("compteBancaireClient");
            var cacheBtn = document.getElementById("cacheBtn"); // variable reference au bouton Fermer X
            if (content.style.display === "none") {
                content.style.display = "block";
            }
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                // 
                if (this.readyState == 4 && this.status == 200) {
                    cacheBtn.style.display = "block";
                    document.getElementById("compteBancaireClient").innerHTML = this.responseText; // mettre a jour le block et implementer les resultats sur le block
                }
            };
            xmlhttp.open("GET", "comptes_bancaires_client.php?id=" + encodeURIComponent(id), true);
            xmlhttp.send();
        }

        function creerComptes(id) {
            caher2ndBlock(); // vider le block des operations
            var content = document.getElementById("compteBancaireClient");
            var cacheBtn = document.getElementById("cacheBtn"); // variable reference au bouton Fermer X
            if (content.style.display === "none") {
                content.style.display = "block"; // Afficher le block de donnees
            }
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    cacheBtn.style.display = "block";
                    document.getElementById("compteBancaireClient").innerHTML = this.responseText; // mettre a jour le block et implementer les resultats sur le block
                }
            };
            xmlhttp.open("GET", "creation_compte_bancaire.php?id=" + encodeURIComponent(id), true);
            xmlhttp.send();
        }

        function deleteCompte(id) { // suppression de compte -------------------------------
            let text = "Vous etes sure de vouloir la suppression ?";
            if (confirm(text) == true) {
                var xmlhttp = new XMLHttpRequest();
                let blockname = "alert-delete-box";
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        deleteRowOfTableById(id, "client-table"); // supprimer ligne de table
                        document.getElementById(blockname).style.display="block";
                        countdownParams(blockname, 5, false);
                        document.getElementById(blockname).innerHTML = this.responseText; // mettre a jour le block et implementer les resultats sur le block
                    }
                };
                xmlhttp.open("GET", "suprimer_compte.php?id_client=" + encodeURIComponent(id), true);
                xmlhttp.send();
            }
        }

        function filterTable() { // Filtrage de la table de client --------------------------
            let table = document.getElementById("client-table");
            let input = document.getElementById("search");
            let filter = input.value.toUpperCase();
            let tr = table.getElementsByTagName('tr');

            // filtrer le tableau par le nom
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
            
        } // -----------------------------------------------------------------------------
        tableDataRowSelected = new Array();

        function editCompte(id) { // modifier le compte ---------------------------------
            let text = "Vous etes sure pour passer vers Modification ?";
            if (confirm(text) == true) {
                caherBlock(); // Fermer tous les block secondaires
                var tr = document.getElementById("client-table").getElementsByTagName('tr');
                for (i = 1; i < tr.length; i++) {
                    if (tr[i].id == id){
                        liste = Array.from(tr[i].getElementsByTagName('td'));
                        continue

                    }
                    else
                        tr[i].style.display = "none";
                }
                editToolsToggle(); // afficher
                compteToolsToggle(1); // cacher

                data = extractElements(liste);
                tableDataRowSelected = data;
                includedForm(id, data);
            }
        } 

        function includedForm(id, data) {
            var tr = document.getElementById("client-table").getElementsByTagName('tr');
            for (i = 1; i < tr.length; i++) {
                if (tr[i].id == id){
                    td = tr[i].getElementsByTagName('td');
                    for (j=0; j<data.length; j++){
                        td[j].innerHTML = "<input class='form-control' onkeyup='getDataInput("+id+")' type='text' value="+data[j]+">";
                    }

                }
            }
        }

        function extractElements(liste) {
            newliste = new Array();
            for (i=0; i<liste.length-1; i++){
                newliste[i] = liste[i].innerHTML;
            }
            return newliste;
        } // -----------------------------------------------------------------------------

        function notConfirmEdit(id) { // quitter la modification -------------------------
            
            let text = "Vous etes sure pour quitter la modification ?";
            if (confirm(text) == true) {
                var tr = document.getElementById("client-table").getElementsByTagName('tr');
                for (i = 1; i < tr.length; i++) {
                    if (tr[i].id == id){
                        td = tr[i].getElementsByTagName('td');
                        for (j=0; j<tableDataRowSelected.length; j++){
                            td[j].innerHTML = tableDataRowSelected[j];
                        }
                        continue
                    } else
                        tr[i].style.display = "";
                }
                editToolsToggle(1); // cacher
                compteToolsToggle(0); // afficher
            }
        } // -----------------------------------------------------------------------------

        function getDataInput(id) {
            var tr = document.getElementById("client-table").getElementsByTagName('tr');
            var data = new Array();
            for (i = 1; i < tr.length; i++) {
                if (tr[i].id == id){
                    td = tr[i].getElementsByTagName('td');
                    for (j=0; j<td.length-1; j++){
                        data[j] = td[j].getElementsByTagName("input")[0].value;
                    }
                    continue
                }
            }
            return data;
        }
        function exchangeData(id, data){ // function change last data by new one
            var tr = document.getElementById("client-table").getElementsByTagName('tr');
            for (i = 1; i < tr.length; i++) {
                if (tr[i].id == id){
                    td = tr[i].getElementsByTagName('td');
                    for (j=0; j<tableDataRowSelected.length; j++){
                        td[j].innerHTML = data[j];
                    }
                    continue
                } else
                    tr[i].style.display = "";
            }
            editToolsToggle(1); // cacher
            compteToolsToggle(0); // afficher
        }

        function confirmEdit(id) {
            
            let text = "Vous etes sure pour continuer la modification ?";
            if (confirm(text) == true) {
                data = getDataInput(id);
                var blockname = "alert-update-box";
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        exchangeData(id, data); // Changement les anciens data par les nouvelles data
                        document.getElementById(blockname).style.display="block";
                        countdownParams(blockname, 5);
                        document.getElementById(blockname).innerHTML = this.responseText; // mettre a jour le block et implementer les resultats sur le block
                    }
                };

                // Ouvrir une connexion avec le serveur
                xmlhttp.open("POST", "update_client.php", true);

                // Définir l'en-tête de la requête
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                // Envoyer la requête avec les données
                query = "id="+id+"&nom="+data[0]+" &prenom="+data[1]+"&email="+data[2]+"&tel="+data[3];
                xmlhttp.send(query);
            }
        } // -----------------------------------------------------------------------------

        function caherBlock() {
            var content = document.getElementById("compteBancaireClient");
            var cacheBtn = document.getElementById("cacheBtn"); // variable reference au bouton Fermer X
            if (content.style.display === "none") {
                content.style.display = "block";
            } else {
                cacheBtn.style.display = "none";
                content.innerHTML = '';
                document.getElementById("operationsRequests").innerHTML='';
                document.getElementById("cacheBtn2nd").style.display = "none";
                content.style.display = "none";
            }
        }


        function caher2ndBlock() {
            document.getElementById("operationsRequests").innerHTML = ''; // Vider le container
            document.getElementById("cacheBtn2nd").style.display = "none"; // Fermer de Bouton de 'X'
        }


        function bankOperations() {
            var content = document.getElementById("operationsRequests");
            content.innerHTML = ''; // vide le container
            document.getElementById("cacheBtn2nd").style.display="block"; // Affichage de Bouton de 'X'
            
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("operationsRequests").innerHTML = this.responseText; // mettre a jour le block et implementer les resultats sur le block
                    
                }
            };

            if(arguments[2]=="Depot") {
                xmlhttp.open("GET", "depot.php?id_client=" + encodeURIComponent(arguments[0]) + "&numero_compte="+arguments[1]+"&type_operation="+arguments[2], true);
                
                xmlhttp.send();
            }
            if(arguments[2]=="Retrait") {
                xmlhttp.open("GET", "retrait.php?id_client=" + encodeURIComponent(arguments[0]) + "&numero_compte="+arguments[1]+"&type_operation="+arguments[2], true);
                
                xmlhttp.send();
            }
            if(arguments[2]=="Transfert") {
                xmlhttp.open("GET", "transfert.php?id_client=" + encodeURIComponent(arguments[0]) + "&numero_compte="+arguments[1]+"&type_operation="+arguments[2], true);
                
                xmlhttp.send();
            }
            if(arguments[2]=="Open") {
                xmlhttp.open("GET", "listeoperations.php?id_client=" + encodeURIComponent(arguments[0]) + "&numero_compte="+arguments[1], true);
                
                xmlhttp.send();
            }
        }


    </script>
    </div>
    </body>
</html>
<?php
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
} 
?>