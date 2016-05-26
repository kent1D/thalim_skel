jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','').addClass('inserer_barre_simple');;
	jQuery(".editer_groupe_3").detach().insertBefore('.editer_titre');
	jQuery('.editer_titre_evenement').hide();
	jQuery("#diogene_auteurs").detach().insertAfter('.editer_titre');
	jQuery("#diogene_auteurs .editer_diogene_gerer_auteurs label").text('Organisateur(s) Thalim');
	jQuery("#diogene_auteurs .editer_diogene_gerer_auteurs label[for='diogene_gerer_auteurs2'").text('Organisateur(s) Thalim Associé(s)');
	jQuery(".editer_autres_auteurs label").text('Autre(s) organisateur(s)');
	jQuery(".editer_autres_auteurs").detach().insertAfter('#diogene_auteurs');
	jQuery(".editer_diogene_gerer_auteurs .explication,.editer_liens_sites legend,.editer_liens_sites h3,.editer_nom_site,.editer_langue,#diogene_auteurs legend,#diogene_auteurs h3").remove();
	jQuery(".editer_url_site label").text('Lien sur une page de site (en savoir plus)');
	jQuery(".editer_texte label").text('Texte de présentation');
	jQuery(".diogene_mots legend").text('A relier à (pas obligatoire pour tous)');
	jQuery(".saisie_programme_recherche").detach().insertAfter('.diogene_mots ul:last-child');
	jQuery("#date_debut").unbind('change').change(function(){
		if(jQuery("#date_fin").val() == ''){
			jQuery("#date_fin").val(jQuery("#date_debut").val());
			jQuery("#heure_fin").val(jQuery("#heure_debut").val());
		}
	});
	jQuery("em.aide").remove();
	barrebouilles_thalim();
});