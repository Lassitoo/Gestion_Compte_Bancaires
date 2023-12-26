<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Agent</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <style>
       
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
body {
   background-color: aliceblue;
}
    </style>
</head>
<body>

<button id="btnretour" onclick="window.location.href = '../index.php';"> Page index ⬅️</button>

    <h1>Session Agent.</h1>
    <div id="login-form">
        <form action="connexion_agent.php" method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            <label for="mot_de_passe_agent">Mot de passe :</label>
            <input type="password" id="mot_de_passe_agent" name="mot_de_passe_agent" required>
            <button type="submit">Se connecter</button>
        </form>
        <a href="#">Mot de passe oublié ?</a>
    </div>

    <?php
  


    // démarrage de la session
    session_start();
    if (isset($_SESSION['id_agent'])) {
        header('Location: agent_profil.php');
    }
    
    // vérification des informations d'identification de l'agent
    require_once 'Agent.php';
    if (isset($_POST['email']) && isset($_POST['mot_de_passe_agent'])) {
        $email = $_POST['email'];
        $mot_de_passe_agent = $_POST['mot_de_passe_agent'];
        $agent = Agent::verifier_identification($email, $mot_de_passe_agent);
    
        if ($agent !== false) {
            // enregistrement de l'agent dans la session
            session_start();
            $_SESSION['id_agent'] = $agent->id_agent;
            $_SESSION['nom_agent'] = $agent->nom_agent;
            
            // redirection vers la page du profil de l'agent
            header('Location: agent_profil.php');
            exit();
        } else {
            // message d'erreur si les informations d'identification sont incorrectes
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
    ?>
    
    


</body>
</html>
