jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','');
	jQuery(".editer_groupe_3").detach().insertBefore('.editer_titre');
	jQuery('.editer_titre_evenement').hide();
	jQuery("#diogene_auteurs").detach().insertAfter('.editer_titre');
	jQuery('.editer_autres_auteurs label').text('Autres directeurs de Thèse');
	jQuery(".editer_autres_auteurs").detach().insertAfter('#diogene_auteurs');
	jQuery(".editer_titre label").text('Titre');
	jQuery(".editer_soustitre label").text('Autre doctorant THALIM');
	jQuery("#diogene_auteurs .editer_diogene_gerer_auteurs label").text('Directeurs de thèse (Thalim)');
	jQuery(".editer_diogene_gerer_auteurs .explication,.editer_liens_sites legend,.editer_liens_sites h3,.editer_nom_site,.editer_langue,#diogene_auteurs legend,#diogene_auteurs h3").remove();
	jQuery(".editer_texte label").text('Texte de présentation');
	jQuery(".diogene_mots legend").text('A relier à (pas obligatoire pour tous)');
	jQuery("#date_debut").unbind('change').change(function(){
		if(jQuery("#date_fin").val() == ''){
			jQuery("#date_fin").val(jQuery("#date_debut").val());
			jQuery("#heure_fin").val(jQuery("#heure_debut").val());
		}
	});
	if($('#horaire').is(':checked')){
		$('#horaire').attr('checked', false);
		$('.afficher_horaire').show();
	}
	jQuery(".editer_diogene_doctorant").detach().insertAfter('.editer_autres_auteurs');
	jQuery(".editer_garants_thalim").detach().insertAfter('.editer_autres_auteurs');
	jQuery(".editer_garants").detach().insertAfter('.editer_garants_thalim');
	jQuery("em.aide").remove();
	barrebouilles_thalim();
});