
<?php
require_once('../inc/init.php');
//Titre de la page
$title = 'Gestion des salles';

// Vérifier que l'on est admin
if(!isAdmin()){
    header('location:'.URL.'connexion.php');
    exit();
}
// Suppression d'un salle
if(isset($_GET['action']) && $_GET['action']== 'delete' && !empty($_GET['id_salle'])){
    //Si $_GET['action'] existe et que $_GET['action'] a la valeur 'delete et que j'ai $_GET['id_salle'] non vide
    //ex: ?action=delete&id_salle=43
    $resultat = execRequete("SELECT * FROM salle WHERE id_salle=:id_salle", array('id_salle' =>$_GET['id_salle']));
    if($resultat->rowCount()== 1){
        $salle= $resultat->fetch();
        $fic = $_SERVER['DOCUMENT_ROOT'].URL.'photo/'.$salle['photo']; //$fic fichier à supprimer
        if(!empty($salle['photo']) && file_exists($fic)){//file_exists() contrôle l'existance d'un fichier
            //suppression du fichier photo
            unlink($fic);
        }
        execRequete("DELETE FROM salle WHERE id_salle=:id_salle", array('id_salle' =>$_GET['id_salle']));
        header('location:'.URL.'admin/gestion_salles.php');
        exit();
    }
} 
// Chargement d'une salle à modifier
if(isset($_GET['action']) && $_GET['action']== 'modif' && !empty($_GET['id_salle'])){
    $resultat= execRequete("SELECT * FROM salle WHERE id_salle=:id_salle", array('id_salle' =>$_GET['id_salle']));
    if($resultat->rowCount()==1){
        $salle_courante= $resultat->fetch();
    }
    $_GET['action']= 'ajout';
}

// traitement du post
if( !empty($_POST) ){
    // formulaire soumis

    //Tenir compte d'une modif de salle
    $photo_bdd= $_POST['photo_courante'] ?? '' ;

    if( !empty($_FILES['photo']['name']) ){
        // j'ai choisi un fichier à uploader
        $photo_bdd = $_POST['titre'] . '_' . $_FILES['photo']['name'];
        $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'photo/';

        $extension_photo = strrchr($_FILES['photo']['name'], '.');

        $extension_photo = strtolower(substr($extension_photo, 1));

        $ext_auto = array('jpeg','png','gif','jpg');

        if( in_array($extension_photo, $ext_auto) ){
            // si l'extension est autorisée je copie sur le serveur
            move_uploaded_file($_FILES['photo']['tmp_name'],$chemin . $photo_bdd);
        }else{
            $messages .= '<div class="alert alert-danger">La photo n\'a pas été enregistrée. Formats acceptés : jpg, png, gif</div>';
        }
    }

    // controle champs vides
    $nb_champs_vides = 0;
    foreach( $_POST as $value){
        if ( trim($value) == '' ) $nb_champs_vides++;
    }
    if ( $nb_champs_vides > 0 ){
        $messages .= '<div class="alert alert-danger">Merci de remplir '.$nb_champs_vides.' champ(s) manquant(s)</div>';
    }
    
    if( empty($messages) ){
        // aucune erreur notifiée
        extract($_POST);
        if(!empty($_POST['id_salle'])):
          //il s'agit d'un update
            execRequete("UPDATE salle
            SET titre=:titre,
            description=:description,
            photo=:photo,
            pays=:pays,
            ville=:ville,
            adresse=:adresse,
            cp=:cp,
            carte=:carte,
            capacite=:capacite,
            categorie=:categorie
            WHERE id_salle=:id_salle",array(
                'id_salle'=> $id_salle,
                'titre'=> $titre,
                'description' => $description,
                'photo' => $photo_bdd,
                'pays' => $pays,
                'ville' => $ville,
                'adresse' => $adresse,
                'cp' => $cp,
                'carte' => $carte,
                'capacite' => $capacite,
                'categorie' => $categorie
                
            ));
            header('location:' . URL . 'admin/gestion_salles.php');
            exit();

        else:
        execRequete("INSERT INTO salle VALUES (NULL,:titre,:description,:photo,:pays,:ville,:adresse,:cp,:carte,:capacite,:categorie)",array(
            'titre'=> $titre,
            'description' => $description,
            'photo' => $photo_bdd,
            'pays' => $pays,
            'ville' => $ville,
            'adresse' => $adresse,
            'cp' => $cp,
            'carte' => $carte,
            'capacite' => $capacite,
            'categorie' => $categorie
        ));
    endif;
        
          header('location:' . URL . 'admin/gestion_salles.php');
          exit();
    }

}


