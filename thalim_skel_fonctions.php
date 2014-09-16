<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function thalim_role_chaine($texte,$small=false){
	switch ($texte) {
		case 'cdd_doctorant':
			$texte = ($small ? '' : _T('thalim:info_cdd_doctorant'));
			break;
		case 'charge_recherche':
			$texte = ($small ? 'CR' : _T('thalim:info_charge_recherche'));
			break;
		case 'directeur_recherche':
			$texte = ($small ? 'DR' : _T('thalim:info_directeur_recherche'));
			break;
		case 'maitre_conference':
			$texte = ($small ? 'MCF' : _T('thalim:info_maitre_conference'));
			break;
		case 'post_doctorant':
			$texte = ($small ? '' : _T('thalim:info_post_doctorant'));
			break;
		case 'prof_emerite':
			$texte = ($small ? 'PREM' : _T('thalim:info_prof_emerite'));
			break;
		case 'professeur':
			$texte = ($small ? 'PR' : _T('thalim:info_professeur'));
			break;
		case '10_secretaire_gestionnaire':
			$texte = ($small ? '' : _T('thalim:info_10_secretaire_gestionnaire'));
			break;
		case '20_communication':
			$texte = ($small ? '' : _T('thalim:info_20_communication'));
			break;
		case '30_conception_graphique':
			$texte = ($small ? '' : _T('thalim:info_30_conception_graphique'));
			break;
		default:
			$texte = ((strlen($texte) > 1) ? _T('thalim:info_'.$texte) : '');
			break;
	}
	return $texte;
}

function thalim_agenda_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme=''){
	$abbr = '';
	if (strpos($forme,'abbr')!==false) $abbr = 'abbr';
	$affdate = "affdate_jourcourt";
	if (strpos($forme,'annee')!==false) $affdate = 'affdate';
	
	$dtstart = $dtend = $dtabbr = "";
	if (strpos($forme,'hcal')!==false) {
		$dtstart = "<abbr class='dtstart' title='".date_iso($date_debut)."'>";
		$dtend = "<abbr class='dtend' title='".date_iso($date_fin)."'>";
		$dtabbr = "</abbr>";
	}
	
	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$d = date("Y-m-d", $date_debut);
	$f = date("Y-m-d", $date_fin);
	$h = $horaire=='oui';
	$hd = date("H:i",$date_debut);
	$hf = date("H:i",$date_fin);
	$au = " " . strtolower(_T('agenda:evenement_date_au'));
	$du = _T('agenda:evenement_date_du') . " ";
	$s = "";
	if ($d==$f){ // meme jour
		$s = $affdate($d);
		if ($h)
			$s .= " $hd";
		$s = "$dtstart$s$dtabbr";
		if ($h AND $hd!=$hf) $s .= "-$dtend$hf$dtabbr";
	}
	// meme annee et mois, jours differents
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin)){
		if ($h)
			$chaine = 'thalim:date_fmt_du_au_meme_mois_heure';
		else 
			$chaine = 'thalim:date_fmt_du_au_meme_mois';
		if(date('Y',$date_debut) == date('Y'))
			$chaine .= '_annee_en_cours';
			$s = _T($chaine,array(
								'dtstart'=>$dtstart,
								'dtend'=>$dtend,
								'dtabbr'=>$dtabbr,
								'hd' => $hd,
								'hf' => $hf,
								'jour_debut'=>jour($d),
								'jour_fin'=>jour($f),
								'nom_mois'=>nom_mois($d),
								'annee'=>annee($d)));
	}
	// meme annee, mois et jours differents
	else if ((date("Y",$date_debut))==date("Y",$date_fin)){ 
		if ($h)
			$chaine = 'thalim:date_fmt_du_au_meme_annee_heure';
		else 
			$chaine = 'thalim:date_fmt_du_au_meme_annee';

		if(date('Y',$date_debut) == date('Y'))
			$chaine .= '_annee_en_cours';
		$s = _T($chaine,array(
							'dtstart'=>$dtstart,
							'dtend'=>$dtend,
							'dtabbr'=>$dtabbr,
							'hd' => $hd,
							'hf' => $hf,
							'jour_debut'=>jour($d),
							'jour_fin'=>jour($f),
							'nom_mois_debut'=>nom_mois($d),
							'nom_mois_fin'=>nom_mois($f),
							'annee'=>annee($d)));
	}
	else
	{ // tout different
		$s = $du . $dtstart . affdate($d);
		if ($h)
			$s .= " ".date("(H:i)",$date_debut);
		$s .= $dtabbr . $au . $dtend. affdate($f);
		if ($h)
			$s .= " ".date("(H:i)",$date_fin);
		$s .= $dtabbr;
	}
	return unicode2charset(charset2unicode($s,'AUTO'));	
}

