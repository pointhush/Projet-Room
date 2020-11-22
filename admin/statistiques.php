<?php
require_once('../inc/init.php');
//Titre de la page
$title = 'Statistiques';

// Vérifier que l'on est admin
if(!isAdmin()){
    header('location:'.URL.'connexion.php');
    exit();
}

require_once('inc_admin/header.php');
// Top 5 des salles les mieux notées

$best_salles = execRequete("SELECT ROUND(AVG(note),1) AS moy, titre, s.id_salle, categorie from avis a LEFT JOIN salle s ON a.id_salle=s.id_salle GROUP BY titre ORDER BY moy DESC LIMIT 5");
?>
<div class="ml-sm-5 mr-sm-5 ">
    <h2 class="text-center mb-5 mt-4"><?=$title?></h2>  
    <div class="row">
        <div class="col cadre pt-4 pb-4 p-md-4 ml-lg-5 mr-lg-5">
        <h3 class="mb-4 text-center"> Salles les mieux notées</h3>
        <?php
        // numérotation de 1 à 5
        $i=0;
        while($note = $best_salles->fetch()){
            $larg = (100*$note['moy'])/5 ;
            $i++;
            if($note['categorie'] == 'bureau') {
                $piece = 'Bureau ';
            }else{
                $piece = 'Salle ';
            }
        ?>
            <span><?=$i.' - '.$piece.$note['titre'].' (id-'.$note['id_salle'].')' ?></span>
            <div class="progress">
                <div class="progress-bar bg-warning text-dark font-weight-bold" role="progressbar" style="width:<?=$larg?>%" aria-valuenow="<?=$larg?>" aria-valuemin="0" aria-valuemax="100"><?=$note['moy']?></div>
            </div>
        <?php
        }
        ?>
        </div>
    </div>
    <!-- Salles les plus commandées -->
    <div class="row">
        <div class=" col cadre pt-4 pb-4 p-md-4 ml-lg-5 mr-lg-5">
        <h3 class="mb-4 text-center"> Salles les plus commandées</h3>
        <?php
        $best_salle = execRequete("SELECT count(id_commande) AS nb,titre, s.id_salle, id_commande, categorie FROM salle s LEFT JOIN produit p ON s.id_salle=p.id_salle,commande c WHERE p.id_produit=c.id_produit GROUP BY titre ORDER BY nb DESC LIMIT 5");
       ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col">id_salle</th>
                        <th scope="col">Nombre de commandes</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php
                        // numérotation de 1 à 5
                        $i=0;   
                        while($commande = $best_salle->fetch()){
                            if($commande['categorie'] == 'bureau') {
                                $piece = 'Bureau ';
                            }else{
                                $piece = 'Salle ';
                            }
                            $i++;
                           // if($commande['nb'] != 0) {
                        ?>
                        <tr>
                            <th scope="row"><?=$i?></th>
                            <td class="align-middle"><?=$piece.$commande['titre'] ?></td>
                            <td class="align-middle"><?=$commande['id_salle'] ?></td>
                            <td class="align-middle text center"><span class="font-weight-bold bg-success text-white pl-2 pr-2"><?=$commande['nb']?></span></td>
                        </tr>
                        <?php
                           // }
                        }
                        ?>
                    </tbody>
            </table>
            
        
        </div>
    </div>
    <!-- Membres achetant le plus -->
    <div class="row">
        <div class=" col cadre pt-4 pb-4 p-md-4 ml-lg-5 mr-lg-5">
        <h3 class="mb-4 text-center"> Membres achetant le plus</h3>
        <?php
        $best_membres = execRequete("SELECT count(id_commande) AS nb,titre, s.id_salle, id_commande, m.id_membre, nom, prenom FROM salle s, commande c, produit p, membre m WHERE s.id_salle=p.id_salle AND p.id_produit=c.id_produit AND m.id_membre=c.id_membre group by m.id_membre  order by nb DESC LIMIT 5");
        
        ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col">id_membre</th>
                        <th scope="col">Nb total d'achat</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php
                         // numérotation de 1 à 5
                        $i=0;   
                        while($membres = $best_membres->fetch()){
                            $i++;
                        ?>
                        <tr>
                            <th scope="row"><?=$i?></th>
                            <td class="align-middle"><?=$membres['prenom'].' '.$membres['nom'] ?></td>
                            <td class="align-middle"><?=$membres['id_membre'] ?></td>
                            <td class="align-middle text center"><span class="font-weight-bold bg-primary text-white pl-2 pr-2"><?=$membres['nb']?></span></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
            </table>
        </div>
    </div>
    <!-- Membres achetant le plus cher -->
    <div class="row">
        <div class=" col cadre pt-4 pb-4 p-md-4 ml-lg-5 mr-lg-5">
        <h3 class="mb-4 text-center"> Membres achetant le plus cher</h3>
        <?php
        $best_membre = execRequete("SELECT sum(prix) AS somme, s.id_salle, m.id_membre, nom, prenom FROM salle s, commande c, produit p, membre m WHERE s.id_salle=p.id_salle AND p.id_produit=c.id_produit AND m.id_membre=c.id_membre group by m.id_membre order by somme DESC LIMIT 5");
        ?>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">id_membre</th>
                    <th scope="col">Total en €</th>
                </tr>
            </thead>
                <tbody>
                    <?php
                    // numérotation de 1 à 5
                    $i=0;   
                    while($membre = $best_membre->fetch()){
                        $i++;
                    ?>
                    <tr>
                        <th scope="row"><?=$i?></th>
                        <td class="align-middle"><?=$membre['prenom'].' '.$membre['nom'] ?></td>
                        <td class="align-middle"><?=$membre['id_membre'] ?></td>
                        <td class="align-middle text center"><span class="font-weight-bold bg-info text-white pl-2 pr-2"><?=$membre['somme']?></span></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
        </table>
        </div>
    </div>
</div>
<?php
require_once('inc_admin/footer.php');