require_once('inc_admin/header.php');

echo $messages;

// Onglet
?>
<h2 class="text-center mb-5 mt-4"><?=$title?></h2>
<ul class="nav nav-tabs nav-justified">
    <li class="nav-item">
        <a class="nav-link <?=(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action']=='affichage')) ? 'active':''?>" href="?action=affichage">Affichage des salles</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?=(isset($_GET['action']) && $_GET['action']=='ajout') ? 'active':''?>" href="?action=ajout">Ajout de salles</a>
    </li>
</ul>
<?php
if(!isset($_GET['action']) || isset($_GET['action']) && $_GET['action'] =='affichage'){
    // Affichage des salles
    $resultat = execRequete("SELECT * FROM salle");
    if($resultat->rowCount()== 0){
        ?>
        <div class="alert alert-warning">Il n'y a pas encore de salles enregistrés</div>
        <?php
    }else{
        ?>
        <p class="ml-2 pl-2 pt-4 font-weight-bold">Il y a <?= $resultat->rowCount()?> salle(s) dans ROOM</p>
        <div class="table-responsive">
            <table class="table table-bordered ">
                <tr class="bg-dark text-white">
                <?php
                    for($i=0;$i<$resultat->columnCount();$i++){
                        $colonne= $resultat->getColumnMeta($i);
                        
                        ?>
                        <th class="text-center align-middle"><?= ucfirst($colonne['name'])?></th> 
                        <?php
                    }
                    ?>
                    <th colspan="2"  class="align-middle">Actions</th>
                </tr>
                <?php
                    $i=0; //Pour numéroter les id de img pour affichage lightbox
                    while($salle= $resultat->fetch()){
                        $i++;
                        ?>
                        <tr>
                            <?php
                            
                            foreach($salle as $key => $value){
                                if($key == 'photo'){
                                   $i++;
                                    $value = '<a href="#img'.$i.'"><img src="'.URL.'photo/'.$value.'" alt="'.$salle['titre'].'" class="img-fluid thumbnail" style="max-width:100px;"></a>
                                    <a href="#_" class="lightbox" id="img'.$i.'"><img src="'. URL.'photo/'.$value.'" style="max-width:70%;" alt="'.$salle['titre'].'">';
                                      
                                }
                                if($key == 'description'){
                                    $lg_max = 45; // Nb. de caractères sans '...'
                                    $description = $salle['description'];
                                    $description = strip_tags($description);
                                    
                                    // Troncage de la description
                                    if (strlen($description) > $lg_max) { 
                                        $description = substr($description, 0, $lg_max) ;
                                        $last_space = strrpos($description, " ") ;
                                        $description = substr($description, 0, $last_space)."..." ;
                                    }
                                    $value = $description;
                                }

                                 if($key == 'carte'){
                                    $value = '<iframe src="'.$value.'" allowfullscreen></iframe>';
                                 }
                                 
                                ?>
                                <td class="align-middle<?=($key== 'id_salle' || $key== 'capacite' || $key== 'categorie'|| $key== 'titre' || $key== 'pays' || $key== 'ville') ? ' text-center' : '' ?>"><?= $value?></td>
                                <?php
                            }
                            ?>
                            <td  class="align-middle"><a href="?action=modif&id_salle=<?=$salle['id_salle']?>"><i class="fas fa-pencil-alt"></i></a></td>
                            <td  class="align-middle"><a class="confsup_salle" href="?action=delete&id_salle=<?=$salle['id_salle']?>"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>
       <?php 
    }



}
if ( isset($_GET['action']) && $_GET['action'] == 'ajout' ):
    // formulaire pour ajouter un salle
    ?>

    <h3 class="mt-4 mb-5"><?= (isset($salle_courante))  ? 'Formulaire de modification de salle' : 'Formulaire d\'ajout de salle'  ?></h3>

    <form method="post" action="" enctype="multipart/form-data">
    <?php
    // Ajout d'un champ pour mémoriser l'id du salle 
        if( isset($salle_courante['id_salle']) || isset($_POST['id_salle']) ){
            ?>
            <input type="hidden" name="id_salle" value="<?= $_POST['id_salle'] ?? $salle_courante['id_salle']  ?>">
            <?php
        }
    ?>
        <div class="form-row">
            <div class="form-group col-12">
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" class="form-control" value="<?= $_POST['titre'] ?? $salle_courante['titre'] ?? '' ?>" placehoder="12 caractères 10 caractères">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" class="form-control"><?= $_POST['description'] ?? $salle_courante['description'] ?? '' ?></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="photo">Photo
                    <div class="mt-1">
                        <div id="icone"><i class="fas fa-camera"></i></div>
                        <span id="nomfichier"></span>
                    </div>
                </label>
                <input type="file" id="photo" name="photo" class="form-control">
                <div id="box"></div>
                <?php
                    // Ajout d'un champs pour mémoriser la photo actuelle
                    if( !empty($salle_courante['photo']) ){
                        ?>
                        <em>Vous pouvez uploader une nouvelle photo</em>
                        <img src="<?= URL . 'photo/' . $salle_courante['photo'] ?>" alt="<?= $salle_courante['titre'] ?>" class="w-25">
                        <input type="hidden" name="photo_courante" value="<?=  $salle_courante['photo'] ?? $_POST['photo'] ?? '' ?>">
                        <?php
                    }
                    //photo uploadée

                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-md-4">
                <label for="cp">Code postal</label>
                <input type="text" id="cp" name="cp" class="form-control" value="<?= $_POST['cp'] ?? $salle_courante['cp'] ?? '' ?>">
            </div>
            <div class="form-group col-12 col-md-4">
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville" class="form-control" value="<?= $_POST['ville'] ?? $salle_courante['ville'] ?? '' ?>">
            </div>
            <div class="form-group col-12 col-md-4">
                <label for="pays">Pays</label>
                <input type="text" id="pays" name="pays" class="form-control" value="<?= $_POST['pays'] ?? $salle_courante['pays'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 ">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" class="form-control" value="<?= $_POST['adresse'] ?? $salle_courante['adresse'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 ">
                <label for="carte">Carte (src)</label>
                <input type="text" id="carte" name="carte" class="form-control " placeholder="https://..." value="<?= $_POST['carte'] ?? $salle_courante['carte'] ?? '' ?>">
                
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-6">
                <label for="capacite">Capacité</label>
                <input type="number" id="capacite" name="capacite" class="form-control" min="0" value="<?= $_POST['capacite'] ?? $salle_courante['capacite'] ?? '' ?>">
            </div>
            <div class="form-group col-6">
                <label for="categorie">Catégorie</label>
                <select id="categorie" name="categorie" class="form-control">
                    <option <?= ((isset($_POST['categorie'])&& $_POST['categorie'] =='reunion' )||(isset($produit_courant['categorie'])&& $produit_courant['categorie']== 'reunion')) ? 'selected' : ''?> value="reunion">Réunion</option>
                    <option <?= ((isset($_POST['categorie'])&& $_POST['categorie'] =='bureau' )||(isset($produit_courant['categorie'])&& $produit_courant['categorie']== 'bureau')) ? 'selected' : ''?> value="bureau">Bureau</option>
                    <option <?= ((isset($_POST['categorie'])&& $_POST['categorie'] =='formation' )||(isset($produit_courant['categorie'])&& $produit_courant['categorie']== 'formation')) ? 'selected' : ''?> value="formation">Formation</option>
                </select>
            </div>
        </div>
        <input type="submit" class="btn btn-lg  btn-primary btn-block  font-weight-bold mb-4" value="Enregistrer">
    </form>
    <?php
endif;

require_once('inc_admin/footer.php'); 

                