function autoriser_auteur_modifierextra_these_titre_dist($faire, $type, $id, $qui, $opt) {
	if(test_espace_prive() && in_array($qui['statut'], array('0minirezo')))
		return true;
	else
		return sql_getfetsel('id_mot','spip_mots_liens','objet="auteur" AND id_objet='.intval($id).' AND id_mot=1048');
}

function autoriser_auteur_modifierextra_these_directeur_dist($faire, $type, $id, $qui, $opt) {
	if(test_espace_prive() && in_array($qui['statut'], array('0minirezo')))
		return true;
	else
		return sql_getfetsel('id_mot','spip_mots_liens','objet="auteur" AND id_objet='.intval($id).' AND id_mot=1048');
}

function autoriser_auteur_modifierextra_these_resume_dist($faire, $type, $id, $qui, $opt) {
	if(test_espace_prive() && in_array($qui['statut'], array('0minirezo')))
		return true;
	else
		return sql_getfetsel('id_mot','spip_mots_liens','objet="auteur" AND id_objet='.intval($id).' AND id_mot=1048');
}

function autoriser_auteur_modifierextra_domaines_recherche_dist($faire, $type, $id, $qui, $opt) {
	if(test_espace_prive() && in_array($qui['statut'], array('0minirezo')))
		return true;
	else
		return !sql_getfetsel('id_mot','spip_mots_liens','objet="auteur" AND id_objet='.intval($id).' AND id_mot=1048');
}

function autoriser_auteur_modifierextra_publications_dist($faire, $type, $id, $qui, $opt) {
	if(test_espace_prive() && in_array($qui['statut'], array('0minirezo')))
		return true;
	else
		return !sql_getfetsel('id_mot','spip_mots_liens','objet="auteur" AND id_objet='.intval($id).' AND id_mot=1048');
}

function autoriser_auteur_modifierextra_hal($faire, $type, $id, $qui, $opt) {
	spip_log('on passe ici','test.'._LOG_ERREUR);
	if(test_espace_prive() && in_array($qui['statut'], array('0minirezo')))
		return true;
	else
		return !sql_getfetsel('id_mot','spip_mots_liens','objet="auteur" AND id_objet='.intval($id).' AND id_mot=1048');
}

function autoriser_auteur_modifierextra_role_dist($faire, $type, $id, $qui, $opt) {
	return test_espace_prive() && in_array($qui['statut'], array('0minirezo'));
}

function autoriser_auteur_modifierextra_membre_qualite_dist($faire, $type, $id, $qui, $opt) {
	return test_espace_prive() && in_array($qui['statut'], array('0minirezo'));
}

function autoriser_auteur_modifierextra_recherches_en_cours_dist($faire, $type, $id, $qui, $opt) {
	return test_espace_prive() && in_array($qui['statut'], array('0minirezo'));
}

function autoriser_auteur_modifierextra_fonction_complement_dist($faire, $type, $id, $qui, $opt) {
	return test_espace_prive() && in_array($qui['statut'], array('0minirezo'));
}

function autoriser_auteur_modifierextra_id_hal_dist() {
	return false;
}
?>