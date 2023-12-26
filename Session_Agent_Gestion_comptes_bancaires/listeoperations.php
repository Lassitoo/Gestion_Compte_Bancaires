<?php
session_start();
require 'Agent.php';
try { 
$numero_compte = $_GET['numero_compte'];
$id_client = $_GET['id_client'];
$stmt = $conn->prepare("select * from  operation WHERE numero_compte = :numero_compte");
$stmt->bindParam(':numero_compte', $numero_compte);
$stmt->execute();
$operations= $stmt->fetchAll();
if (count($operations) === 0) {
    echo "Aucune demande en attente";
    } else {
?>
<!DOCTYPE html>
<html>
<head>

    <title>Liste operation <?php echo $numero_compte; ?> </title>
</head>
<body>
    <div class="container border-left border-warning">
        <h4>Liste operation n&deg;<?php echo $numero_compte; ?> : </h4>
        <table class="table table-sm table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Id</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Numero_compte</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo"<tr>";
                foreach($operations as $operation){
                    echo ("<td>".$operation['id_operation']."</td>");
                    echo ("<td>".$operation['type_operation']."</td>");
                    echo ("<td>".$operation['montant']."</td>");
                    echo ("<td>".$operation['date_operation']."</td>");
                    echo ("<td>".$operation['numero_compte']."</td>");
                    echo"</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>


<?php }
} catch(PDOException $e) {
echo "Erreur : " . $e->getMessage();
}

?>