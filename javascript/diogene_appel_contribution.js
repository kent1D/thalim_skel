jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','');
	jQuery("#diogene_auteurs").detach().insertAfter('.editer_titre');
	jQuery("#diogene_auteurs label").text('Auteurs/organisateurs');
	jQuery(".editer_autres_auteurs label").text('Autre(s) auteur(s)/organisateur(s)');
	jQuery(".editer_autres_auteurs").detach().insertAfter('#diogene_auteurs');
	jQuery(".editer_url_site label").text("Lien pour plus d'informations sur l'appel");
	jQuery(".editer_date_orig label").text("Date limite d'envoi des propositions");
	jQuery(".editer_date_redac_orig label").text("Date du début de l'appel");
	jQuery(".editer_diogene_gerer_auteurs .explication,.editer_liens_sites legend,.editer_liens_sites h3,.editer_nom_site,.editer_langue,#diogene_auteurs legend,#diogene_auteurs h3").remove();
});