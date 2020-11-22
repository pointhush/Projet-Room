<?php
// PAGE OK. ON NE TOUCHE PLUS 
require_once('inc/init.php');
$title = 'Accueil'; // titre de la page




require_once('inc/header.php');

?>
<div class="row">
    <div class="col-6 col-md-3 bg-faded">
    <?php
        // catégories de salle
        $listcateg = execRequete("SELECT DISTINCT categorie FROM salle ORDER BY categorie");
        ?>
        
        <form action="" method="post">
        <label for="categ" class="h5">Catégories</label>
           <select name="categ" id="categ" class="form-control mb-2"> <option value="*">Toutes</option>
            <?php
                while( $categ = $listcateg->fetch() ){
                    ?>

<option value="<?= $categ['categorie'] ?>" <?= ( isset($_POST['categ']) && $_POST['categ'] == $categ['categorie'] ) ? 'selected':'' ?>><?= $categ['categorie'] ?></option>
       
                   
                  <?php
                }
                echo '</select>';
       // ville des salles
       $listville = execRequete("SELECT DISTINCT ville FROM salle ORDER BY ville");
       ?>
     <label for="ville" class="h5">Villes</label>
          <select name="ville" id="ville" class="form-control mb-2"> <option value="*">Toutes</option>
           <?php
               while( $ville = $listville->fetch() ){
                   ?>

<option value="<?= $ville['ville'] ?>" <?= ( isset($_POST['ville']) && $_POST['ville'] == $ville['ville'] ) ? 'selected':'' ?>><?= $ville['ville'] ?></option>
      
                  
<?php
                }
                echo '</select>';
       // capacite des salles
       ?>
     <label for="capacite" class="h5">Capacite</label>
          <div class="row mbz-2">
          <div class="col">
      <input type="number" name="capacitemin" id="capacitemin" class="form-control" placeholder="minimum" value="<?= $_POST['capacitemin'] ?? $produit_courant['capacitemin'] ?? '' ?>">
    </div>
    <div class="col">
      <input type="number" name="capacitemax" id="capacitemax" class="form-control" placeholder="maximum" value="<?= $_POST['capacitemax'] ?? $produit_courant['capacitemax'] ?? '' ?>">
    </div>
          </div>
          <!-- Prix des salles -->
          <label for="prix" class="h5">Prix</label>
          <div class="row pb-3">
          <div class="col">
      <input type="number" name="prixmin" id="prixmin" class="form-control" placeholder="minimum" value="<?= $_POST['prixmin'] ?? $produit_courant['prixmin'] ?? '' ?>">
    </div>
    <div class="col">
      <input type="number" name="prixmax" id="prixmax" class="form-control" placeholder="maximum" value="<?= $_POST['prixmax'] ?? $produit_courant['prixmax'] ?? '' ?>">
    </div>
          </div>
           <!-- DATE des salles -->
          <h5> Période</h5>
      <label for="date_arrivee" class="h6"> Date d'arrivée </label>
      <div class="row pb-3"><div class="input-group-prepend col-2">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <div class="col-10"><input type="text" class="form-control" name="date_arrivee" id="date_arrivee" value="<?= $_POST['date_arrivee'] ?? $produit_courant['date_arrivee'] ?? '' ?>"></div></div>
                    <label for="date_depart" class="h6"> Date de départ </label>
      <div class="row pb-3"><div class="input-group-prepend col-2">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <div class="col-10"><input type="text" class="form-control" name="date_depart" id="date_depart" value="<?= $_POST['date_depart'] ?? $produit_courant['date_depart'] ?? '' ?>"></div></div>
                 
             <button class="btn btn-lg  btn-info btn-block  text-uppercase font-weight-bold mb-2" type="submit">Rechercher</button>
             </form> 
             <?php
        // Afficher les produits en tenant compte d'un éventuel choix de catégorie
        $whereClause = '';
        $arg = array();

        if( !empty($_POST['categ']) && $_POST['categ'] != '*'){
            $whereClause .= ' AND categorie=:categorie ';
            $arg['categorie'] = $_POST['categ'];
        }
        if( !empty($_POST['ville']) && $_POST['ville'] != '*'){
            $whereClause .= ' AND ville=:ville ';
            $arg['ville'] = $_POST['ville'];
        }
    
        if( !empty($_POST['capacitemin'])){
            $whereClause .= ' AND capacite >= :capacitemin ';
            $arg['capacitemin'] = $_POST['capacitemin'];
        } 
        if( !empty($_POST['capacitemax'])){
            $whereClause .= ' AND capacite <= :capacitemax ';
            $arg['capacitemax'] = $_POST['capacitemax'];
        }
        if( !empty($_POST['prixmin'])){
            $whereClause .= ' AND prix >= :prixmin ';
            $arg['prixmin'] = $_POST['prixmin'];
        } 
        if( !empty($_POST['prixmax'])){
            $whereClause .= ' AND prix <= :prixmax ';
            $arg['prixmax'] = $_POST['prixmax'];
        }
        if( !empty($_POST['date_arrivee']) && $_POST['date_arrivee'] != '*'){
            $whereClause .= ' AND date_arrivee>=:date_arrivee';
            $arg3 = DateTime::createFromFormat('d/m/Y', $_POST['date_arrivee']) ;
            $arg['date_arrivee'] = $arg3->format('Y-m-d H:i:s');
            
        }
        if( !empty($_POST['date_depart']) && $_POST['date_depart'] != '*'){
            $whereClause .= ' AND date_depart>=:date_depart';
            $arg2 = DateTime::createFromFormat('d/m/Y', $_POST['date_depart']) ;
            $arg['date_depart'] = $arg2->format('Y-m-d H:i:s');
            
        }
        // Date du jour 
        $today = date('Y-m-d');

      // REQUETE DU COMPTAGE DU NOMBRE DE RESULTAT
        $listProduits2 = execRequete(" SELECT *, ROUND(AVG(note)) AS moyenne FROM produit p LEFT JOIN salle s  ON p.id_salle = s.id_salle LEFT JOIN avis a ON p.id_salle = a.id_salle  WHERE true $whereClause AND DATEDIFF(date_arrivee,'$today')>=1 GROUP BY id_produit HAVING etat='libre'",$arg);
