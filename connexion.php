<?php
require_once('inc/init.php');
//Titre de la page
$title = 'Connexion';

if(isset($_GET['action']) && $_GET['action']== 'deconnexion'){
  //Je détruis les infos du membre (je le deconnecte)
  unset($_SESSION['membre']);
}
//Si inscription réussie
if(!empty($_SESSION['attente'])){
  $messages .= $_SESSION['attente'];
  unset($_SESSION['attente']);
}
//Si l'internaute est déja connecté
if(isConnected()){
  header('location:'.URL.'compte.php');
 exit();
}
//L'internaute tente de se connecter, en cas de formulaire posté
if(!empty($_POST)){
  $resultat = execRequete("SELECT * FROM membre WHERE pseudo=:pseudo AND mdp=:mdp", array(
      'pseudo'=> $_POST['pseudo'],
      'mdp' => md5($_POST['mdp'] . SALT)
  ));
  if($resultat->rowCount()==1){
      //Connexion réussie
      $membre = $resultat->fetch();
      //On detruit la variable $membre['mdp'] du tableau
      unset($membre['mdp']);
      $_SESSION['membre'] = $membre;
      header('location:'. URL);
      exit();
  }else{
      $messages .= '<div class="alert alert-danger">Erreur sur les identifiants ou nom d\'utilisateur incorrecte</div>';   
  }
}


require_once('inc/header.php');
echo $messages;
?>

  <div class="row no-gutter">
    <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
      <div class="col-md-8 col-lg-6">
        <div class="login d-flex align-items-center py-5">
          <div class="container">
            <div class="row">
              <div class="col-md-9 col-lg-8 mx-auto bg-faded">
              <h3>Connexion</h3>
                <form action="" method="post">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="pseudo">Pseudo</label>
                            <input type="text" id="pseudo" value="<?= $_POST['pseudo'] ?? '' ?>" class="form-control" name="pseudo" placeholder="Pseudo">
                        </div>
                        <div class="form-group col-12">
                            <label for="mdp">Mot de passe</label>
                            <input type="password" id="mdp" value="" class="form-control" name="mdp" placeholder="Mot de passe" >
                        </div>
                    </div>
                    
                  <button class="btn btn-lg  btn-info btn-block  text-uppercase font-weight-bold mb-2" type="submit">Se connecter</button>
                  <p class="small">Pas encore inscrit ? <a class=" small text-primary" href="<?=URL.'inscription.php'?>">S'inscrire</a></p>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


<?php
require_once('inc/footer.php');

