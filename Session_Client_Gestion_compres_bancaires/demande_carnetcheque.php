<?php
session_start();
require 'Client.php';

try {
    $id_client = $_SESSION['id_client'];
    $etat_demande = "en_attente";
    $numero_compte = $_GET['numero_compte'];
    // Récupération des informations du client connecté
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id_client = :id_client");
    $stmt->bindParam(':id_client', $id_client);
    $stmt->execute();
    $client = $stmt->fetch();

    
    $stmt = $conn->prepare("SELECT * FROM demande_carnetcheque WHERE numero_compte = :numero_compte");
    $stmt->bindParam(':numero_compte', $numero_compte);
    $stmt->execute();
    $demande_carnetcheque = $stmt->fetch();

    
    if ($demande_carnetcheque)

    {
        // deja demandee demande_carnetcheque
        $btnCloseResponse = "<div><button class='btn close text-danger float-right' onclick='closeFormTransfer();'>&times;</button></div>";
        $response = "<div class='alert alert-info text-center'>$btnCloseResponse Deja demandee demande_carnetcheque pour le compte <span class='badge badge-info'>N&deg;$numero_compte</span></div>";
        echo $response;
    }
    else 
    {
        $stmt = $conn->prepare("INSERT INTO demande_carnetcheque (etat_demande,numero_compte) VALUES ( :etat_demande, :numero_compte)");
        $stmt->bindParam(':etat_demande', $etat_demande);
        $stmt->bindParam(':numero_compte', $numero_compte);
        $stmt->execute();
        
        $btnCloseResponse = "<div><button class='btn close text-danger float-right' onclick='closeFormTransfer();'>&times;</button></div>";
        $response = "<div class='alert alert-info text-center'>$btnCloseResponse Demande en cours, pour le compte <span class='badge badge-info'>N&deg;$numero_compte</span></div>";
        echo $response;

    }

           
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
