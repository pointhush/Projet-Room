<?php
require_once('inc/init.php');
//Titre de la page
$title = 'Contact';





require_once('inc/header.php');


$message='';
if(!empty($_POST))
{

// entête email
$headers = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=ISO-8859-1'."\n";
$headers .= 'Reply-To: ' . $_POST['expediteur'] . "\n";
$headers .= 'From: "' . ucfirst(substr($_POST['expediteur'], 0, strpos($_POST['expediteur'], '@'))) . '"<'.$_POST['expediteur'].'>' . "\n";
$headers .= 'Delivered-to: monadresse@gmail.com' . "\n";
$message = "Nom : " . $_POST['nom'] . "\nPrénom : " . $_POST['prenom'] . "\nSociété : " . $_POST['societe'] . "\nMessage : " .
$_POST['message'];
mail("aaurelia.aymard@gmail.com", $_POST['sujet'], $message, $headers);
$message="<div class=\"alert alert-success\" role=\"alert\">
Votre message a été envoyé!
</div>";
}
?> <div class="row justify-content-center ">
    <div class="col-9 bg-faded">
    <?=$message ?>
    <h2> Nous contactez !</h2>
    <p> Un soucis avec votre commande ? Une question ? Une interrogation ? Envoyez nous un message ! </p>
<form method="post" action="">
<div class="form-row">
    <div class="form-group col-md-6">
<label for="nom">Nom</label><br>
<input name="nom" id="nom" placeholder="Votre Nom" type="text" class="form-control" required></div>
    <div class="form-group col-md-6">
<label for="prenom">Prenom</label><br>
<input name="prenom" id="prenom" placeholder="Votre Prénom" type="text" class="form-control" required></div></div>
<div class="form-row">
    <div class="form-group col-md-12">
<label for="societe">Société</label><br>
<input name="societe" id="societe" placeholder="Votre Sociéte" type="text" class="form-control" required></div></div>
<div class="form-row">
    <div class="form-group col-md-6">
<label for="expediteur">Expediteur</label><br>
<input type="text" name="expediteur" id="expediteur" placeholder="Adresse email où l'on peut vous contacter" class="form-control" required></div>
    <div class="form-group col-md-6">
<label for="sujet">Sujet</label><br>
<input type="text" name="sujet" id="sujet" placeholder="Pensez à mentionner le numéro de la commande s'il y en a un." class="form-control" required></div></div>
<div class="form-row">
    <div class="form-group col-md-12">
<label for="message">Message</label><br>
<textarea name="message" placeholder="Détaillez ici l'objet de votre demande. Plus celui ci sera précis, plus notre réponse sera rapide ! " class="form-control" required></textarea></div></div>
<div class="form-row">
    <div class="form-group col-md-9">
<input type="submit" value="C'est parti !" class="btn btn-info"></div></div>
</div>
</div>
<?php
require_once('inc/footer.php');
