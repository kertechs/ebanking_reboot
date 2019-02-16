<?php
session_start();
if(isset($_POST['connexion'])) { 
    if(empty($_POST['email'])) {
        $_SESSION['alerte'] = "Le champ Pseudo est vide.";
        header('Location: connexionPFT.php');
        
    } else {
        // on vérifie maintenant si le champ "Mot de passe" n'est pas vide"
        if(empty($_POST['password'])) {
            $_SESSION['alerte'] = "Le champ Mot de passe est vide.";
            header('Location: connexionPFT.php');
           

        } else {
            
                // les champs sont bien posté et pas vide, on sécurise les données entrées par le membre:
                $email = htmlentities($_POST['email'], ENT_QUOTES, "ISO-8859-1"); // le htmlentities() passera les guillemets en entités HTML, ce qui empêchera les injections SQL

                $password = htmlentities($_POST['password'], ENT_QUOTES, "ISO-8859-1");
                //on se connecte à la base de données:
                $mysqli = mysqli_connect("localhost", "ourda", "ourda", "ebanking2019");
                //on vérifie que la connexion s'effectue correctement:
                if(!$mysqli){
                      $_SESSION['alerte'] = "Erreur de connexion à la base de données.";
                        header('Location: connexionPFT.php');
                    
                }else{
                    // on fait maintenant la requête dans la base de données pour rechercher si ces données existe et correspondent:
                    $Requete = mysqli_query($mysqli,"SELECT * FROM banker_user WHERE email = '".$email."' AND password = '".$password."'");
                    if(mysqli_num_rows($Requete) == 0) {
                        $_SESSION['alerte'] = "Le pseudo ou le mot de passe est incorrect, le compte n'a pas été trouvé.";
                        header('Location: connexionPFT.php');
                       
                    } else {
                        // on ouvre la session avec $_SESSION:
                        $_SESSION['email'] = $email; 
                        header('Location: import.php');
                        exit();
                        
                    }
                
            }
        }
    }
}
?>