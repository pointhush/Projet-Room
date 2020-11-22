<?php

require_once('inc/init.php');
// RENDRE INNACCESSIBLE SANS COMPTE
if( !isConnected() ){
    header('location:' . URL . 'connexion.php');
    exit();
}
$title = 'Profil'; // titre de la page
// REQUETE A LA BASE DE DONNEE
    $membre = execRequete("SELECT * FROM membre m , commande c ,produit p ,salle s WHERE m.id_membre = c.id_membre AND c.id_produit = p.id_produit AND p.id_salle = s.id_salle AND m.id_membre=:id_membre",array('id_membre' => $_SESSION['membre']['id_membre']));
   

require_once('inc/header.php');
if (isset($_SESSION['membre']['id_membre']))
// AFFICHAGE DES INFORMATION DU COMPTE VIS A VIS DU COMPTE CONNECTE
{ ?>
<div class="row justify-content-center my-5 "><div class="col-9 my-3 bg-faded pb-3 ">
<h3>Bienvenue sur votre profil, <?= $_SESSION['membre']['pseudo']?></h3>

<ul class="list-group list-group-flush shadow-sm pl-3 w-75 m-auto">  <li class="list-group-item" style="border:none;"> <strong>Pseudo:</strong>  <?= $_SESSION['membre']['pseudo']?></li>
<li class="list-group-item"><strong>Email: </strong> <?= $_SESSION['membre']['email']?></li>
<li class="list-group-item"><strong>Prénom: </strong> <?= $_SESSION['membre']['prenom']?></li>
<li class="list-group-item"><strong>Nom: </strong> <?= $_SESSION['membre']['nom']?></li>

</ul></div></div>

<div class="row mb-5 justify-content-center">

<?php 
// EN CAS D'ABSENCE DE COMMANDE
if($membre->rowCount()== 0){
    ?>
    <div class=" col-9 alert alert-warning mt-5 text-center">Vous n'avez pas encore passé commande.</div>
    <?php
}else{
 // AFFICHAGE DES COMMANDE ENTETE BOUCLE   ?>
<div class="col-sm-12 text-center table-responsive-sm pb-5 bg-faded"><h3 class="text-center mt-4">Historiques de mes commandes</h3>
       <hr><table class="table table-bordered "><thead class="thead-blue"><tr>
            <th>Id de la commande</th>          
            <th>Id du produit</th>
            <th>Nom de la salle</th>
            <th>Date d'arrivée</th>
            <th>Date de départ</th>
            <th>Prix</th>
            <th>Date de la commande</th>
            <th>Fiche Produit</th>
           
            
        </tr></thead>
        <?php
                    if(isset($membreb['categorie']) && $membre['categorie'] == 'bureau') {
                        $piece = 'Bureau ';
                    }else{
                        $piece = 'Salle ';
                    }
                  
                    while($membreb = $membre->fetch()){
                        $datein=date("d/m/Y", strtotime($membreb['date_arrivee']));
                        $dateout=date("d/m/Y", strtotime($membreb['date_depart']));
                        $dateeng=date("d/m/Y", strtotime($membreb['date_enregistrement']));
                     
                        ?>
                        <tr class="text-center">
                            <td class="align-middle"><?=$membreb['id_commande'] ?></td>
                            <td class="align-middle"><?=$membreb['id_produit'] ?></td>
                            <td class="align-middle"><?=$piece . $membreb['titre'] ?></td>
                            <td class="align-middle star"><?=$datein ?></td>
                            <td class="align-middle"><?=$dateout?></td>
                            <td class="align-middle"><?=$membreb['prix'] ?>€</td>
                            <td class="align-middle"><?=$dateeng?></td>
                            <td class="align-middle"><a href="<?= URL . 'fiche_produit.php?id_produit=' .$membreb['id_produit'] ?>"><i class="fas fa-briefcase"></i></a></td>
                           
                        </tr>
                        <?php    
                    }  
                        ?>  
                      
            </table></div></div>
        <?php
        }
    }
require_once('inc/footer.php'); 

?>