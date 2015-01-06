jQuery(document).ready(function(){
	jQuery('#titre').attr('placeholder','').addClass('inserer_barre_simple');;
	jQuery(".editer_groupe_11").detach().insertBefore('.editer_surtitre');
	jQuery(".editer_surtitre label").text('Titre de l’article ou du chapitre');
	jQuery(".editer_titre label").text('Titre de l’ouvrage ou de la revue');
	jQuery("#diogene_auteurs").detach().insertAfter('.editer_titre');
	jQuery("#diogene_auteurs .editer_diogene_gerer_auteurs label").text('Auteur(s) Thalim');
	jQuery(".editer_editeur,.editer_autres_auteurs").detach().insertAfter('#diogene_auteurs');
	jQuery(".editer_direction_ouvrage").detach().insertAfter('.editer_autres_auteurs');
	jQuery(".editer_diogene_gerer_auteurs .explication,.editer_liens_sites legend,.editer_liens_sites h3,.editer_nom_site,.editer_langue,#diogene_auteurs legend,#diogene_auteurs h3").remove();
	jQuery(".editer_url_site label").text('Lien sur une page de site (en savoir plus)');
	jQuery(".editer_date_orig").detach().insertBefore('.editer_texte');
	jQuery(".editer_texte label").text('Texte de présentation');
	jQuery(".editer_date_orig label").text("Date de publication de l'ouvrage ou de l'article");
	jQuery(".diogene_mots legend").text('A relier à (pas obligatoire pour tous)');
	jQuery("em.aide").remove();
	barrebouilles_thalim();
});