// PAGINATION
        $count2 = $listProduits2->rowCount(); 
        $limite = 9;
        $ptt = ceil($count2 / $limite);
        if(isset($_GET['page']) AND !empty($_GET['page']) AND $_GET['page']> 0 AND $_GET['page'] <= $ptt){
            $_GET['page'] = intval($_GET['page']);
            $pageCourante = $_GET['page'];
        }else{
            $pageCourante = 1;
        }
       
        $depart =($pageCourante - 1) * $limite;
         
       
         // REQUETE DE LA PAGINATION
        $listProduits = execRequete(" SELECT *, ROUND(AVG(note)) AS moyenne FROM produit p LEFT JOIN salle s  ON p.id_salle = s.id_salle LEFT JOIN avis a ON s.id_salle = a.id_salle  WHERE true $whereClause  AND DATEDIFF(date_arrivee,'$today')>=1 GROUP BY id_produit HAVING etat='libre'   LIMIT $depart,$limite",$arg);
      
        

        // ORTHOGRAPHE DE RESULTAT 
        $count = $listProduits->rowCount(); 
       if($count2 < 2){
           $motres =' résultat';
       }else{
        $motres =' résultats';  
       }
    
       

       ?>
<!--  AFFICHAGE DU NOMBRE DE RESULTAT -->
<div class="alert alert-info text-center" role="alert"> <?=$count2 .$motres?></div>
        
    </div>





    <div class="col-6 col-md-9 row  ">
    
        
       <?php
       // SI AUCUN RESULTAT DISPONIBLE
       if($count2 == 0){
       $nonen = '<div class="alert alert-danger text-center" role="alert" style="height:auto">
       Désolé. Aucune salle ne correspond à votre recherche.
      </div>';
       }else{
           $nonen='';
       }
         
      
       // TRONCAGE DU CARACTERE
            while(( $produit = $listProduits->fetch())){
                $lg_max = 45; // Nb. de caractères sans '...'
        $description = $produit['description'];
        $description = strip_tags($description);
        
        if (strlen($description) > $lg_max) { 
            $description = substr($description, 0, $lg_max) ;
            $last_space = strrpos($description, " ") ;
            $description = substr($description, 0, $last_space)."..." ;
        }
        //FORMAT DES DATES
        $produit['date_arrivee']= date("d/m/Y", strtotime($produit['date_arrivee']));
        $produit['date_depart']= date("d/m/Y", strtotime($produit['date_depart']));

          
                
  
        ?>
                <div class="col-12 col-md-6 col-lg-4 p-1">
                  <!-- DEBUT D'UNE CARTE RESULTAT-->
                    <div class="border bg-faded">
                     <div class="thumbnail">
                     
                            <a href="<?= URL . 'fiche_produit.php?id_produit=' . $produit['id_produit']?>">
                            <?php
                         // AFFICHAGE DE SALLE OU BUREAU EN FONCTION DU TYPE
                        if($produit['categorie']=="bureau"){
                            $categaff="Bureau";
                        }else{
                           $categaff="Salle";
                        }
                            ?>
                            <img src="<?= URL .'photo/'.$produit['photo'] ?>" alt="<?= $produit['titre'] ?>" class="img-fluid">
                            </a>
                        </div>
                        <div class="caption mx-2 py-2">
                           <div class="row"> <div class="col-8"><h3 class="text-nowrap" style="font-size:1.3rem;text-transform:capitalize;"><a href="<?= URL . 'fiche_produit.php?id_produit=' . $produit['id_produit']?>"><?= $categaff.' '. $produit['titre'] ?></a></h3></div><div class="col-4 prix"><?= $produit['prix'] ?> €</div></div>
                            <p class="mb-0" style="font-size:0.9rem;"><?= $description ?></p>
                            <p class="pt-1 mb-0"><i class="fas fa-calendar-alt"></i>
                            
                            
                             <?= $produit['date_arrivee'] ?> au <?= $produit['date_depart'] ?></p>
                            <div class="row"> <div class="col-8">
                            <div class="row align-items-center"> 
                                <div class="col-8 pt-2 star">
                           <?=avis($produit['moyenne']) ?>
                                    
                                </div>
                            
                            
                            
                            </div></div><div class="col-4 prix pt-1"><a href="<?= URL . 'fiche_produit.php?id_produit=' . $produit['id_produit']?>"> <i class="fas fa-search "style="font-size:1rem"></i> <span style="font-size:1.2rem">Voir</span> </a></div></div>
                        </div>
                    </div>
                </div>
                <?php
            }

           
            
        ?>
        <!-- AFFICHAGE DES MESSAGES D'ALERTE -->
      <div class="text-center col-12"><?= $nonen ?> </div>
     </div><div class="row w-100"> 
       <!-- AFFICHAGE DE la PAGINATION -->
     <nav aria-label="Page navigation example" class="col-12">
  <ul class="pagination justify-content-end">
    <li class="page-item disabled">
      
    </li>
    
    
      <?php
   if($count2>9){
        for($i=1;$i<=$ptt;$i++) {
            
            echo '<li class="page-item"><a class="page-link" href="'.URL.'index.php?page='.$i.'">'.$i.'</a></li>';

        }
    }
    
      ?>
  </ul>
</nav></div> </div>
   

<?php

require_once('inc/footer.php');