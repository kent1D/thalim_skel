jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','').addClass('inserer_barre_simple');;
	jQuery("#diogene_auteurs").detach().insertAfter('.editer_titre');
	jQuery("#diogene_auteurs label").text('Auteurs/organisateurs');
	jQuery("#diogene_auteurs .editer_diogene_gerer_auteurs label[for='diogene_gerer_auteurs2'").text('Auteurs/organisateurs Associés');
	jQuery(".editer_autres_auteurs label").text('Autre(s) auteur(s)/organisateur(s)');
	jQuery(".editer_autres_auteurs").detach().insertAfter('#diogene_auteurs');
	jQuery(".editer_url_site label").text("Lien pour plus d'informations sur l'appel");
	jQuery(".editer_date_orig label").text("Date limite d'envoi des propositions");
	jQuery(".editer_date_redac_orig label").text("Date du début de l'appel");
	jQuery(".editer_date_redac_orig").detach().insertBefore('.editer_date_orig');
	jQuery(".editer_diogene_gerer_auteurs .explication,.editer_liens_sites legend,.editer_liens_sites h3,.editer_nom_site,.editer_langue,#diogene_auteurs legend,#diogene_auteurs h3").remove();
	jQuery(".saisie_programme_recherche").detach().insertAfter('.diogene_mots ul:last-child');
	jQuery("em.aide").remove();
	jQuery("#date_debut").unbind('change').change(function(){
		if(jQuery("#date_fin").val() == ''){
			jQuery("#date_fin").val(jQuery("#date_debut").val());
			jQuery("#heure_fin").val(jQuery("#heure_debut").val());
		}
	});
	barrebouilles_thalim();
});