<html>

<body>
<?php
require 'Agent.php';
$id=$_GET['id'];
?>
<div id="container" class="mt-3" >
    <h4>Creation compte pour client n&deg;<?= $id ?></h4>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
        <input type="text" id="id_client" name="id_client" hidden value="<?php echo $id;?>">
        <div class="form-group">
            <label for="numero_compte">Numero de compte</label>
            <input class="form-control" type="text" id="numero_compte" name="numero_compte" required>
        </div>
        <div class="form-group">
            <label for="solde">Solde</label>
            <input class="form-control" type="number" id="solde" name="solde" required>
        </div>
        <button class="btn btn-primary btn-block" type="submit" value="creer">Creer</button>
    </form>
</div>

<?php
if (isset($_POST['numero_compte']) && isset($_POST['solde']) && isset($_POST['id_client']))
try {
    $numero_compte=$_POST['numero_compte'];
    $solde=$_POST['solde'];
    $id=$_POST['id_client'];

    
    $stmt = $conn->prepare("SELECT * FROM compte_bancaire WHERE numero_compte = :numero_compte");
    $stmt->bindParam(':numero_compte', $numero_compte);
    $stmt->execute();
    $req = $stmt->fetch();

    if ($req['numero_compte'] !== $numero_compte ) {
        $stmt = $conn->prepare("INSERT INTO compte_bancaire (numero_compte, solde, id_Client ) VALUES (:numero_compte, :solde, :id)");
        $stmt->bindParam(':numero_compte', $numero_compte);
        $stmt->bindParam(':solde', $solde);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: agent_profil.php?creation_succes=succes');
    }
    else {
        header('Location: agent_profil.php?error_in_creation="existe"');
    }


}catch (PDOException $e) {
    $error =  "Erreur : " . $e->getMessage();
    header("Location: agent_profil.php?error_in_creation=$error");
} 
?>
</body>
</html>