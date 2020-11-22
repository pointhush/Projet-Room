<?php

// Identifier un membre connecté
function isConnected(){
    if( isset($_SESSION['membre']) ){
        return true;
    }
    else{
        return false;
    }
}
// Identifier un admin
function isAdmin(){
    if( isConnected() && $_SESSION['membre']['statut'] == 1){
        return true;
    }
    else{
        return false;
    }
}

// Fonction qui execute une requête (nettoyage inclus)
function execRequete($req,$params=array()){

    // Assainissement
    if( !empty($params) ){
        foreach($params as $key => $value){
            $params[$key] = htmlspecialchars($value,ENT_QUOTES);
        }
    }

    // globalisation de $pdo
    global $pdo;

    $r = $pdo->prepare($req);
    $r->execute($params);
    return $r;
}


// Fonctions liées au panier
// création du panier
function createPanier(){
    if(!isset($_SESSION['panier'])){
        $_SESSION['panier'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['prix'] = array();
    }
}

// ajout d'un produit au panier
function ajoutPanier($id_produit,$quantite,$prix){
    createPanier();
    $position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']);
    if( $position_produit === false ){
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
    }else{
        $_SESSION['panier']['quantite'][$position_produit] += $quantite;
    }
}

// retirer une ligne du panier
function retraitPanier($id_produit){
    $position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']);
    if( $position_produit !== false ){
        array_splice($_SESSION['panier']['id_produit'],$position_produit,1);
        array_splice($_SESSION['panier']['quantite'],$position_produit,1);
        array_splice($_SESSION['panier']['prix'],$position_produit,1);
    }
}

// montant total d'un panier
function montantPanier(){
    $total = 0;
    if(isset($_SESSION['panier'])){
        for($i=0; $i<count($_SESSION['panier']['id_produit']) ; $i++){
            $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
        }
    }
    return $total;
}

// Nb d'articles dans le panier
function nbArticles(){
    $nb='';
    if( isset($_SESSION['panier']['id_produit']) ){
        $nb = array_sum($_SESSION['panier']['quantite']);
        if( $nb != 0 ){
            $nb = '<span class="badge badge-primary nba">' . $nb . '</span> ';
        }
        else { $nb = ''; }
    }
    return $nb;
}

// Afficher la note en étoile
function avis($note){
     
    $resultat = execRequete("SELECT *, DATE_FORMAT(a.date_enregistrement, '%d/%m/%Y %T') AS date_enr FROM avis a LEFT JOIN salle s ON a.id_salle=s.id_salle LEFT JOIN membre m ON m.id_membre=a.id_membre");
     
        $avis= $resultat->fetch();
        
         ?>
         
                 <?php
                     for($i=1;$i<=$note;$i++){
                         ?>
                         <i class="fas fa-star checked"></i>
                         <?php
                     }
                     for($i=1;$i<=(5 - $note);$i++){
                         ?>
                         <i class="fas fa-star "></i>
                         <?php
                     }
                 ?>
           
             
         <?php    
      
        
}
// ajoute la date d'aujourd'hui
$today = date('Y-m-d');

