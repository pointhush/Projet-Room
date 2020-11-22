<?php
require_once('../inc/init.php');
//Titre de la page
$title = 'Gestion des Membres';

// Vérifier que l'on est admin
if(!isAdmin()){
    header('location:'.URL.'connexion.php');
    exit();
}
//Traitement du formulaire
if(!empty($_POST) && !isset($_POST['id_membre'])){
   
    $nb_champs_vides = 0;
    foreach($_POST as $key => $value){
        if(empty(trim($value))) $nb_champs_vides++; //accolades pas necessaires car 1 seule instruction
    }
    if($nb_champs_vides > 0){
        $messages .= '<div class="alert alert-danger">Il manque '.$nb_champs_vides.' information(s)</div>';
    }
    // Controle sur la longueur des caractères du pseudo
    if(!preg_match('#^[\w-.]{3,20}$#',$_POST['pseudo'])){ //Fonction qui teste que la variable en 2ème paramètre repond à l'expression regulière
        $messages .= '<div class="alert alert-danger">Merci de choisir un pseudo compris entre 3 et 20 caractères<br>Caractères autorisés : a à z, A à Z, 0 à 9, "_", "." et "-" </div>';
    }
    
    // Controle de l'email
    if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) && (!empty($_POST['email']))){
        $messages .= '<div class="alert alert-danger">L\'adresse email n\'est pas valide</div>';
    }
    if(empty($messages) && empty($_GET['id_membre'])){
        // Controler l'unicité du pseudo
        $controle = execRequete("SELECT * FROM membre WHERE pseudo=:pseudo", array('pseudo' => $_POST['pseudo']));
        if($controle->rowCount() > 0){
            $messages .= '<div class="alert alert-danger">Pseudo indisponible, merci d\'en choisir un autre</div>';
        }else{
            // Pseudo disponible on peut inscrire me membre
            $date_enregistrement = date('l d/m/Y H:i:s');
            extract($_POST); // génère des variables à partir des index du $_POST
            execRequete("INSERT INTO membre VALUES (NULL,:pseudo,:mdp,:nom,:prenom,:email,:civilite,:statut,NOW())", array(
                'pseudo'=> $pseudo,
                'mdp' => md5($mdp . SALT),// encryptage du mot de passe
                'nom'=> $nom,
                'prenom'=> $prenom,
                'email'=> $email,
                'civilite' => $civilite,
                'statut' => $statut
            ));
            header('location:' . URL . 'admin/gestion_membres.php');
            exit(); 
        }

    }
}    

