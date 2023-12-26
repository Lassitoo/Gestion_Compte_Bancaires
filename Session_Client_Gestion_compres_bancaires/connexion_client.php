<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion client</title>
    <link rel="stylesheet" href="fontAwesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    color : blue;
    padding-top:20px;
}
input[type="email"], input[type="password"] {
    width: 60%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    box-shadow: 0 0 5px 0 rgba(0,0,0,0.2);
    margin-top:20px;
}
#btnretour{
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-left:80%;
    margin-bottom: 20px;
    margin-right:10px;
}

    </style>
</head>
<body>

<button id="btnretour" onclick="window.location.href = '../index.php';"> Page index ⬅️</button>

    <h1>Session Client</h1>
    <div id="login-form">
        <form action="connexion_client.php" method="POST">
            <label for="email_client">Email :</label>
            <input type="email" id="email_client" name="email_client" required>
            <label for="mot_de_passe_client">Mot de passe :</label>
            <input type="password" id="mot_de_passe_client" name="mot_de_passe_client" required>
            <button type="submit">Se connecter</button>
        </form>
        <a href="#">Mot de passe oublié ?</a>
        <a href="creer_compte_client.php">Créer un compte client</a>
    </div>
    <?php
       // démarrage de la session
    session_start();
    if (isset($_SESSION['id_client'])) {
        header('Location: client_profil.php');
    }
    
    // vérification des informations d'identification de l'agent
    require_once 'Client.php';
    if (isset($_POST['email_client']) && isset($_POST['mot_de_passe_client'])) {
        $email_client = $_POST['email_client'];
        $mot_de_passe_client = $_POST['mot_de_passe_client'];
        $client = Client::verifier_identification($email_client, $mot_de_passe_client);
    
        if ($client !== false) {
            // enregistrement du client dans la session
            session_start();
            $_SESSION['id_client'] = $client->id_client;
            $_SESSION['nom_client'] = $client->nom_client;
            
            // redirection vers la page du profil du client
            header('Location: client_profil.php');
            exit();
        } else {
            // message d'erreur si les informations d'identification sont incorrectes
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
    ?>
</body>
</html>
