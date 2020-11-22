<?php
// ON NE TOUCHE PLUS A CETTE PAGE
require_once('inc/init.php');
// AFFICHAGE DES MESSAGES D'ALERTES
$message='';
// REQUETE 1 
$note = execRequete("SELECT ROUND(AVG(note)) AS moyenne FROM  salle s , avis a , membre m, produit p
WHERE s.id_salle = a.id_salle
AND s.id_salle = p.id_salle 
AND a.id_membre = m.id_membre
AND p.id_produit= :id_produit ",array('id_produit' => $_GET['id_produit']));
// UN SEUL PRODUIT = UNE SEULE PAGE + MOYENNE
if($note->rowCount() == 1){
    $donnees = $note->fetch();
    $note_finale = $donnees['moyenne'];
  
}else {
    $note_finale = 0;
}
// REQUETE 2 ET 3 RESULTAT GENERAL PUIS RESULTAT AFFICHAGE DES AVIS 
$resultat = execRequete("SELECT * FROM produit p, salle s
WHERE p.id_salle = s.id_salle 
AND p.id_produit=:id_produit"
,array('id_produit' => $_GET['id_produit']));
$resultarest =execRequete("SELECT * FROM  salle s , avis a , membre m, produit p
WHERE s.id_salle = a.id_salle
AND s.id_salle = p.id_salle 
AND p.id_produit=:id_produit 
AND a.id_membre = m.id_membre",array('id_produit' => $_GET['id_produit']));
if( !empty($_GET['id_produit'])){
    if($resultat->rowCount() == 1){
        $salle = $resultat->fetch();
       
    }else{ // SI PAS DE RESULTAT EQUIVALENT RENVOIE EN PAGE D'ACCEUIL
        header('location:' . URL);
        exit();
    }
}else{
    header('location:' . URL);
    exit();
}
//FONCTION DATE 
$datein=date("d-m-Y", strtotime($salle['date_arrivee']));
$dateout=date("d-m-Y", strtotime($salle['date_depart']));

    // Salle disponible
    if(!empty($_SESSION['attente']) && ($salle['date_arrivee']< date("Y-m-d"))){
        $messages .= $_SESSION['attente'];
        unset($_SESSION['attente']);
      }
// traitement d'un message posté
if( !empty($_POST) ){

    foreach($_POST AS $indice => $valeur){
        $_POST[$indice] = trim($valeur);
    }

    
        if( empty($_POST['commentaire']) || empty($_POST['note']) ){
        $message = '<div class="alert alert-danger mt-2">Merci de remplir tous les champs</div>';
    }
    else{
        // traitement l'insertion en BDD
        $message = '<div class="alert alert-success" role="alert">
  Votre message a bien été enregistré. Il s\'affichera sous peu. Veuillez recharger la page.
</div>';

        /* Limiter les injections HTML */
        /* sanitize = assainir */
        $_POST['note'] = htmlspecialchars($_POST['note'],ENT_QUOTES);
        $_POST['commentaire'] = htmlspecialchars($_POST['commentaire'],ENT_QUOTES);
        $req = "INSERT INTO avis (date_enregistrement,id_membre,note,commentaire, id_salle) VALUES (NOW(),:id_membre,:note,:message,:id_salle)";
        $resultats = $pdo->prepare($req);
        $resultats->execute(array(
            'id_membre' => $_SESSION['membre']['id_membre'],
            'id_salle' => $_POST['id_salle'],
            'note' => $_POST['note'],
            'message' => $_POST['commentaire']
        ));
    }

}


$title = 'Fiche Produit';

if($salle['categorie']=="bureau"){
    $categaff="Bureau";
}else{
   $categaff="Salle";
}

require_once('inc/header.php');
?>

<div class="row d-flex justify-content-between bg-faded">
  <div class="col-12"><?= $message ?></div>
     <h2 class="col-4"><?= $categaff .' '. $salle['titre'] ?> <?=avis($donnees['moyenne']) ?></h2>
     
    <div class="col-2"> <?php if((isConnected()) AND ($salle['etat'] == 'libre')) {?>
    <form action="commande.php" method="post"><input type="hidden" name="id_produit" value="<?= $salle['id_produit'] ?>"><button type="submit" class="btn btn-info" name="reserv">Réserver</button></form> <?php }
    if(!isConnected()){?>
    <a href="<?= URL . 'connexion.php' ?>" class="btn btn-info">Se Connecter</a>
     <?php }?>
    </div>
 </div>
<div class="row d-flex justify-content-between align-items-stretch bg-faded">
    <div class="col-7">
        <img src="<?= URL . 'photo/' . $salle['photo'] ?>" alt="<?= $salle['titre'] ?>" class="img-fluid img-sty">
    </div>
    <div class="col-5">
        <h5>Description</h5>
        <p class="text-justify" style="font-size:0.9rem;"><?= $salle['description'] ?></p>
        <h5>Localisation</h5>
       <?='<iframe src="'.$salle['carte'].'" allowfullscreen></iframe>' ?>
     </div>
 </div>
    <div class="row d-flex justify-content-center align-items-stretch mt-3 w-100 p-2 bg-faded">
    <div class="col-4"><p><i class="fas fa-calendar-alt"></i> Arrivée : <?= $datein ?></p>
    <p><i class="fas fa-calendar-alt"> </i> Départ : <?= $dateout ?></p></div>
    <div class="col-4"><p><i class="fas fa-user-alt"></i> Capacité : <?= $salle['capacite'] ?></p>
    <p style="text-transform:capitalize"><i class="fas fa-clipboard"></i> Catégorie : <?= $salle['categorie'] ?> </p></div>
    <div class="col-4"><p><i class="fas fa-map-marker-alt"></i> Adresse: <?= $salle['adresse'] ?>, <?= $salle['cp'] ?>  <?= $salle['ville'] ?></p>
    <p><i class="fas fa-euro-sign"></i> Tarif : <?= $salle['prix'] ?> €</p></div>
    </div>
    <div class="row">
        <h4> Autres produits</h4>
        <?php // GENERATION DE PRODUIT ALEATOIRE
         $listsalle = execRequete('SELECT photo, id_produit FROM salle s,produit p WHERE s.id_salle = p.id_salle');
        if($listsalle->rowCount() >= 4) { 
            $nb_salle = 4;
        } else {
            $nb_salle = $listsalle->rowCount();
        }
        
        $tableausalle =$listsalle->fetchAll();

        $listIndex = array_rand($tableausalle, $nb_salle);?>
        <div class="card-deck"> 
<?php
        for($i = 0; $i < $nb_salle; $i++) { ?> 
       
  <div class="card">
    <a href="<?= URL . 'fiche_produit.php?id_produit=' .$tableausalle[$listIndex[$i]]['id_produit']  ?>"><img src="<?= URL . 'photo/' .$tableausalle[$listIndex[$i]]['photo']  ?>" class="card-img-top" alt="..."></a>
  </div>


      <?php  }
        
        
        
        ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3"></div>
    </div>
    <hr>
    <div class="col-12">
    <div class="row justify-content-center ">
    <?php 
 
        while($avis = $resultarest->fetch()) { ?>
           <div class="card p-3 m-2 text-justify border-info " style="width:46vh;">
            <div class="card-body text-info"><div><p class="card-title star"><?=avis($avis['note'])?></p></div>
              <p class="card-text p-1" style="max-height:20vh;overflow-y:auto"><?=$avis['commentaire'] ?></p>
            </div>
            <div class="blockquote-footer p-3"><?=$avis['pseudo']?></div>
          </div>   
        <?php
    } ?>
    
    
</div>
    </div> <!--  COMMENTAIRE--> 
    <div class="row justify-content-between p-1 ">
        <div class="col-6"><?php if(!isConnected()){?>
    <a href="<?= URL . 'connexion.php' ?>" class="btn btn-info">Se Connecter</a>
     <?php } if(isConnected()){?>
        <a class="" data-toggle="collapse" href="#commentaire" role="button" aria-expanded="false" aria-controls="commentaire">
    Déposer un commentaire et une note
        </a><?php }?></div>
        <div class="col-6 text-right"> <a href="<?= URL;?>"> Retour vers le catalogue</a></div>
    </div>
    <div class="collapse" id="commentaire">
  <div class="row w-100">
  <div class="col-12">
  <form action="" method="post">
      <input type="hidden" name="id_salle" value="<?= $salle['id_salle'] ?>">
  <div class="form-row">
  <div class="col-10">
    <label for="commentaire" class="h6">Votre Commentaire</label>
    <textarea name="commentaire" class="form-control" id="commentaire" rows="3" value="<?= $_POST['message'] ?? '' ?>"></textarea>
    </div>
    <div class="col-2 p-3 text-center ">
        <div class="row"><div class="col">
    <label for="note">Note</label></div>
 <div class="row"><div class="col"><input name="note" type="number" max="5" min="0" value="5" class="form-control"> </div></div>
    </div></div>
  </div>
  <div class="row">
      <div class="col-12 text-center"> <button type="submit" class="btn btn-info m-3 w-50" name="avis">Envoyer</button></div>
  </div>
 
</form>
<?php


  ?>
  </div>
</div>
        </div>
        <div class="mb-4"></div>
    <?php
require_once('inc/footer.php');
?>