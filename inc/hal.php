<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// prend un json issu de la recherche de HAL et retourne un tableau des documents lus,
// et false en cas d'erreur
function analyser_publications($json, $url_syndic='') {
	$json = json_decode($json,true);
	$json = pipeline('pre_syndication_publications', $json);
	$publications  = false;
	if(isset($json['response']) && isset($json['response']['docs']) && is_array($json['response']['docs'])){
		/**
		 * Correspondance des items du json par rapport aux champs de la base de donnée
		 * Correspondance plusieurs à un pour 'citation_reference'.
		 * Champs supplémentaires :
		 * degreeType
		 * director
		 * thesisSchool
		 * defenseDate_tdate
		 * scientificEditor
		 * bookTitle
		 * journalName
		 * conferenceEndDate_tdate
		 * conferenceOrganizer
		 * conferenceStartDate_tdate
		 * conferenceTitle
		 * credit
		 * serie
		 * bookCollection
		 * volume
		 * issue
		 * country
		 * city
		 * publisher
		 * journalPublisher
		 * producedDate (publication)
		 * page
		 * uri (url)
		 * isbn
		 * journalIssn
		 * 
		 */
		$champs = array(
			'abstract_s' => 'resume',
			'authFullName_s' => 'lesauteurs',
			'bookCollection_s' => 'citation_reference',
			'bookTitle_s' => 'livre',
			'citationFull_s' => 'citation_complete',
			'citationRef_s' => 'citation_reference',
			'city_s' => 'citation_reference',
			'comment_s' => 'commentaire',
			'conferenceEndDate_tdate' => 'citation_reference',
			'conferenceOrganizer_s' => 'citation_reference',
			'conferenceStartDate_tdate' => 'citation_reference',
			'conferenceTitle_s' => 'citation_reference',
			'country_t' => 'citation_reference',
			'credit_s' => 'citation_reference',
			'defenseDate_tdate' => 'citation_reference',
			'degreeType_s' => 'citation_reference',
			'director_s' => 'citation_reference',
			'docid' => 'docid',
			'docType_s' => 'typdoc',
			'hal_id' => 'identifiant',
			'isbn_s' => 'isbn',
			'issue_s' => 'issue',
			'journalDate_s' => 'date_revue',
			'journalId_i' => 'revue_id',
			'journalIssn_s' => 'isbn',
			'journalPublisher_s' => 'citation_reference',
			'journalTitle_s' => 'revue',
			'language_s' => 'lang',
			'modifiedDate_s' => 'date_modif',
			'page_s' => 'page',
			'producedDate_s' => 'date_production',
			'publisher_s' => 'editeur',
			'publisherLink_s' => 'citation_reference',
			'scientificEditor_s' => 'citation_reference',
			'serie_s' => 'citation_reference',
			'submittedDate_s' => 'date_soumission',
			'subTitle_s' => 'soustitre',
			'thesisSchool_s' => 'citation_reference',
			'title_s' => 'titre',
			'uri_s' => 'url_publication',
			'volume_s' => 'citation_reference',
			'writingDate_s' => 'date_ecriture');
		$publications = array();
		$count = 0;
		foreach($json['response']['docs'] as $id => $contenu_publication){
			$count++;
			$infos_publication = array();
			$affiche = false;
			$docType = (isset($contenu_publication['docType_s']) && $contenu_publication['docType_s'] != "")? $contenu_publication['docType_s'] : "";
			$year_sub = "0000";
			$month_sub = "01";
			$day_sub = "01";
			if (isset($contenu_publication['submittedDate_s']) && preg_match('/^(\d{4})-(\d{2})-(\d{2})/',$contenu_publication['submittedDate_s'],$date_sub_match)) {
				if (isset($date_sub_match[1]))
					$year_sub = $date_sub_match[1];
				if (isset($date_sub_match[2]))
					$month_sub = $date_sub_match[2];
				if (isset($date_sub_match[3]))
					$day_sub = $date_sub_match[3];
			}

			foreach($champs as $champ => $base){
				if(isset($contenu_publication[$champ]) && (is_array($contenu_publication[$champ]) OR (strlen($contenu_publication[$champ]) > 0))){
					switch ($champ) {
						case 'publisher_s':
							$infos_publication[$base] = implode(', ',$contenu_publication[$champ]);
							break;
						case 'producedDate_s':
						case 'writingDate_s':
							if(strlen($contenu_publication[$champ]) == 4 && preg_match('/\d{4}/',$contenu_publication[$champ],$date_publi_match)){
								$infos_publication[$base] = $contenu_publication[$champ].'-01-01 00:00:00';
								if (isset($date_publi_match[0]) && $date_publi_match[0] == $year_sub) {
									$infos_publication[$base] = $contenu_publication[$champ].'-'.$month_sub.'-'.$day_sub.' 00:00:00';
								}
								$infos_publication['date_production_format'] = 'annee';
							}else if(strlen($contenu_publication[$champ]) == 7 && preg_match('/(\d{4})-\d{2}/',$contenu_publication[$champ],$date_publi_match)){
								$infos_publication[$base] = $contenu_publication[$champ].'-01 00:00:00';
								if (isset($date_publi_match[0]) && $date_publi_match[1] == $year_sub) {
									$infos_publication[$base] = $contenu_publication[$champ].'-'.$day_sub.' 00:00:00';
								}
								$infos_publication['date_production_format'] = 'mois';
							}else if(strlen($contenu_publication[$champ]) == 10 && preg_match('/\d{4}-\d{2}-\d{2}/',$contenu_publication[$champ])){
								$infos_publication[$base] = $contenu_publication[$champ].' 00:00:00';
								$infos_publication['date_production_format'] = 'jour';
							}else if(strlen($contenu_publication[$champ]) == 19){
								$infos_publication[$base] = $contenu_publication[$champ];
								$infos_publication['date_production_format'] = 'complet';
							}
							break;
						case 'journalDate_s':
						case 'submittedDate_s':
						case 'modifiedDate_s':
							if(strlen($contenu_publication[$champ]) == 4 && preg_match('/\d{4}/',$contenu_publication[$champ])){
								$infos_publication[$base] = $contenu_publication[$champ].'-01-01 00:00:00';								
							}else if(strlen($contenu_publication[$champ]) == 7 && preg_match('/\d{4}-\d{2}/',$contenu_publication[$champ])){
								$infos_publication[$base] = $contenu_publication[$champ].'-01 00:00:00';
							}else if(strlen($contenu_publication[$champ]) == 10 && preg_match('/\d{4}-\d{2}-\d{2}/',$contenu_publication[$champ])){
								$infos_publication[$base] = $contenu_publication[$champ].' 00:00:00';
							}else if(strlen($contenu_publication[$champ]) == 19){
								$infos_publication[$base] = $contenu_publication[$champ];
							}
							break;
						case 'language_s':
							$infos_publication[$base] = $contenu_publication[$champ][0];
							break;
						case 'authFullName_s':
							$infos_publication[$base] = implode(', ',$contenu_publication[$champ]);
							break;
						default:
							if ($base == 'citation_reference')
								break;
							if(is_array($contenu_publication[$champ])){
								if(count($contenu_publication[$champ]) == 1)
									$infos_publication[$base] = $contenu_publication[$champ][0];
								else{
									$infos_publication[$base] = "<multi>";
									foreach($contenu_publication[$champ] as $key => $content){
										if(isset($contenu_publication['language_s'][$key]))
											$infos_publication[$base] .= "[".$contenu_publication['language_s'][$key]."]".$content;
									}
									$infos_publication[$base] .= "</multi>";
								}
							}else
								$infos_publication[$base] = $contenu_publication[$champ];
							break;
					}
				}
			}
			unset($year_sub);
			unset($month_sub);
			unset($day_sub);

			/**
			 * On construit la citation de référence
			 */
			$infos_publication['citation_reference'] = "";
			$revue = false;
			switch ($docType) {
				case "OUV":
				case "UNDEFINED":				
				case "OTHER":
					if (isset($contenu_publication['scientificEditor_s'])) {
						if (is_array($contenu_publication['scientificEditor_s'])) {
							if (count($contenu_publication['scientificEditor_s']) > 1)
								$sci_eds = implode(',', $contenu_publication['scientificEditor_s'])." (eds.)";
							else
								$sci_eds = implode(',', $contenu_publication['scientificEditor_s'])." (ed.)";
						} else
							$sci_eds = $contenu_publication['scientificEditor_s']." (ed)";
					}
				case "DOUV":
					if (isset($contenu_publication['subTitle_s']) && (is_array($contenu_publication['subTitle_s']) || (strlen($contenu_publication['subTitle_s']) > 0))) {
						// subTitle est un tableau : on ne prend que le sous-titre en français s'il existe
						$subTitle = "";
						if (is_array($contenu_publication['subTitle_s'])) {
							// à vérifier
							if (isset($contenu_publication['subTitle_s']['fr']))
								$subTitle = $contenu_publication['subTitle_s']['fr'];
							elseif (isset($contenu_publication['subTitle_s'][0]))
								$subTitle = $contenu_publication['subTitle_s'][0];
						}
						spip_log($subTitle, _LOG_ERREUR);
						// Guillemets : if (!preg_match('/^ *(\x{AB}|\x{22}|\x{201A}|\x{201C}|\x{}201F)/i',...) 
					}
					// Numéro spécial de revue : on ajoute la référence du numéro
					if (isset($contenu_publication['journalTitle_s']) && $contenu_publication['journalTitle_s'] != "") {
						$revue = true;
						/* if (isset($contenu_publication['bookTitle_s']) && $contenu_publication['bookTitle_s'] != "") {
							if (!preg_match('/^ *(num[ée]ro|(special)? issue)/i',$contenu_publication['bookTitle_s']))
								$infos_publication['citation_reference'] .= ;
							if (!preg_match('/^ *(\x{AB}|\x{22}|\x{201A}|\x{201C}|\x{}201F)/i',$contenu_publication['bookTitle_s']))
								$infos_publication['citation_reference'] .= "&laquo;&nbsp;".$contenu_publication['bookTitle_s']."&nbsp;&laquo;";
						} */
						if (isset($subTitle)) {
							$infos_publication['citation_reference'] .= $subTitle;
							if (preg_match('/^ *(numéro|(special)? issue).+$/i',$subTitle))
								$infos_publication['citation_reference'] .= ", ";
							else
								$infos_publication['citation_reference'] .= ", numéro spécial de la revue ";
							unset($subTitle);
						} else 
							$infos_publication['citation_reference'] .= "numéro spécial de la revue ";
						$infos_publication['citation_reference'] .= "<i>".$contenu_publication['journalTitle_s']."</i>";
						if (isset($contenu_publication['issue_s']) && $contenu_publication['issue_s'] != "") {
							$infos_publication['citation_reference'] .= ", n°".implode("–",$contenu_publication['issue_s']);
							if (isset($contenu_publication['volume_s']) && $contenu_publication['volume_s'] != "")
								$infos_publication['citation_reference'] .= " (".$contenu_publication['volume_s'].")";
						} elseif (isset($contenu_publication['volume_s']) && $contenu_publication['volume_s'] != "")
							$infos_publication['citation_reference'] .= "vol. ".$contenu_publication['volume_s'];
					}

					if (isset ($subTitle)) {
						if ($infos_publication['citation_reference'] != "")
							$infos_publication['citation_reference'] .= ". ";
						$infos_publication['citation_reference'] .= $subTitle;
					}
					// Normalement pas d'éditeur scientifique (autre que les auteurs dans les DOUV) : à vérifier
					if (isset($sci_eds)) {
						if ($infos_publication['citation_reference'] != "")
							$infos_publication['citation_reference'] .= ", ";
						$infos_publication['citation_reference'] .= $sci_eds;
					}

					// On met une virgule ici parce qu'il y aura au moins une date après
					if ($infos_publication['citation_reference'] != "")
						$infos_publication['citation_reference'] .= ", ";
					
					if (!$revue) {
						if (isset($contenu_publication['volume_s']) && !is_array($contenu_publication['volume_s'])) {
							$infos_publication['citation_reference'] .= $contenu_publication['volume_s'].", ";
						}

						if (isset($contenu_publication['city_s']) && $contenu_publication['city_s'] != "") {
							$infos_publication['citation_reference'] .= $contenu_publication['city_s'];
							if (isset($contenu_publication['publisher_s']) && (is_array($contenu_publication['publisher_s']) || strlen($contenu_publication['publisher_s']) > 0))
								$infos_publication['citation_reference'] .= "&nbsp;: ";
							else
								$infos_publication['citation_reference'] .= ", ";
						}
						if (isset($contenu_publication['publisher_s'])) {
							if (is_array($contenu_publication['publisher_s'])) {
								$infos_publication['citation_reference'] .= implode(' / ', $contenu_publication['publisher_s']).", ";
							}
							else {
								$infos_publication['citation_reference'] .= $contenu_publication['publisher_s'].", ";
							}
						}
						if (isset($contenu_publication['serie_s']) && is_array($contenu_publication['serie_s']))
							$infos_publication['citation_reference'] .= implode(', ', $contenu_publication['serie_s']).", ";
					}
					if ($contenu_publication['inPress_bool'] == "true")
						$infos_publication['citation_reference'] .= "à paraître";
					elseif ($infos_publication['date_production_format'] == 'annee' || !$revue) {
						$infos_publication['citation_reference'] .= substr($contenu_publication['producedDate_s'],0,4);
						spip_log($infos_publication['citation_reference'], _LOG_ERREUR);
					}
					elseif ($revue) {
						if (preg_match('/^(\d{4})-(\d{2})-(\d{2}).+$/',$infos_publication['date_production'],$date_publi_match)) {
							spip_log($infos_publication['date_production']." // ".$date_publi_match, _LOG_ERREUR);
							switch ($date_publi_match[2]) {
								case '01':
									$infos_publication['citation_reference'] .= "janvier ".$date_publi_match[1];
									break;
								case '02':
									$infos_publication['citation_reference'] .= "février ".$date_publi_match[1];
									break;
								case '03':
									$infos_publication['citation_reference'] .= "mars ".$date_publi_match[1];
									break;
								case '04':
									$infos_publication['citation_reference'] .= "avril ".$date_publi_match[1];
									break;
								case '05':
									$infos_publication['citation_reference'] .= "mai ".$date_publi_match[1];
									break;
								case '06':
									$infos_publication['citation_reference'] .= "juin ".$date_publi_match[1];
									break;
								case '07':
									$infos_publication['citation_reference'] .= "juillet ".$date_publi_match[1];
									break;
								case '08':
									$infos_publication['citation_reference'] .= "août ".$date_publi_match[1];
									break;
								case '09':
									$infos_publication['citation_reference'] .= "septembre ".$date_publi_match[1];
									break;
								case '10':
									$infos_publication['citation_reference'] .= "octobre ".$date_publi_match[1];
									break;
								case '11':
									$infos_publication['citation_reference'] .= "novembre ".$date_publi_match[1];
									break;
								case '12':
									$infos_publication['citation_reference'] .= "décembre ".$date_publi_match[1];
									break;
								default:
									break;
							}
						}
					}
					if (isset($infos_publication['isbn'])) {
						if ($revue)
							$infos_publication['citation_reference'] .= ". ISSN ".$infos_publication['isbn'];
						else
							$infos_publication['citation_reference'] .= ". ISBN ".$infos_publication['isbn'];
					}
					if (isset($contenu_publication['publisherLink_s']) && is_array($contenu_publication['publisherLink_s'])) {
						$infos_publication['citation_reference'] .= ". <a target=\"_blank\" href=\"".$contenu_publication['publisherLink_s'][0]."\">&#x3008;".$contenu_publication['publisherLink_s'][0]."&#x3009;</a>";
					}
					break;
				default:
					$infos_publication['citation_reference'] = $contenu_publication['citationRef_s'];
					break;
			}
			unset($sci_eds);
			unset($subTitle);

			/**
			 * On va chercher les ISBNs dans les citations (si le champs isbn est vide)
			 */
			if (!isset($infos_publication['isbn']) || $infos_publication['isbn'] == "") {
				foreach(array('citation_reference','citation_complete') as $info){
					if(isset($infos_publication[$info]) && (strlen($infos_publication[$info]) > 0)){
						if(preg_match('/(ISBN|ISSN) ?([0-9\-]{10,17}) /Uims',$infos_publication[$info],$matches)){
							$infos_publication['isbn'] = $matches[2];
							continue;
						}
					}
				}
			}
			/**
			 * On conserve l'ensemble des infos du document au cas où quand même
			 */
			$infos_publication['hal_complet'] = serialize($contenu_publication);
			
			$publications[] = $infos_publication;
		}
	}
	return $publications;
}

?>
