<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

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
?>