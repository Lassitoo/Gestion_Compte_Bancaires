<?php
session_start();
require 'Client.php';

try {
    $numero_compte = $_GET['numero_compte'];
    $id_client = $_GET['id_client'];

    $stmt = $conn->prepare("SELECT * FROM operation WHERE numero_compte = :numero_compte");
    $stmt->bindParam(':numero_compte', $numero_compte);
    $stmt->execute();
    $operations = $stmt->fetchAll();

    if (count($operations) === 0) {
        $btnCloseResponse = "<div><button class='btn close text-danger float-right' onclick='closeListeOperation();'>&times;</button></div>";
        $response = "<div class='alert alert-info text-center'>$btnCloseResponse Aucune operation pour le compte <span class='badge badge-info'>N&deg;$numero_compte</span></div>";
        echo $response;
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
        </head>
        <body>
            <button class="btn close text-danger float-right" onclick="closeListeOperation();">&times;</button>
            <h4>Liste des opérations <span class='badge badge-info text-italic'>compte N&deg;<?php echo $numero_compte; ?></span></h4>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>id_operation</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>N&deg; compte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($operations as $operation) {
                        echo "<tr>";
                        echo "<td>" . $operation['id_operation'] . "</td>";
                        echo "<td>" . $operation['type_operation'] . "</td>";
                        echo "<td>" . $operation['montant'] . "</td>";
                        echo "<td>" . $operation['date_operation'] . "</td>";
                        echo "<td>" . $operation['numero_compte'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        }
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
