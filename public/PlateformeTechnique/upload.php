<?php
session_start();
?>
<html>
<head>
     <meta charset="utf-8">
</head>
<body>

<?php
extract(filter_input_array(INPUT_POST));
$fichier=$_FILES["userfile"]["name"];
     if ($fichier){
          $fp = fopen ($_FILES["userfile"]["tmp_name"], "r");

     //declaration de la variable "cpt" qui permettra de compter le nombre d'enregitrements réalisés
     $cpt=0;
?>

<?php //importation
     $compteur_lignes = 0;
     while (!feof($fp)) {
          $compteur_lignes++;
          $ligne = fgets($fp,2555);
          
          if ($compteur_lignes == 1)
          {
               continue;
          }

          //echo $ligne . '<br />';
          //on crée un tableau des éléments séparés par des points virgule
          $liste = explode(";",$ligne);
          $table = filter_input(INPUT_POST, 'userfile');

         $date_execution          = ( isset($liste[0]) ) ? $liste[0] : Null;
         $date_execution          = date('Y-m-d H:i:s');

         $type_operation          = ( isset($liste[1]) ) ? 'PTF_'.$liste[1] : PTF_.'NO_REASON';
          $credit_debit            = ( isset($liste[2]) ) ? $liste[2] : Null;
          $montant                 = ( isset($liste[3]) ) ? $liste[3] : Null;
          $emetteur_compte_id      = ( isset($liste[4]) ) ? $liste[4] : 1027;
          $destinataire_compte_id  = ( isset($liste[5]) ) ? $liste[5] : 1000;
          $created_from_ip         = ( isset($liste[6]) ) ? $liste[6] : Null;
          $updated_from_ip         = ( isset($liste[7]) ) ? $liste[7] : Null;
          //$created_by              = ( isset($liste[8]) ) ? $liste[8] : Null;
          $created_by              = 'ourda@banquedauphine.services';
          $updated_by              = $created_by;
          $deleted_at              = Null;
          $created_at              = date('Y-m-d H:i:s');
          $updated_at              = $created_at;
          $details                 = ( isset($liste[13]) ) ? $liste[13] : Null;
          $compte_emetteur_id      = ( isset($liste[14]) ) ? $liste[14] : Null;
          $beneficiaire_id         = ( isset($liste[15]) ) ? $liste[15] : Null;
          $compte_destinataire_id  = ( isset($liste[16]) ) ? $liste[16] : Null;
          $beneficiaire_id         = "NULL";

                  
          if ($date_execution!=''){
              // $cpt++;

               $db = new mysqli('localhost', 'root', 'root', 'ebanking2019');
               $sql = "INSERT INTO operations (date_execution, type_operation, credit_debit, montant, emetteur_compte_id, destinataire_compte_id, created_from_ip, updated_from_ip, created_by, updated_by, deleted_at, created_at, updated_at, details, compte_emetteur_id, beneficiaire_id, compte_destinataire_id)";
               $sql .= " VALUES(";
               $sql .= ($date_execution == "NULL")?"null":"'".$date_execution."'";
               $sql .= ',';
               $sql .= ($type_operation == "NULL")?"null":"'".$type_operation."'";
               $sql .= ',';
               $sql .= ($credit_debit == "NULL")?"null":"'".$credit_debit."'";
               $sql .= ',';
               $sql .= ($montant == "NULL")?"null":"'".$montant."'";
               $sql .= ',';
               $sql .= ($emetteur_compte_id == "NULL")?"null":"'".$emetteur_compte_id."'";
               $sql .= ',';
               $sql .= ($destinataire_compte_id == "NULL")?"null":"'".$destinataire_compte_id."'";
               $sql .= ',';
               $sql .= ($created_from_ip == "NULL")?"null":"'".$created_from_ip."'";
               $sql .= ',';
               $sql .= ($updated_from_ip == "NULL")?"null":"'".$updated_from_ip."'";
               $sql .= ',';
               $sql .= ($created_by == "NULL")?"null":"'".$created_by."'";
               $sql .= ',';
               $sql .= ($updated_by == "NULL")?"null":"'".$updated_by."'";
               $sql .= ',';
               $sql .= ($deleted_at='null');
               $sql .= ',';
               $sql .= ($created_at == "NULL")?"null":"'".$created_at."'";
               $sql .= ',';
               $sql .= ($updated_at == "NULL")?"null":"'".$updated_at."'";
               $sql .= ',';
               $sql .= ($details == "NULL")?"null":"'".$details."'";
               $sql .= ',';
               $sql .= ($compte_emetteur_id == "NULL")?"null":"'".$compte_emetteur_id."'";
               $sql .= ',';
               $sql .= ($beneficiaire_id == "NULL")?"null":"'".$beneficiaire_id."'";
               $sql .= ',';
               $sql .= ($compte_destinataire_id == "NULL")?"null":"'".$compte_destinataire_id."'";
               $sql.= ')';

               echo $sql .';<br/>';
               $result = $db-> query($sql);



          }
               //echo '<hr />';
     }
     fclose($fp);
     
     ?>
          <p align="center">- Importation réussie -</p>
          <h4 align="center"> vous avez  inséré <b><?php echo $compteur_lignes; ?></b> enregistrement(s) avec succès </h4>
          <?php
     
               // $_SESSION['alerte'] = "Insertion à la base de données réuissie"; 
               //header('Location: import.php');
     }else{//fichier inconnu 
?>
          <p align="center" >- Importation echouée -</p>
          <p align="center" ><B>Vous n'avez pas specifie de chemin valide...</B></p>
<?php 
     exit();
     }
     ?>


   </body>
   </html>