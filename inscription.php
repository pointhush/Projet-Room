<?php
require_once('inc/init.php');
//Titre de la page
$title = 'Inscription';

//Traitement du formulaire
if(!empty($_POST)){
   
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
    if(empty($messages)){
        // Controler l'unicité du pseudo
        $controle = execRequete("SELECT * FROM membre WHERE pseudo=:pseudo", array('pseudo' => $_POST['pseudo']));
        if($controle->rowCount() > 0){
            $messages .= '<div class="alert alert-danger">Pseudo indisponible, merci d\'en choisir un autre</div>';
        }else{
            // Pseudo disponible on peut inscrire me membre
            $date_enregistrement = date('l d/m/Y H:i:s');
            extract($_POST); // génère des variables à partir des index du $_POST
            execRequete("INSERT INTO membre VALUES (NULL,:pseudo,:mdp,:nom,:prenom,:email,:civilite,0,NOW())", array(
                'pseudo'=> $pseudo,
                'mdp' => md5($mdp . SALT),// encryptage du mot de passe
                'nom'=> $nom,
                'prenom'=> $prenom,
                'email'=> $email,
                'civilite' => $civilite
                

                              

               
            ));
            $_SESSION['attente']= '<div class ="alert alert-success">Inscription réussie, vous pouvez vous connecter</div>';
            //redirection vers la page de connexion
            header('location:'.URL.'connexion.php');
            exit();

        }

    }
}



require_once('inc/header.php');
echo $messages;
?>


  
    <div class="row">
        <div class="col-lg-10 col-xl-9 mx-auto">
            <div class="card card-signin flex-row my-5">
                <div class="card-img-left d-none d-md-flex">
                    <!-- Background image for card set in CSS! -->
                </div>
                <div class="card-body">
                    <h3 class="text-center">Inscription</h3> 
                    <form action="" method="post">
        
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="civilite">Civilité</label>
                                <select name="civilite" id="civilite" class="form-control" autofocus required>
                                    <option value="">---</option>
                                    <option value="m" <?= (isset($_POST['civilite']) && $_POST['civilite']== 'm' ) ? 'selected' : ''?>>Monsieur</option>
                                    <option value="f" <?= (isset($_POST['civilite']) && $_POST['civilite']== 'f' ) ? 'selected' : ''?>>Madame</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="prenom">Prenom</label>
                                <input type="text" name='prenom' id='prenom' placeholder="Prénom" class="form-control" value="<?=$_POST['prenom'] ?? ''?>" required>
                            </div>
                            <div class="form-group col-12">
                                <label for="nom">Nom</label>
                                <input type="text" name='nom' id='nom' placeholder="Nom" class="form-control" value="<?=$_POST['nom'] ?? ''?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="pseudo">Pseudo</label>
                                <input type="text" name='pseudo' id='pseudo' placeholder="Pseudo" class="form-control" value="<?=$_POST['pseudo'] ?? ''?>" required>
                            </div>
                            <div class="form-group col-12">
                                <label for="mdp">Mot de passe</label>
                                <input type="password" name='mdp' id='mdp' placeholder="Mot de passe" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                    <label for="email">Email</label>
                                    <input type="email" name='email' id='email' placeholder="Adresse email" class="form-control" value="<?=$_POST['email'] ?? ''?>" required>
                            </div>
                        </div>
                        <input type="submit" value="Enregistrer" class="btn btn-lg  btn-info btn-block  text-uppercase font-weight-bold mb-2">
                    </form>
                </div>
            </div>
        </div>
    </div>



<?php
require_once('inc/footer.php');

