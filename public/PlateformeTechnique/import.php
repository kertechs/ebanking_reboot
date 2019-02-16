<!doctype html>

<html lang="en">
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
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">      
            </li>
        </ul>
        <span class="navbar-text">
          <a class="btn btn-outline-primary" href="deconnexion.php" role="button">SE DECONNECTER</a>
        </span>
    </div>
  </nav>
        
    </nav>
    <div class="container">
    <hr>
        <div class="row">
            <aside class="col-sm-4">
            <p>Insertion des opérations par la Plateforme technique</p>
                <div class="card">
                    <article class="card-body">
                    <h4 class="card-title mb-4 mt-1">Insérer fichier</h4>
                  



                    <form method="POST" enctype="multipart/form-data" action="upload.php">
   
   

                            <div class="form-group">
                                 
                                  <!-- On limite le fichier à 100Ko -->
                                  <input type="hidden" name="MAX_FILE_SIZE" value="100000">
                                    
                                     <input name="userfile" type="file" value="table"/> 
                            </div>
                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"> Envoyer le fichier </button>
                            </div>                                                           
                        </form>
                     </article>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>

