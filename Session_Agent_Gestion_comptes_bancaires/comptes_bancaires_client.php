<?php
session_start();
require 'Agent.php';

try {   
    $id_agent = $_SESSION['id_agent'];

    // Récupération des informations de l'agent connecté
    $stmt = $conn->prepare("SELECT * FROM agents WHERE id_agent = :id_agent");
    $stmt->bindParam(':id_agent', $id_agent);
    $stmt->execute();
    $agent = $stmt->fetch();

    // Récupération de l'ID du client
    $id_client = $_GET['id'];

    // Vérification que le client existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id_client = :id_client");
    $stmt->bindParam(':id_client', $id_client);
    $stmt->execute();
    $client = $stmt->fetch();

    if (!$client) {
        // Afficher un message d'erreur si le client n'existe pas
        echo "Ce client n'existe pas.";
    } else {
        // Récupération des comptes bancaires du client
        $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE id_client = :id_client");
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();
        $compte_bancaire = $stmt->fetchAll();

        // Affichage de la liste des comptes bancaires du client
?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Liste des comptes bancaires de <?= $client['prenom_client']." ".$client['nom_client']; ?></title>
        </head>
        <body>
            <h4>Comptes bancaires de <i><?= $client['prenom_client']." ".$client['nom_client']; ?><i></h4>
            <?php if (count($compte_bancaire) === 0) { ?>
                <p>Aucun compte bancaire enregistré pour ce client.</p>
            <?php } else { ?>
                <table class="table table-sm mt-3">
                    <thead class='thead-light'>
                        <tr>
                            <th scope="col">N&deg; Compte</th>
                            <th scope="col" class='bg-secondary text-white'>Solde</th>
                            <th scope="col" colspan=2>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($compte_bancaire as $compte) : ?>
                            <tr>
                                <td><?php echo $compte['numero_compte']; ?></td>
                                <td class='bg-secondary text-white'><?php echo $compte['solde']; ?> €</td>
                                <td>
                                    <button class='btn btn-outline text-primary' onclick="bankOperations(<?= $id_client ?>, <?= $compte['numero_compte'] ?>, 'Depot')" >
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </button>
                                    <button class='btn btn-outline text-success'  onclick="bankOperations(<?= $id_client ?>, <?= $compte['numero_compte'] ?>, 'Retrait')" >
                                        <i class="fa fa-minus-square" aria-hidden="true"></i>
                                    </button>
                                    <button class='btn btn-outline text-warning'  onclick="bankOperations(<?= $id_client ?>, <?= $compte['numero_compte'] ?>, 'Transfert')" >
                                        <i class="fa fa-exchange" aria-hidden="true"></i>
                                    </button>
                                    <button class='btn btn-outline text-dark' onclick="bankOperations(<?= $id_client ?>, <?= $compte['numero_compte'] ?>, 'Open')" >
                                        <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td class='text-center'> <!-- Supprimer le compte client-->
                                    <button class="btn btn-outline text-danger" onclick="bankOperations(<?= $id_client ?>, <?= $compte['numero_compte'] ?>, 'Delete')">
                                        <i class="fa fa-ban" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
<?php } ?>
        </body>
        </html>
<?php
}
} catch(PDOException $e) {
echo "Erreur : " . $e->getMessage();
}





