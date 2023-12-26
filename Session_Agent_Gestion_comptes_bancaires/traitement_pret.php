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
?>