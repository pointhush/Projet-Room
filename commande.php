<?php

require_once('inc/init.php');

$title = 'Commandes'; // titre de la page
require_once('inc/header.php');
$message='';


    // controles
    $feu_rouge = false;
   
    $resultat = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit",array('id_produit'=>$_POST['id_produit']));
        $produit = $resultat->fetch();

        // PERMET DE NE PAS POUVOIR ACCEDER A LA PAGE SI ON EST PAS CONNECTE, MEME VIA URL. 
if(!isConnected()){
    if( !isConnected() ){
        header('location:' . URL . 'connexion.php');
        exit();
    }
}
        if($produit['etat'] =='libre'){
            $feu_rouge = true;
            $message = '<div class="alert alert-success" role="alert">
  Votre réservation a bien été enregitrée. Rendez vous sur <a href="'. URL. 'moncompte.php"> <strong>votre compte</strong></a> pour la consulter.
</div>';
// alimentation des tables commandes et details_commandes
    // 1. Générer un numéro de commande
    $id_membre = $_SESSION['membre']['id_membre'];
    $id_produit = $_POST['id_produit'];

    execRequete("INSERT INTO commande VALUES (NULL,:id_membre,:id_produit,NOW())",array(
        'id_membre' => $id_membre ,
        'id_produit' => $id_produit
    ));
    $id_commande = $pdo->lastInsertId();
        // MAJ du stock
        execRequete("UPDATE produit SET etat ='reservation' WHERE id_produit=:id_produit",array(
            
            'id_produit' => $id_produit
         ));
        }else {
            $feu_rouge= false;
           $message = '<div class="alert alert-warning" role="alert">
  Désolé. Ce produit a déjà été réservé. Vous pouvez cependant consultez <a href="'. URL. '"> <strong> nos autres offres</strong></a> ou nous contacter.
</div>';

        }
    
        
 
        
        
        
        ?>
       <div class="row justify-content-center my-2 text-center"><div class="col-10"><?= $message ?></div></div>
        <div class="row justify-content-center text-center my-5"><div class="col-5 "><img src="images/pub1.png" alt="publicite-1" class="img-fluid"></div><div class="col-5"><img src="images/pub2.png" alt="publicite-2" class="img-fluid"></div></div>
        <div class=" text-center m-5"> <a href="<?= URL;?>"> Retour vers le catalogue</a></div>
        <?php
require_once('inc/footer.php');
?>