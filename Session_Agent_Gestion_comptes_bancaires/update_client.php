<?php

session_start();
if (!isset($_SESSION['id_agent'])) {
    header('Location: connexion_agent.php');
}
require 'Agent.php';


try { 
    if(isset($_POST['id'], $_POST['nom'], $_POST['prenom'], $_POST['tel'], $_POST['email'])){
        // echo $_POST['id'], $_POST['nom'], $_POST['prenom'], $_POST['tel'], $_POST['email'];
        // die;
        // Supprimer le compte client
        $stmt = $conn->prepare("UPDATE clients SET nom_client=:nom ,prenom_client=:prenom ,email_client=:email, telephone_client=:tel WHERE id_client=:id_client");
        $stmt->bindParam(':nom', $_POST['nom']);
        $stmt->bindParam(':prenom', $_POST['prenom']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':tel', $_POST['tel']);
        $stmt->bindParam(':id_client', $_POST['id']);
        $stmt->execute();
    }

    echo "<div class='alert alert-success'>Modification fait avec Success !</div>";
    
    
} catch (PDOException $e) {
    // En Cas ou il s'agit une erreur a l'operation de suppression
    $errorUpdate =  "Erreur : " . $e->getMessage();
    
    echo "<div class='alert alert-danger'>Erreur de  modification : $errorUpdate  !</div>";
} 
?>