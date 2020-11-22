<?php
require_once('../inc/init.php');
//Titre de la page
$title = 'Gestion des Produits';

// Vérifier que l'on est admin
if(!isAdmin()){
    header('location:'.URL.'connexion.php');
    exit();
}
// Suppression d'un produit
if(isset($_GET['action']) && $_GET['action']== 'delete' && !empty($_GET['id_produit'])){
    //Si $_GET['action'] existe et que $_GET['action'] a la valeur 'delete et que j'ai $_GET['id_produit'] non vide
    //ex: ?action=delete&id_produit=43
    $resultat = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit", array('id_produit' =>$_GET['id_produit']));
    if($resultat->rowCount()== 1){
        $produit= $resultat->fetch();
        $fic = $_SERVER['DOCUMENT_ROOT'].URL.'photo/'.$produit['photo']; //$fic fichier à supprimer
        if(!empty($produit['photo']) && file_exists($fic)){//file_exists() contrôle l'existance d'un fichier
            //suppression du fichier photo
            unlink($fic);
        }
        execRequete("DELETE FROM produit WHERE id_produit=:id_produit", array('id_produit' =>$_GET['id_produit']));
        header('location:'.URL.'admin/gestion_produits.php');
        exit();
    }
} 
//Chargement d'un produit à modifier
if(isset($_GET['action']) && $_GET['action']== 'modif' && !empty($_GET['id_produit'])){
    $resultat= execRequete("SELECT *, id_produit, DATE_FORMAT(date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(date_depart, '%d/%m/%Y') AS date_depart, prix, etat FROM produit p, salle s
    WHERE id_produit=:id_produit
    AND p.id_salle= s.id_salle", array('id_produit' =>$_GET['id_produit']));

    if($resultat->rowCount()==1){
        $produit_courant= $resultat->fetch();
    }
    $_GET['action']= 'ajout';
}

// traitement du post
if( !empty($_POST) ){
    // formulaire soumis

    // Tenir compte d'une modif de produit
    $photo_bdd= $_POST['photo_courante'] ?? '' ;

    if( !empty($_FILES['photo']['name']) ){
        // j'ai choisi un fichier à uploader
        $photo_bdd = $_POST['reference'] . '_' . $_FILES['photo']['name'];
        $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'photo/';

        $ext_auto = array('image/jpeg','image/png','image/gif');

        if( in_array($_FILES['photo']['type'], $ext_auto) ){
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
    
    if(!empty($_POST['id_produit'])){
        extract($_POST); 
            //Format date
            if(isset($_POST['date_arrivee'])){
                            
                $date_arrivee_en = str_replace('/', '-', $_POST['date_arrivee']);
                $date_arrivee = date('Y-m-d', strtotime($date_arrivee_en));
            }
            if(isset($_POST['date_depart'])){
                
                $date_depart_en = str_replace('/', '-', $_POST['date_depart']);
                $date_depart = date('Y-m-d', strtotime($date_depart_en));
            }

        execRequete("UPDATE produit 
        SET id_salle=:id_salle,
        date_arrivee=:date_arrivee,
        date_depart=:date_depart,
        prix=:prix,
        etat=:etat
        
        WHERE id_produit=:id_produit",array(
            'id_produit'=> $id_produit,
            'id_salle' => $id_salle,
            'date_arrivee' => $date_arrivee,
            'date_depart' => $date_depart,
            'prix' => $prix,
            'etat' => $etat ));
        
            header('location:' . URL . 'admin/gestion_produits.php');
            exit();
    }else{
        //Format date
        if(isset($_POST['date_arrivee'])){
            
            $date_arrivee_en = str_replace('/', '-', $_POST['date_arrivee']);
            $date_arrivee = date('Y-m-d', strtotime($date_arrivee_en));
        }
        if(isset($_POST['date_depart'])){
            
            $date_depart_en = str_replace('/', '-', $_POST['date_depart']);
            $date_depart = date('Y-m-d', strtotime($date_depart_en));
        }
        // Controler si salle libre
        $controle = execRequete("SELECT *,DATE_FORMAT(date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(date_depart, '%d/%m/%Y') AS date_depart, id_salle FROM produit  
        WHERE id_salle=:id_salle AND(:date_arrivee BETWEEN
            date_arrivee AND date_depart)
    OR id_salle=:id_salle AND (:date_depart BETWEEN
            date_arrivee AND date_depart)
             ", array(
            'date_arrivee' => $date_arrivee,
            'id_salle' => $_POST['id_salle'],
            'date_depart' => $date_depart));

        if($controle->rowCount() > 0){
            $message= $controle -> fetch();
            $messages .= '<div class="alert alert-danger">La salle est déjà occupée sur la période du '.$message['date_arrivee'].' au '.$message['date_depart'].'</div>';
        }
        if((($_POST["date_depart"] != 0) || $_POST["date_arrivee"] != 0) && ($date_arrivee < date("d-m-Y H:i:s") || $date_depart < date("d-m-Y H:i:s"))){
            $messages .= '<div class="alert alert-danger">Attention! vous ne pouvez pas choisir une date antérieure à aujourd\'hui</div>'; 
        } 
        if($_POST["date_depart"]!= 0 && $_POST["date_arrivee"]!=0 && $date_depart < $date_arrivee){
            $messages .= '<div class="alert alert-danger">Attention! La date de départ ne peut pas être antérieure à la date d\'arrivée</div>'; 
        } 
       /* echo '<pre>'; print_r($_POST); echo '</pre>';
        var_dump(date("d-m-Y H:i:s"));*/
        
            if( empty($messages) ){
                // aucune erreur notifiée
                extract($_POST);
                if(isset($date_arrivee)) { 
                    $date_arrivee_en = str_replace('/', '-', $date_arrivee);
                    $date_arrivee = date('Y-m-d', strtotime($date_arrivee_en));                  
                }
                if(isset($date_depart)) {
                    $date_depart_en = str_replace('/', '-', $date_depart);    
                    $date_depart = date('Y-m-d', strtotime($date_depart_en));
                    
                }
            // dates disponibles on peut inscrire le produit
            
            
        
            execRequete("INSERT INTO produit VALUES (NULL,:id_salle,:date_arrivee,:date_depart,:prix,:etat)",array(
                'id_salle' => $id_salle,
                'date_arrivee' => $date_arrivee,
                'date_depart' => $date_depart,
                'prix' => $prix,
                'etat' => $etat
            ));
             header('location:' . URL . 'admin/gestion_produits.php');
             exit();
            }
        }  
}

require_once('inc_admin/header.php'); 
echo $messages;


// Onglet
?>
<h2 class="text-center mb-5 mt-4"><?=$title?></h2>
<ul class="nav nav-tabs nav-justified">
    <li class="nav-item">
        <a class="nav-link <?=(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action']=='affichage')) ? 'active':''?>" href="?action=affichage">Affichage des produits</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?=(isset($_GET['action']) && $_GET['action']=='ajout') ? 'active':''?>" href="?action=ajout">Ajout produits</a>
    </li>
</ul>
<?php
if(!isset($_GET['action']) || isset($_GET['action']) && $_GET['action'] =='affichage'){
    // Affichage des produits
    $resultat = execRequete("SELECT *, id_produit, DATE_FORMAT(date_arrivee, '%d/%m/%Y'), CONCAT(DATE_FORMAT(date_arrivee, '%d/%m/%Y'),' 09:00') AS arrivee, DATE_FORMAT(date_depart, '%d/%m/%Y'), CONCAT(DATE_FORMAT(date_depart, '%d/%m/%Y'),' 19:00') AS depart, s.id_salle, titre, photo, prix, etat FROM salle s, produit p 
    WHERE s.id_salle=p.id_salle 
    ORDER BY id_produit DESC");

       if($resultat->rowCount()== 0){
        
        ?>
        <div class="alert alert-warning">Il n'y a pas encore de produits enregistrés</div>
        <?php
    }else{
        ?>
        <p class="ml-2 pl-2 pt-4 font-weight-bold">Il y a <?= $resultat->rowCount()?> produit(s) dans ROOM</p>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <tr class="bg-dark text-white">
                    <th class="align-middle">Id_produit</th>
                    <th class="align-middle">Date d'arrivée</th>
                    <th class="align-middle">Date de départ</th>
                    <th class="align-middle">Salle</th>
                    <th class="align-middle">Prix</th>
                    <th class="align-middle">Etat</th>
                    <th colspan="3" class="align-middle">Actions</th>
                </tr>
                <?php
                    $i=0;//Pour numéroter les id de img pour affichage lightbox
                    while($produits= $resultat->fetch()){
                        $i++;
                        ?>
                        <tr class="text-center">
                            <td class="align-middle"><?=$produits['id_produit'] ?></td>
                            <td class="align-middle"><?=$produits["arrivee"] ?></td>
                            <td class="align-middle"><?=$produits["depart"] ?></td>
                            <td class="align-middle"><?=$produits['id_salle'].' - Salle '.$produits['titre'].'<br>'.'<a href="#img'.$i.'"><img src="'.URL.'photo/'.$produits['photo'].'" alt="photo salle" style="max-width:100px;" class="img-fluid thumbnail"></a>
                            <a href="#_" class="lightbox" id="img'.$i.'"><img src="'. URL.'photo/'.$produits['photo'].'" style="max-width:70%;" alt="Photo salle">' ?></td>
                            <td class="align-middle"><?=$produits['prix'] ?></td>
                            <td class="align-middle"><?=$produits['etat'] ?></td>
                            <?php
                                    $_SESSION['expire']= '<div class ="alert alert-danger">Attention!! La date de validité de ce produit a expiré.</div>';
                             ?>   
                            <td class="align-middle">
                            <a href=" <?= URL.'fiche_produit.php?id_produit='.$produits['id_produit'] ?>">
                                <a href="<?= URL.'fiche_produit.php?id_produit='.$produits['id_produit']?>">
                            <i class="fas fa-search"></i></i></a></td>
                            <td class="align-middle"><a href="?action=modif&id_produit=<?=$produits['id_produit']?>"><i class="fas fa-pencil-alt"></i></a></td>
                            <td class="align-middle"><a class="confsup_produit" href="?action=delete&id_produit=<?=$produits['id_produit']?>"><i class="fas fa-trash-alt"></i></a></td>
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
    // formulaire pour ajouter un produit
    ?>

    <h3 class="mt-4 mb-5"><?= (isset($produit_courant))  ? 'Formulaire de modification de produit' : 'Formulaire d\'ajout de produit'  ?></h3>

    <form method="post" action="" enctype="multipart/form-data">
    <?php
    // Ajout d'un champ pour mémoriser l'id du produit 
        if( isset($produit_courant['id_produit']) || isset($_POST['id_produit']) ){
            ?>
            <input type="hidden" name="id_produit" value="<?= $_POST['id_produit'] ?? $produit_courant['id_produit']  ?>">
            <?php
        }

    ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="date_arrivee">Date d'arrivée</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <input type="text" class="form-control" name="date_arrivee" id="date_arrivee" value="<?= $_POST['date_arrivee'] ?? $produit_courant['date_arrivee'] ?? '' ?>">
                </div>               
            </div>
            <div class="form-group col-md-6">
                <label for="date_depart">Date de départ</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <input type="text" class="form-control" name="date_depart" id="date_depart" value="<?= $_POST['date_depart'] ?? $produit_courant['date_depart'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="id_salle">Salle</label> 
                <?php 
                //Affichage du nom de la salle
                $liste_salle = $pdo->query("SELECT DISTINCT s.id_salle,titre, adresse, cp, categorie, capacite FROM salle s, produit p ORDER BY s.id_salle");  ?>
                <select name="id_salle" class="form-control">
                <?php
                    while($salle = $liste_salle->fetch()){
                        $selected = '';
                        if((isset($_POST['id_salle']) && ($_POST['id_salle'] == $salle['id_salle'])) || (isset($produit_courant['id_salle']) && ($produit_courant['id_salle'] == $salle['id_salle']))) {
                            $selected = 'selected';
                        }

                        if(isset($salle['categorie']) && $salle['categorie'] == 'bureau') {
                            $piece = 'Bureau ';
                        }else{
                            $piece = 'Salle ';
                        }
                        ?>
                        <option value="<?=$salle['id_salle'].'" '. $selected ?>><?= $salle['id_salle'].' - '.$piece.' '.$salle['titre'].' - '.$salle['adresse'].' '.$salle['cp'].' - '.$salle['capacite'].' pers'?>
                        </option>
                        <?php
                        
                    }
                   
                ?>
                </select>

            </div>
            <div class="form-group col-md-6">
                <label for="prix">Tarif</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">€</div>
                    </div>
                    <input type="number" class="form-control" id="prix" name="prix" value="<?= $_POST['prix'] ?? $produit_courant['prix'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="etat">Etat</label>
                <select name="etat" id="etat" class="form-control" >
                    <option value="libre">Libre</option>
                    <option value="reservation" <?= ((isset($_POST['etat']) && $_POST['etat']== 'reservation') || (isset($produit_courant['etat']) && $produit_courant['etat']== 'reservation') ) ? 'selected' : ''?>>Réservation</option>
                </select>
            </div>
        </div>
        
        <input type="submit" class="btn btn-lg  btn-primary btn-block  font-weight-bold mb-4 mt-4" value="Enregistrer">
    </form>
    <?php
endif;
require_once('inc_admin/footer.php'); 