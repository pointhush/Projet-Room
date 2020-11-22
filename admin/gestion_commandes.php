<?php
require_once('../inc/init.php');
//Titre de la page
$title = 'Gestion des Commandes';

// Vérifier que l'on est admin
if(!isAdmin()){
    header('location:'.URL.'connexion.php');
    exit();
}
// Suppression d'une commande
if(isset($_GET['action']) && $_GET['action']== 'delete' && !empty($_GET['id_commande'])){
    
    $resultat = execRequete("SELECT * FROM commande WHERE id_commande=:id_commande", array('id_commande' =>$_GET['id_commande']));
    if($resultat->rowCount()== 1){
        $commande= $resultat->fetch();
        execRequete("DELETE FROM commande WHERE id_commande=:id_commande", array('id_commande' =>$_GET['id_commande']));
       
    }
} 


require_once('inc_admin/header.php'); 
echo $messages;

    // Affichage des commandes
    $resultat = execRequete("SELECT *, DATE_FORMAT(c.date_enregistrement, '%d/%m/%Y %T') AS date_enr, DATE_FORMAT(date_arrivee, '%d/%m/%Y') AS date_arr, DATE_FORMAT(date_depart, '%d/%m/%Y') AS date_dep FROM commande c, produit p, membre m, salle s
    WHERE m.id_membre=c.id_membre
    AND p.id_produit=c.id_produit
    AND s.id_salle=p.id_salle");

    if($resultat->rowCount()== 0){
        ?>
        <div class="alert alert-warning">Il n'y a pas encore de commandes enregistrés</div>
        <?php
    }else{
        ?>
        <h2 class="text-center mb-3 mt-4"><?=$title?></h2>
        <p class="ml-2 pl-2 pt-4 font-weight-bold">Il y a <?= $resultat->rowCount()?> commande(s) dans ROOM</p>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <tr class="bg-dark text-white">
                    <th class="align-middle">Id_commande</th>
                    <th class="align-middle">Id membre</th>
                    <th class="align-middle">Id produit</th>
                    <th class="align-middle">Prix</th>
                    <th class="align-middle">Date d'enregistrement</th>
                    <th colspan="2" class="align-middle">Actions</th>
                </tr>
                <?php
                    if(isset($commande['categorie']) && $commande['categorie'] == 'bureau') {
                        $piece = 'Bureau ';
                    }else{
                        $piece = 'Salle ';
                    }

                    while($commande= $resultat->fetch()){ 
                       ?>
                        <tr class="text-center">
                            <td class="align-middle"><?=$commande['id_commande'] ?></td>
                            <td class="align-middle"><?=$commande['id_membre']." - ".$commande['email'] ?></td>
                            <td class="align-middle"><?=$commande['id_produit']." - ".$piece.' '. $commande['titre']."<br>". $commande['date_arr']." au ".$commande['date_dep'] ?></td>
                            <td class="align-middle"><?=$commande['prix']?></td>
                            <td class="align-middle"><?=$commande['date_enr'] ?></td>
                            <td class="align-middle"><a class="confsup_commande" href="?action=delete&id_commande=<?=$commande['id_commande']?>"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <?php    
                    }  
                        ?>  
            </table>
        </div>
       <?php 
    }

require_once('inc_admin/footer.php'); 

