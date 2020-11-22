$(document).ready(function(){
    
    $('#photo').change(function(e){
        
        $fic = $(this);
        //$('#nomfichier').html(e.target.files[0].name);
        var reader = new FileReader();
        reader.onload = function(e){
            $('#box').html('<img src="' + e.target.result +'" class="img-fluid">');
        }
        reader.readAsDataURL(e.target.files[0]);
    });
    // Confirmation supression produit
    $('.confsup_produit').on('click',function(){
    return(confirm('Etes vous certain(e) de vouloir supprimer ce produit ?'))
    });
    //Confirmation supression membre
    $('.confsup_membre').on('click',function(){
        return(confirm('Etes vous certain(e) de vouloir supprimer ce membre ?'))
    });
    //Confirmation supression avis
    $('.confsup_avis').on('click',function(){
        return(confirm('Etes vous certain(e) de vouloir supprimer cet avis ?'))
    }); 
    //Confirmation supression commande
    $('.confsup_commande').on('click',function(){
        return(confirm('Etes vous certain(e) de vouloir supprimer cette commande ?'))
    });
    //Confirmation supression salle
    $('.confsup_salle').on('click',function(){
        return(confirm('Etes vous certain(e) de vouloir supprimer cette salle ?'))
    });   

    // Datepicker format français
    jQuery(function($) {
    $.datepicker.regional['fr'] = {
        closeText: 'Fermer',
        prevText: 'Précédent',
        nextText: 'Suivant',
        currentText: 'Aujourd\'hui',
        monthNames: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
            'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
        ],
        monthNamesShort: ['janv.', 'févr.', 'mars', 'avril', 'mai', 'juin',
            'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'
        ],
        dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
        dayNamesShort: ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        weekHeader: 'Sem.',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['fr']);
});

// datepicker


    $( "#date_arrivee" ).datepicker({
        minDate: 0, // pour empécher de choisir un jour antérieur à la date du jour
        
        // Pour désactiver sur le champs date_depart les jours antérieurs au choix sur date_arrivee
        onSelect: function (date) { 
            var date1 = $('#date_arrivee').datepicker('getDate');           
            var date = new Date( Date.parse( date1 ) ); 
            date.setDate( date.getDate() + 1 );        
            var newDate = date.toDateString(); 
            newDate = new Date( Date.parse( newDate ) );                      
            $('#date_depart').datepicker("option","minDate",newDate);   
        }
    });


    $( "#date_depart" ).datepicker();


}); //fin document ready