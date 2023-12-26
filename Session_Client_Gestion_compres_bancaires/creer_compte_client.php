<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de création de compte client</title>
    <link rel="stylesheet" href="fontAwesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Demande de création de compte client</h1>
    <form  action="demandes_creation_comptes_clients.php" method="POST">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>
        <label for="email">Email :</label>
        <input type="email" id="email_creer_compte_client" name="email" required>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        <label for="mot_de_passe_confirm">Confirmer le mot de passe :</label>
        <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required>
        <label for="adresse">Adresse :</label>
        <textarea id="adresse" name="adresse" required></textarea>
        <label for="telephone">Téléphone :</label>
        <input type="tel" id="telephone" name="telephone" required>
        <label for="piece_identite">Pièce d'identité :</label>
        <select id="piece_identite" name="piece_identite" required>
            <option value="carte_identite">Carte d'identité</option>
            <option value="passeport">Passeport</option>
            <option value="permis_conduire">Permis de conduire</option>
        </select>
        <label for="numero_piece_identite">Numéro de pièce d'identité :</label>
        <input type="text" id="numero_piece_identite" name="numero_piece_identite" required>
        <button type="submit">Envoyer la demande</button>
    </form>
</body>
</html>