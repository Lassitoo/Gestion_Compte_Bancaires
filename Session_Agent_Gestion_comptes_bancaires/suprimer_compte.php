<?php
session_start();
if (!isset($_SESSION['id_agent'])) {
    header('Location: connexion_agent.php');
}
require 'Agent.php';


$type_operation="Del";
try {
    if(isset($_GET['id_client'])){
        $id_client = $_GET['id_client'];

        // Supprimer le compte client
        $stmt = $conn->prepare("UPDATE clients SET status_client='DEL' WHERE id_client=:id_client");
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();

        // $_POST['id_client_deleted']=$id_client;
        echo "<div class='alert alert-warning'>Suppression de client n&deg;$id_client fait avec success !</div>";
    }
    
    
} catch (PDOException $e) {
    // En Cas ou il s'agit une erreur a l'operation de suppression
    $errorDelete =  "Erreur : " . $e->getMessage();
    
    header("Location: agent_profil.php?errorOps=$error&Ops=$type_operation");
} 


?>
