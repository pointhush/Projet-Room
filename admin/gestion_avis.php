<?php
require_once('../inc/init.php');
//Titre de la page
$title = 'Gestion des avis';

// Vérifier que l'on est admin
if(!isAdmin()){
    header('location:'.URL.'connexion.php');
    exit();
}
// Suppression d'un avis
if(isset($_GET['action']) && $_GET['action']== 'delete' && !empty($_GET['id_avis'])){
    
    $resultat = execRequete("SELECT * FROM avis WHERE id_avis=:id_avis", array('id_avis' =>$_GET['id_avis']));
    if($resultat->rowCount()== 1){
        $avis= $resultat->fetch();
        execRequete("DELETE FROM avis WHERE id_avis=:id_avis", array('id_avis' =>$_GET['id_avis']));
        header('location:'.URL.'admin/gestion_avis.php');
        exit();
    }
} 


require_once('inc_admin/header.php'); 
echo $messages;


?>
<h2 class="text-center mb-5 mt-4"><?=$title?></h2>
<?php
    //5. Affichage des avis
    $resultat = execRequete("SELECT *, DATE_FORMAT(a.date_enregistrement, '%d/%m/%Y %T') AS date_enr FROM avis a LEFT JOIN salle s ON a.id_salle=s.id_salle LEFT JOIN membre m ON m.id_membre=a.id_membre");

    if($resultat->rowCount()== 0){
        ?>
        <div class="alert alert-warning">Il n'y a pas encore de avis enregistrés</div>
        <?php
    }else{
        ?>
        <p class="ml-2 pl-2 pt-4 font-weight-bold">Il y a <?= $resultat->rowCount()?> avis dans ROOM</p>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <tr class="bg-dark text-white ">
                    <th class="align-middle">id_avis</th>
                    <th class="align-middle">id_membre</th>
                    <th class="align-middle">id_salle</th>
                    <th class="align-middle">Commentaires</th>
                    <th class="align-middle">Note</th>
                    <th class="align-middle">Date d'enregistrement</th>
                    <th colspan="3" class="align-middle">Actions</th>
                </tr>
                <?php
                    if(isset($commande['categorie']) && $commande['categorie'] == 'bureau') {
                        $piece = 'Bureau ';
                    }else{
                        $piece = 'Salle ';
                    }
                    while($avis= $resultat->fetch()){
                       if($avis["id_membre"]== ""){
                           $id_memb = 'Inconnu';
                       }else{
                        $id_memb = $avis["id_membre"].' - '.$avis['email'];
                       }
                     
                        ?>
                        <tr class="text-center">
                            <td class="align-middle"><?=$avis['id_avis'] ?></td>
                            <td class="align-middle"><?= $id_memb ?></td>
                            <td class="align-middle"><?=$avis["id_salle"].' - '.$piece.$avis['titre'] ?></td>
                            <td class="align-middle"><?= $avis['commentaire']?></td>
                            <!--  AVIS -->
                            <td class="align-middle star"><?=avis($avis['note'])?></td>
                            <td class="align-middle"><?=$avis['date_enr'] ?></td>
                            <td class="align-middle"><a class="confsup_avis" href="?action=delete&id_avis=<?=$avis['id_avis']?>"><i class="fas fa-trash-alt"></i></a></td>
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

require_once('inc_admin/footer.php'); 