// Suppression d'un membre
if(isset($_GET['action']) && $_GET['action']== 'delete' && !empty($_GET['id_membre'])){
    //Si $_GET['action'] existe et que $_GET['action'] a la valeur 'delete et que j'ai $_GET['id_membre'] non vide
    //ex: ?action=delete&id_membre=43
    $resultat = execRequete("SELECT * FROM membre WHERE id_membre=:id_membre", array('id_membre' =>$_GET['id_membre']));
    if($resultat->rowCount()== 1){
        $membre= $resultat->fetch();
        execRequete("DELETE FROM membre WHERE id_membre=:id_membre", array('id_membre' =>$_GET['id_membre']));
        header('location:'.URL.'admin/gestion_membres.php');
        exit();
    }
} 
// Chargement d'un membre à modifier
if(isset($_GET['action']) && $_GET['action']== 'modif' && !empty($_GET['id_membre'])){
    $resultat= execRequete("SELECT * FROM membre 
    WHERE id_membre=:id_membre", array('id_membre' =>$_GET['id_membre']));

    if(isset($date_enregistrement)) { 
    $date_enregistrement_en = str_replace('/', '-', $date_enregistrement);
    $date_enregistrement = date('Y-m-d', strtotime($date_enregistrement_en)); 
    }

    if($resultat->rowCount()==1){
        $membre_courant= $resultat->fetch();
    }
    $_GET['action']= 'ajout';
}

// traitement du post
if( !empty($_POST) ){
    // formulaire soumis
    
    // controle champs vides
    $nb_champs_vides = 0;
    foreach( $_POST as $value){
        if ( trim($value) == '' ) $nb_champs_vides++;
    }
    if ( $nb_champs_vides > 0 ){
        $messages .= '<div class="alert alert-danger">Merci de remplir '.$nb_champs_vides.' champ(s) manquant(s)</div>';
    }
    
    if( empty($messages) && !empty($_GET['id_membre'] )){
        // aucune erreur notifiée
        extract($_POST);
            
        if(!empty($_POST['id_membre'])):
          //il s'agit d'un update
            execRequete("UPDATE membre 
            SET mdp=:mdp,
            nom=:nom,
            prenom=:prenom,
            email=:email,
            civilite=:civilite,
            statut=:statut,
            date_enregistrement=:date_enregistrement

            
            WHERE id_membre=:id_membre",array(
                'id_membre'=> $id_membre,
                'mdp' => md5($mdp . SALT),
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'civilite' => $civilite,
                'statut' => $statut,
                'date_enregistrement' => $date_enregistrement
            
        ));
        
        endif;
        header('location:' . URL . 'admin/gestion_membres.php');
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
        <a class="nav-link <?=(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action']=='affichage')) ? 'active':''?>" href="?action=affichage">Affichage des membres</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?=(isset($_GET['action']) && $_GET['action']=='ajout') ? 'active':''?>" href="?action=ajout">Ajout de membres</a>
    </li>
</ul>
<?php
if(!isset($_GET['action']) || isset($_GET['action']) && $_GET['action'] =='affichage'){
    //5. Affichage des membres
    $resultat = execRequete("SELECT id_membre,pseudo,nom, prenom, email, civilite, statut, DATE_FORMAT(date_enregistrement, '%d/%m/%Y %T') AS date_enr FROM membre ORDER BY id_membre DESC");

    if($resultat->rowCount()== 0){
        ?>
        <div class="alert alert-warning">Il n'y a pas encore de membres enregistrés</div>
        <?php
    }else{
        ?>
        <p class="ml-2 pl-2 pt-4 font-weight-bold">Il y a <?= $resultat->rowCount()?> membre(s) dans ROOM</p>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <tr class="bg-dark text-white">
                    <th class="align-middle">Id_membre</th>
                    <th class="align-middle">Pseudo</th>
                    <th class="align-middle">Nom</th>
                    <th class="align-middle">Prénom</th>
                    <th class="align-middle">Email</th>
                    <th class="align-middle">Civilité</th>
                    <th class="align-middle">Statut</th>
                    <th class="align-middle">Date d'enregistrement</th>
                    <th colspan="3" class="align-middle">Actions</th>
                </tr>
                <?php
                    
                    while($membre= $resultat->fetch()){
                       
                     
                        ?>
                        <tr class="text-center">
                            <td class="align-middle"><?=$membre['id_membre'] ?></td>
                            <td class="align-middle"><?=$membre["pseudo"] ?></td>
                            <td class="align-middle"><?=$membre["nom"] ?></td>
                            <td class="align-middle"><?=$membre['prenom']?></td>
                            <td class="align-middle"><?=$membre['email'] ?></td>
                            <td class="align-middle"><?=$membre['civilite'] ?></td>
                            <td class="align-middle"><?= (isset($membre['statut']) && $membre['statut']== '1') ? 'Admin' : 'Membre' ?></td>
                            <td class="align-middle"><?=$membre['date_enr'] ?></td>
                            <td class="align-middle"><a href="?action=modif&id_membre=<?=$membre['id_membre']?>"><i class="fas fa-pencil-alt"></i></a></td>
                            <td class="align-middle"><a class="confsup_membre" href="?action=delete&id_membre=<?=$membre['id_membre']?>"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <?php    
                    }  
                        ?>  
                        <?php
                   
                ?>
            </table>
        </div>
       <?php 
    }
}
if ( isset($_GET['action']) && $_GET['action'] == 'ajout' ):
    //formulaire pour ajouter un membre
    ?>

    <h3 class="mt-4 mb-5"><?= (isset($membre_courant))  ? 'Formulaire de modification de membre' : 'Formulaire d\'ajout de membre'  ?></h3>

    <form method="post" action="" >
    <?php
    // Ajout d'un champ  caché pour mémoriser l'id du membre et la date d'enregistrement
        if( isset($membre_courant['id_membre'])){
            ?>
            <input type="hidden" name="id_membre" value="<?= $_POST['id_membre'] ?? $membre_courant['id_membre']  ?>">
            <input type="hidden" name="date_enregistrement" value="<?= $_POST['date_enregistrement'] ?? $membre_courant['date_enregistrement']  ?>">
            <?php
        }
       
        
    ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="pseudo">Pseudo</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-user"></i></div>
                    </div>
                    <input type="text" name='pseudo' id='pseudo' placeholder="Pseudo" class="form-control" value="<?=$_POST['pseudo'] ?? $membre_courant['pseudo'] ??''?>" required <?= (isset($membre_courant['id_membre'])) ? 'disabled' : '' ?>>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                    </div>
                    <input type="email" name='email' id='email' placeholder="Adresse email" class="form-control" value="<?=$_POST['email'] ?? $membre_courant['email'] ?? ''?>" required>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="mdp">Mot de passe</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                    </div>
                    <input type="password" name='mdp' id='mdp' placeholder="Mot de passe" class="form-control" value="<?=$membre_courant['mdp'] ?? '' ?>">
                </div>
            </div>
            <div class="form-group  col-md-3">
                <label for="civilite">Civilité</label>
                <select name="civilite" id="civilite" class="form-control" required>
                    <option value="m" >Monsieur</option>
                    <option value="f" <?= (isset($_POST['civilite']) && $_POST['civilite']== 'f' ) || isset($membre_courant['civilite']) && ($membre_courant['civilite'] == 'f') ? 'selected' : ''?>>Madame</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nom">Nom</label>
                <input type="text" name='nom' id='nom' placeholder="Nom" class="form-control" value="<?=$_POST['nom'] ?? $membre_courant['nom'] ?? ''?>" required>
            </div>
            <div class="form-group col-md-3">
                <label for="statut">Statut</label>
                <select name="statut" id="statut" class="form-control"  required>
                    <option value="">--</option>
                    <option value="1" <?= ((isset($_POST['statut']) && $_POST['statut']== '1' )) || isset($membre_courant['statut']) && ($membre_courant['statut'] == '1')  ? 'selected' : ''?>>Admin</option>
                    <option value="2" <?= (isset($_POST['statut']) && $_POST['statut']== '2' ) || isset($membre_courant['statut']) && ($membre_courant['statut'] == '2') ? 'selected' : ''?>>Membre</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="prenom">Prenom</label>
                <input type="text" name='prenom' id='prenom' placeholder="Prénom" class="form-control" value="<?=$_POST['prenom'] ?? $membre_courant['prenom'] ?? ''?>" required>
            </div>    
        </div>
        <input type="submit" class="btn btn-lg  btn-primary btn-block  font-weight-bold mb-4 mt-4" value="Enregistrer">
    </form>
    <?php
    
endif;
require_once('inc_admin/footer.php'); 

