<?php
// informations de connexion
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "projet_gestion_comptes_bancaires";

// création d'une instance PDO pour la connexion à la base de données
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // afficher un message en cas d'erreur de la connexion
    echo "Échec de la connexion à la base de données : " . $e->getMessage();
}
?>
