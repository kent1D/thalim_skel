jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','');
	jQuery('.evenements_lies h2').text('Séance(s) du séminaire');
	jQuery('.formulaire_editer_article #titre').attr('placeholder','');
	jQuery('.formulaire_editer_article .editer_titre label').text('Titre du séminaire');
	jQuery(".formulaire_editer_article .editer_groupe_3").detach().insertBefore('.formulaire_editer_article .editer_titre');
	jQuery(".formulaire_editer_article #diogene_auteurs").detach().insertAfter('.formulaire_editer_article .editer_titre');
	jQuery(".formulaire_editer_article #diogene_auteurs .editer_diogene_gerer_auteurs label").text('Organisateur(s) Thalim');
	jQuery(".formulaire_editer_article .editer_autres_auteurs label").text('Autre(s) organisateur(s)');
	jQuery(".formulaire_editer_article .editer_autres_auteurs").detach().insertAfter('.formulaire_editer_article #diogene_auteurs');
	jQuery(".formulaire_editer_evenement .editer_inscription,.formulaire_editer_evenement .editer_parent,.formulaire_editer_evenement .champs_extras,.formulaire_editer_evenement .editer_repetitions,.formulaire_editer_article .editer_diogene_gerer_auteurs .explication,.formulaire_editer_article .editer_liens_sites legend,.formulaire_editer_article .editer_liens_sites h3,.formulaire_editer_article .editer_nom_site,.editer_langue,.formulaire_editer_article #diogene_auteurs legend,#diogene_auteurs h3").remove();
	jQuery(".formulaire_editer_article .editer_url_site label").text("Lien pour plus d'informations sur le séminaire");
	jQuery(".formulaire_editer_article .editer_texte label").text('Texte de présentation');
	jQuery(".formulaire_editer_article .diogene_mots legend").text('A relier à (pas obligatoire pour tous)');
	jQuery(".agenda_fieldset h3.legend").text('Ajouter une séance au séminaire');
	jQuery(".agenda_fieldset .editer_titre_evenement label").text('Titre de la séance');
	jQuery(".agenda_fieldset .editer_descriptif_evenement label").text('Descriptif de la séance');
});