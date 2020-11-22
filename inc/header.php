
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Room | <?= $title ?></title>
    <meta name="description" content=" Réservation professionnelle de salle et de bureau sur Paris, Lyon et Marseille. Adoptez le réflexe ROOM, économisez votre espace!" " />
    <link rel="stylesheet" href="<?= URL . 'inc/css/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?= URL . 'inc/css/bootstrap-grid.min' ?>">
    <link rel="stylesheet" href="<?= URL . 'inc/css/bootstrap-reboot.min.css' ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= URL .'inc/js/lib/jquery-ui.min.css' ?>">
    <link rel="stylesheet" href="<?= URL . 'inc/css/style.css' ?>">
    <link href="https://fonts.googleapis.com/css?family=Rochester&display=swap" rel="stylesheet"> 

    <script src="<?=URL.'admin/backoffice/vendor/jquery/jquery.min.js'?>"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <script src="<?= URL . 'inc/js/lib/jquery-ui.min.js' ?>"></script>
    <script src="<?= URL . 'inc/js/functions.js' ?>"></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-info">
            <a class="" href="<?= URL ?>"> 
            <object class="d-inline-block align-top" type="image/svg+xml" data="<?= URL . 'images/logo.svg' ?>" width="40" height="40" >
    Room
</object> <h1 class="d-inline"> ROOM</h1></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto ">
                <li class="nav-item <?= ( $title == 'Qui Sommes nous ?') ? 'active': '' ?>">
                        <a class="nav-link" href="<?= URL . 'quisommesnous.php' ?>">Qui sommes Nous ?</a>
                        </li>
                        <li class="nav-item <?= ( $title == 'Contact') ? 'active': '' ?>">
                        <a class="nav-link" href="<?= URL . 'contact.php' ?>">Contact</a>
                        </li>
						
						</ul><ul class="navbar-nav ml-auto nav-flex-icons">
                        <li class="nav-item dropdown my-2 my-lg-0">
          <a class="nav-link nav-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-user-circle"></i>  Espace Membre
        </a>
          <!-- Here's the magic. Add the .animate and .slide-in classes to your .dropdown-menu and you're all set! -->
          <div class="dropdown-menu dropdown-menu-right animate slideIn navbar-dark bg-info p-3" aria-labelledby="navbarDropdown">
          <?php
                    if( isConnected() ) :
                ?>
                        <a class=" nav-link <?= ( $title == 'Mon Compte') ? 'active': '' ?>" href="<?= URL . 'moncompte.php' ?>" style="white-space:nowrap;"><i class="fas fa-id-badge" style="padding:5px;"></i>Mon Compte</a>
                        
                        <?php
                    else:
                        ?>
                        
                        <a class=" nav-link <?= ( $title == 'Inscription') ? 'active': '' ?>" href="<?= URL . 'inscription.php' ?>" style="white-space:nowrap;"> <i class="fas fa-clipboard-list"style="padding:5px;"></i>Inscription</a>
                        <a class=" nav-link <?= ( $title == 'Connexion') ? 'active': '' ?>" href="<?= URL . 'connexion.php' ?>" style="white-space:nowrap;"> <i class="fas fa-door-open" style="padding:5px;"></i>Connexion</a>

                        <?php
                    endif;
                    if (isAdmin()): ?>
                <div class="dropdown-divider"></div>
                <a class=" nav-link" style="white-space:nowrap;" href="<?= URL . 'admin/backoffice/index.php' ?>"> <i class="fas fa-tools" style="padding:5px;"></i>Administration</a>
                       <?php
                       
                       endif;
                       if( isConnected() ) :?>
                       <div class="dropdown-divider"></div>
                       <a class=" nav-link" href="<?= URL . 'connexion.php?action=deconnexion' ?>" style="white-space:nowrap;"><i class="fas fa-door-closed" style="padding:5px;"></i> Deconnecter</a>
                       <?php
                    endif;
                   ?>
                    </div>
        
                
            </li>
            </ul>
        </nav>
    </header>
    <main class="container flex-shrink-0">