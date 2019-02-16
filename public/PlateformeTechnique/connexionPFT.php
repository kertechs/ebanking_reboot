<?php
session_start(); 
?>
<head>
  <title>Back office Banque Dauphine</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">


</head>

<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
        <a class="navbar-brand" href="http://www.banquedauphine.site">BANQUE DAUPHINE </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
    </nav>
    


    <div class="container">
    <hr>

        <div class="row">

          <aside class="col-sm-4">
             <div class="alert alert-danger" role="alert">
  

      <?php
      if (isset($_SESSION['alerte'])){
        echo $_SESSION['alerte'];
        unset($_SESSION['alerte']);
      }else
    ?>
    </div>
            <p>Connexion à la platefome technique de la Banque Dauphine</p>
                <div class="card">
                    <article class="card-body">
                    <h4 class="card-title mb-4 mt-1">Se connecter</h4>

                     <form action="control_connexion.php" method="post">
                            <div class="form-group">
                              <label>Adresse mail</label>
                                <input name="email" class="form-control" placeholder="Email" type="email">
                            </div> 
                            <div class="form-group">
                              <label>Mot de passe</label>
                                <input name="password"class="form-control" placeholder="******" type="password">
                            </div>  
                           
                            <div class="form-group">
                                <button name="connexion" type="submit" class="btn btn-primary btn-lg btn-block"> Se connecter </button>
                            </div>                                                           
                        </form>
                     </article>
                </div>
            </aside>
        </div>
    </div>


</body>
</html>
