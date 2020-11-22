document.addEventListener('DOMContentLoaded', function(){
    $('#saisie').on('input',recherche);
    
    function recherche(){
        let categories = $('#categ').val();
        let params = $('#form_filtre').serialize();
            $.post('ajax.php',params,function(reponse){
                $('#resultat').html(reponse.resultat);
            }, 'json');//(où je vais, critères?,fonction exploitant la réponse, format JSON)
       
    }






}); // fin du DOM chargé