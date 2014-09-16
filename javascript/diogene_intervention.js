jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','');
	jQuery(".editer_groupe_3").detach().insertBefore('.editer_titre');
	jQuery('.editer_titre_evenement').hide();
	jQuery("#diogene_auteurs").detach().insertAfter('.editer_soustitre');
	jQuery(".editer_titre label").text('Titre à afficher');
	jQuery(".editer_soustitre label").text('Media (Nom de la radio, etc)');
	jQuery("#diogene_auteurs .editer_diogene_gerer_auteurs label").text('Intervenant(s) Thalim');
	jQuery(".editer_diogene_gerer_auteurs .explication,.editer_liens_sites legend,.editer_liens_sites h3,.editer_nom_site,.editer_langue,#diogene_auteurs legend,#diogene_auteurs h3").remove();
	jQuery(".editer_url_site label").text('Lien sur une page de site (en savoir plus)');
	jQuery(".editer_texte label").text('Texte de présentation');
	jQuery(".diogene_mots legend").text('A relier à (pas obligatoire pour tous)');
	if($('#horaire').is(':checked')){
		$('#horaire').attr('checked', false);
		$('.afficher_horaire').show();
	}
});