<?php
// Fuseau horaire
date_default_timezone_set('Europe/Paris');

// Session
session_start();

//Connexion BDD
$pdo = new PDO(
    'mysql:host=sql24;dbname=diu08731',
    'diu08731',
    'lEG8zCw6yYrw',
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, 
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
    )
);

//Définition de constantes
define('URL','/projet_room/'); 
define('SALT','Mgt4xBam!'); //on definit une variable pour encrypter le mot de passe

// Initialisation de variables
$messages='';


// Inclusion de nos fonctions PHP
require_once('functions.php');

// Pour empecher les espaces 
if(!empty($_POST)) {
    foreach($_POST AS $indice => $valeur) {
        $_POST[$indice] = trim($valeur);
    }
}

?>