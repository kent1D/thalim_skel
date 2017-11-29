<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * 
 * Si on est dans un article séminaire, que l'on utilise un squelette basé sur z ou zcore, 
 * on passe la composition seminaire à structure.html
 * 
 * Cela permet par exemple d'avoir un squelette content/article-seminaire.html avec spipr
 */
function thalim_skel_recuperer_fond($flux){
	if(isset($flux['args']['contexte']['id_article']) 
		&& isset($flux['args']['contexte']['type-page'])
		&& ($flux['args']['fond'] == 'structure')
		&& ($flux['args']['contexte']['id_article'] > 0)
		&& $flux['args']['contexte']['type-page'] == 'article'
		&& $flux['args']['contexte']['composition'] != 'these'
		&& sql_getfetsel('id_evenement','spip_evenements','id_article='.intval($flux['args']['contexte']['id_article']).' AND statut="publie"')
		&& sql_getfetsel('seminaire','spip_articles','id_article='.intval($flux['args']['contexte']['id_article'])) != 'on'){
			$nb_events = sql_countsel('spip_evenements','id_article='.intval($flux['args']['contexte']['id_article']));
			if(count($nb_events) > 1){
				$flux['args']['contexte']['composition'] = 'evenements';
			}else
				$flux['args']['contexte']['composition'] = 'evenement';
			$flux['data'] = evaluer_fond('structure', $flux['args']['contexte']);
	}
	else if(isset($flux['args']['contexte']['id_article']) 
		&& isset($flux['args']['contexte']['type-page'])
		&& ($flux['args']['fond'] == 'structure')
		&& ($flux['args']['contexte']['id_article'] > 0)
		&& $flux['args']['contexte']['type-page'] == 'article'
		&& sql_getfetsel('id_rubrique','spip_articles','id_article='.intval($flux['args']['contexte']['id_article'])) == '-1'){
			$flux['args']['contexte']['composition'] = 'unique';
			$flux['data'] = evaluer_fond('structure', $flux['args']['contexte']);
	}
	else if(isset($flux['args']['contexte']['id_auteur']) 
		&& isset($flux['args']['contexte']['type-page'])
		&& ($flux['args']['fond'] == 'structure')
		&& ($flux['args']['contexte']['id_auteur'] > 0)
		&& ($flux['args']['contexte']['type-page'] == 'auteur')
		&& ($flux['args']['contexte']['vue'] == 'edit' || _request('vue') == 'edit')
		&& autoriser('modifier','auteur',$GLOBALS['visiteur_session'])){
			$flux['args']['contexte']['composition'] = 'edit';
			$flux['data'] = evaluer_fond('structure', $flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_boucle (SPIP)
 * 
 * On n'affiche que les utilisateurs qui ont le statut "membre"
 * 
 * @param object $boucle
 * 		Le contexte de boucle dans lequel on est
 * @return object $boucle
 * 		Le contexte de boucle modifié
 */
function thalim_skel_pre_boucle($boucle){
	if(!test_espace_prive()
		&& ($boucle->type_requete == 'auteurs')){
		if(!isset($boucle->modificateur['tout'])
			&& !isset($boucle->modificateur['criteres']['id_auteur'])
			&& !isset($boucle->modificateur['criteres']['membre_qualite'])){
			$qualite = $boucle->id_table . '.membre_qualite';
			$boucle->where[] = array("'='", "'$qualite'", "_q('membre')"); 
		}
	}
	return $boucle;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * 
 * @param array $plugins
 * 		Les plugins jQuery chargés
 * @return array $plugins
 * 		Le tableau des plugins jQuery complété
 */
function thalim_skel_jquery_plugins($plugins){
	if(!test_espace_prive()) {
		$plugins[] = 'javascript/thalim.js';
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline diogene_objets (Diogene)
 * On ajoute la possibilité d'ajouter des input files spécifiques dans les formulaires
 * d'article
 * 
 * @param array $flux
 * 		La liste des champs pour les diogenes
 * @return array $flux
 * 		La liste des champs complétés
 */
function thalim_skel_diogene_objets($flux){
	if(isset($flux['article']) && is_array($flux['article'])){
		$flux['article']['champs_sup']['doctorants'] = _T('thalim:label_ajout_doctorant');
		$flux['article']['champs_sup']['fichier_appel'] = _T('thalim:label_ajout_fichier_appel');
		$flux['article']['champs_sup']['fichier_affiche'] = _T('thalim:label_ajout_fichier_affiche');
		$flux['article']['champs_sup']['fichier_couverture'] = _T('thalim:label_ajout_fichier_couverture');
		$flux['article']['champs_sup']['fichier_programme'] = _T('thalim:label_ajout_fichier_programme');
		$flux['article']['champs_sup']['fichier_flyer'] = _T('thalim:label_ajout_fichier_flyer');
		$flux['article']['champs_sup']['fichier_communication'] = _T('thalim:label_ajout_fichier_communication');
		$flux['article']['champs_sup']['fichier_invitation'] = _T('thalim:label_ajout_fichier_invitation');
		$flux['article']['champs_sup']['fichier_sommaire'] = _T('thalim:label_ajout_fichier_sommaire');
		$flux['article']['champs_sup']['fichier_resumes'] = _T('thalim:label_ajout_fichier_resumes');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * 
 * On ajoute enctype='multipart/form-data' sur le formulaire si on a des fichiers et qu'on n'a pas ce paramètre sur le formulaire
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function thalim_skel_editer_contenu_objet($flux){
	var_dump($flux['args']['contexte']['typdoc']);
	/**
	 * On vérifie que on a fichier_* dans le formulaire
	 */
	if(preg_match(',fichier_,Uims',$flux['data'],$regs)){
		/**
		 * On vérifie si on n'a pas déjà le enctype
		 */
		if(!preg_match(',<form.*enctype=.*>,Uims',$flux['data'],$regs)){
			/**
			 * On vérifie quand même si on a bien un <form et on le remplace par ce qu'il faut
			 */
			if(preg_match(',<(form.*[^>])>,Uims',$flux['data'],$regs)){
				$flux['data'] = preg_replace(',<(form.*[^>])>,Uims','<\\1 enctype=\'multipart/form-data\'>',$flux['data'],1);
			}
		}
	}
	if($flux['args']['type'] == 'auteur' && strpos($flux['data'],'<!--extra-->')!==FALSE){
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['id'];
		$saisie = recuperer_fond('formulaires/thalim_ajouter_cv',$flux['args']['contexte']);
		$flux['data'] = preg_replace(',(.*)(<!--extra-->),ims',"\\1<ul>".$saisie."</ul>\\2",$flux['data'],1);
		if(preg_match(',<(form.*[^>])>,Uims',$flux['data'],$regs)){
			$flux['data'] = preg_replace(',<(form.*[^>])>,Uims','<\\1 enctype=\'multipart/form-data\'>',$flux['data'],1);
		}
	}
	if($flux['args']['type'] == 'hals_publication' && strpos($flux['data'],'<!--extra-->')!==FALSE){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($objet);
		$id_objet = $flux['args']['id'];
		
		/**
		 * Ajouter les mots clés
		 */
		// $mots_obligatoires = array('8','10','13');
		 /**
		 * Ajout BL: type de publication pour publis HAL
		 */
		/**
		 * On récupère le type de publication
		 */
		$type_hal_old = sql_fetsel('hals.typdoc','spip_hals_publications as hals','hals.id_hals_publication='.intval($id_objet));
		$type_hal = $flux['args']['contexte']['typdoc'];
		echo "<h1>$type_hal</h1>";
		/**
		 * On définit les groupes de mots obligatoire en fonction du type
		 */
		$mots_obligatoires = ($type_hal == 'COMM') ? array('8','10','13','3') : array('8','10','13','11');

		$valeurs_mots['id_groupes'] = $mots_obligatoires;
		/**
		 * On récupère les mots qui sont postés ou peut être déjà associés
		 */
		foreach($valeurs_mots['id_groupes'] as $id_groupe){
			//$valeurs_mots['groupe_obligatoire_'.$id_groupe] = 'oui';
			if (_request('groupe_'.$id_groupe)) {
				// Pour récupérer la selection courante en cas d'erreur dans vérifier() ou traiter()
				$valeurs_mots['groupe_'.$id_groupe] = _request('groupe_'.$id_groupe);
			}else if (sql_getfetsel('unseul','spip_groupes_mots','id_groupe='.intval($id_groupe)) == 'oui') {
				$valeurs_mots['groupe_'.$id_groupe] = sql_fetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON (mot.id_mot=mots_liens.id_mot)','mots_liens.objet='.sql_quote('hals_publication').' AND mots_liens.id_objet='.intval($id_objet).' AND mot.id_groupe='.intval($id_groupe));
			}else {
				$result = sql_allfetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON mot.id_mot=mots_liens.id_mot','mots_liens.objet='.sql_quote('hals_publication').' AND mot.id_groupe='.intval($id_groupe).' AND mots_liens.id_objet='.intval($id_objet));
				foreach ($result as $row) {
					$valeurs_mots['groupe_'.$id_groupe][] = $row['id_mot'];
				}
			}
		}
		if (is_array($valeurs_mots))
			$flux['args']['contexte'] = array_merge($flux['args']['contexte'],$valeurs_mots);
		
		$flux['args']['contexte']['legende'] = "Sélectionner les autres pages concernées par votre annonce";
		$saisie = recuperer_fond('formulaires/diogene_ajouter_medias_mots',$flux['args']['contexte']);
		
		/**
		 * Ajouter les documents
		 */
		if(!preg_match(',<form.*enctype=.*>,Uims',$flux['data'],$regs)){
			/**
			 * On vérifie quand même si on a bien un <form et on le remplace par ce qu'il faut
			 */
			if(preg_match(',<(form.*[^>])>,Uims',$flux['data'],$regs)){
				$flux['data'] = preg_replace(',<(form.*[^>])>,Uims','<\\1 enctype=\'multipart/form-data\'>',$flux['data'],1);
			}
		}
		$flux['args']['contexte']['champs_ajoutes'] = array('fichier_couverture','fichier_flyer','fichier_invitation');
		$saisie .= recuperer_fond('formulaires/diogene_fichiers_thalim',$flux['args']['contexte']);
		
		/**
		 * On ajoute tout cela dans le formulaire
		 */
		$flux['data'] = preg_replace(',(.*)(<!--extra-->),ims',"\\1<ul>".$saisie."</ul>\\2",$flux['data'],1);
	}
	return $flux;
}
/**
 * Insertion dans le pipeline diogene_ajouter_saisies (Diogène)
 * 
 * On ajoute nos champs dans le formulaire
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux 
 * 		Le contexte modifié passé aux suivants
 */
function thalim_skel_diogene_ajouter_saisies($flux){
	$champs_ajoutes = unserialize($flux['args']['champs_ajoutes']);
	if(is_array($champs_ajoutes) && in_array('doctorants',$champs_ajoutes)){
		$flux['args']['contexte']['champs_ajoutes'] = unserialize($flux['args']['champs_ajoutes']);
		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_doctorants',$flux['args']['contexte']);
	}
	$fichier = false;
	$types_fichier = array('fichier_affiche','fichier_appel','fichier_couverture','fichier_programme','fichier_flyer','fichier_communication','fichier_invitation','fichier_sommaire','fichier_resumes');
	foreach($types_fichier as $type){
		if(in_array($type,$champs_ajoutes))
			$fichier = true;
	}
	if(is_array($champs_ajoutes) && $fichier){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];

		$flux['args']['contexte']['champs_ajoutes'] = unserialize($flux['args']['champs_ajoutes']);
		$flux['data'] .= recuperer_fond('formulaires/diogene_fichiers_thalim',$flux['args']['contexte']);
	}
	return $flux;
}

/** 
 * Vérifier les contenus des formulaires pour ajouter erreurs au submit du formulaire renvoie erreur
 */
function thalim_skel_formulaire_verifier($flux){
	if($flux['args']['form'] == 'editer_auteur' && intval(_request('id_auteur')) > 0){
		if(isset($_FILES['thalim_cv']) && ($_FILES['thalim_cv']['error'] == 0)){
			$file = $_FILES['thalim_cv'];
			include_spip('action/ajouter_documents');
			include_spip('inc/joindre_document');
			/**
			 * Ces fichiers ne peuvent être que PDFs
			 */
			$infos_doc = fixer_extension_document($_FILES['thalim_cv']);
			if(!in_array($infos_doc[0],array('pdf')))
				$flux['data']['thalim_cv'] = _T('thalim:erreur_doc_pdf');
		}
	}
	
	if($flux['args']['form'] == 'editer_hals_publication'){
		/**
		 * Vérification des documents
		 */
		$erreurs = $flux['data'];
		$fichier = true;
		$types_fichier = array('fichier_couverture','fichier_flyer','fichier_sommaire');
		include_spip('inc/joindre_document');
		$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
		$files = array();
		if (is_array($post)){
			include_spip('action/ajouter_documents');
			foreach ($post as $name => $file) {
				if (is_array($file['name'])){
					while (count($file['name'])){
						$test=array(
							'error'=>array_shift($file['error']),
							'name'=>array_shift($file['name']),
							'tmp_name'=>array_shift($file['tmp_name']),
							'type'=>array_shift($file['type']),
							);
						if (!($test['error'] == 4)){
							if (is_string($err = joindre_upload_error($test['error'])))
								$erreur[$name] = $err; // un erreur upload
							if (!is_array(verifier_upload_autorise($test['name'])))
								$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$test['name']));
							if(!isset($erreur[$name]))
								$files[$name]=$test;
						}
					}
				}
				else {
					//UPLOAD_ERR_NO_FILE
					if (!($file['error'] == 4)){
						if (is_string($err = joindre_upload_error($file['error'])))
							$erreur[$name] = $err; // un erreur upload
						if (!is_array(verifier_upload_autorise($file['name'])))
							$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$file['name']));
						if(!isset($erreur[$name]))
							$files[$name]=$file;
					}
				}
			}
		}
		/**
		 * Ces fichiers peuvent être soit des PDFs, soit des images
		 */
		$pdf_image = array('fichier_couverture','fichier_flyer','fichier_sommaire');
		foreach($pdf_image as $type_fichier_pdf_image){
			if(in_array($type_fichier_pdf_image,$types_fichier) && isset($files[$type_fichier_pdf_image])){
				$infos_doc = fixer_extension_document($files[$type_fichier_pdf_image]);
				if(!in_array($infos_doc[0],array('pdf','jpg','gif','png')))
					$erreurs[$type_fichier_pdf_image] = _T('thalim:erreur_doc_image_pdf');
			}
		}
	}
	return $flux;
}

/**
 * Association à l'auteur du formulaire
 */
function thalim_skel_formulaire_traiter($flux){
	if($flux['args']['form'] == 'editer_auteur' && intval(_request('id_auteur')) > 0){
		if(isset($_FILES['thalim_cv']) && ($_FILES['thalim_cv']['error'] == 0)){
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');
			include_spip('action/editer_document');
			$mode = 'document';
			$nb_docs = 0;
			$files = array($_FILES['thalim_cv']);
			if($_FILES['thalim_cv']['name'] != '' && $_FILES['thalim_cv']['error'] != 4){
					$id_document = sql_getfetsel('doc.id_document',
												 'spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document',
												 'lien.objet="auteur" AND lien.id_objet='.intval(_request('id_auteur')).' AND doc.extension="pdf" AND doc.document_type="cv"');
					$nouveau_doc = $ajouter_documents($id_document,array($_FILES['thalim_cv']),'auteur',_request('id_auteur'),$mode);
					$test = document_modifier($nouveau_doc[0],array('document_type'=>'cv'));
			}
		}
		// si on a cliqué sur le bouton supprimer
		elseif(_request('supprimer_thalim_cv')){
			include_spip('action/dissocier_document');
			$id_document = sql_getfetsel('doc.id_document',
										 'spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document',
										 'lien.objet="auteur" AND lien.id_objet='.intval(_request('id_auteur')).' AND doc.document_type="cv"');
			if($id_document)
				dissocier_document($id_document, 'auteur', _request('id_auteur'), true);
		}
	}
	if($flux['args']['form'] == 'editer_hals_publication' && intval($flux['data']['id_hals_publication']) > 0){
		include_spip('action/editer_mot');
		$invalider = false;
		$objet = 'hals_publication';

		$id_objet = $flux['data']['id_hals_publication'];

		include_spip('inc/editer_mots');
		/**
		 * BL : ajout du type de publication
		 */
		// $groupes_possibles = array('8','10','13');
		$groupes_possibles = array('8','10','13','3','11');

		/**
		 * On traite chaque groupe séparément
		 * Si c'est une modification d'objet il se peut qu'il faille supprimer les anciens mots
		 * On fait une vérifications sur chaque groupe
		 */
		foreach($groupes_possibles as $id_groupe){
			$mots_multiples = array();
			$requete_id_groupe = _request('groupe_'.$id_groupe) ? _request('groupe_'.$id_groupe) : array();

			$result = sql_allfetsel('0+mot.titre AS num, mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON mot.id_mot=liens.id_mot','liens.objet='.sql_quote($objet).' AND mot.id_groupe='.intval($id_groupe).' AND liens.id_objet='.intval($id_objet),'','num, mot.titre');
			foreach ($result as $row) {
				$mots_multiples[] = $row['id_mot'];
			}

			if(is_array($requete_id_groupe)){
				foreach($requete_id_groupe as $cle => $mot){
					/**
					 * Si le mot est déja dans les mots, on le supprime juste
					 * de l'array des mots originaux
					 */
					if(in_array($mot, $mots_multiples))
						$mots_multiples = array_diff($mots_multiples,array($mot));
					else{
						sql_insertq('spip_mots_liens', array('id_mot' =>$mot,  'id_objet' => $id_objet,'objet'=> $objet));
						$invalider = true;
					}
				}
			}
			else{
				if(in_array($requete_id_groupe, $mots_multiples))
					$mots_multiples = array_diff($mots_multiples,array($requete_id_groupe));
				else{
					sql_insertq('spip_mots_liens', array('id_mot' =>$requete_id_groupe,  'id_objet' => $id_objet,'objet'=> $objet));
					$invalider = true;
				}
			}
			/**
			 * S'il reste quelque chose dans les mots d'origine, on les délie de l'objet
			 */
			if(count($mots_multiples)>0){
				sql_delete('spip_mots_liens','objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_mot IN ('.implode(',',$mots_multiples).')');
				$invalider = true;
			}

			// On nettoie les variables mises à jour dans verifier()
			set_request('groupe_'.$id_groupe, $requete_id_groupe);
			set_request('nouveaux_groupe_'.$id_groupe, array());
			
			/**
			 * Traitement des documents
			 */
			$fichier = true;
			$types_fichier = array('fichier_couverture','fichier_flyer','fichier_sommaire');
			
			include_spip('inc/joindre_document');
			$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
			$files = array();
			$fichiers_ajoutes = array();
			if (is_array($post)){
				include_spip('action/ajouter_documents');
				foreach ($post as $name => $file) {
					if (is_array($file['name'])){
						while (count($file['name'])){
							$test=array(
								'error'=>array_shift($file['error']),
								'name'=>array_shift($file['name']),
								'tmp_name'=>array_shift($file['tmp_name']),
								'type'=>array_shift($file['type']),
								);
							if (!($test['error'] == 4)){
								if (is_string($err = joindre_upload_error($test['error'])))
									$erreur[$name] = $err; // un erreur upload
								if (!is_array(verifier_upload_autorise($test['name'])))
									$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$test['name']));
								if(!isset($erreur[$name]))
									$files[$name]=$test;
							}
						}
					}
					else {
						//UPLOAD_ERR_NO_FILE
						if (!($file['error'] == 4)){
							if (is_string($err = joindre_upload_error($file['error'])))
								$erreur[$name] = $err; // un erreur upload
							if (!is_array(verifier_upload_autorise($file['name'])))
								$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$file['name']));
							if(!isset($erreur[$name]))
								$files[$name]=$file;
						}
					}
				}
			}
			if(is_array($files) && count($files) > 0){
				$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				include_spip('action/editer_document');
				$mode = 'document';
				$nb_docs = 0;
				foreach($files as $name => $fichier){
					if($fichier['name'] != '' && $fichier['error'] != 4){
						$type_document = str_replace('fichier_','',$name);
						if(in_array($type_document,array('couverture','flyer','sommaire'))){
							$id_document = sql_getfetsel('doc.id_document',
														 'spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document',
														 'lien.objet="hals_publication" AND lien.id_objet='.intval($id_objet).' AND doc.document_type='.sql_quote($type_document));
							$nouveau_doc = $ajouter_documents($id_document,array($fichier),'hals_publication',$id_objet,$mode);
							$test = document_modifier($nouveau_doc[0],array('document_type'=>$type_document));
							$fichiers_ajoutes[] = $name;
							$nb_docs++;
						}
					}
				}
			}
			foreach($types_fichier as $type){
				include_spip('action/dissocier_document');
				if(_request('supprimer_'.$type) && !in_array($type,$fichiers_ajoutes)){
					$type_document = str_replace('fichier_','',$type);
					$id_document = sql_getfetsel('doc.id_document',
												 'spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document',
												 'lien.objet="hals_publication" AND lien.id_objet='.intval($id_objet).' AND doc.document_type='.sql_quote($type_document));
					if($id_document)
						dissocier_document($id_document, 'hals_publication', $id_objet, true);
				}
			}
			if($invalider){
				include_spip('inc/invalideur');
				suivre_invalideur("id='$objet/$id_objet'");
			}
		}
	}
	return $flux;
}
/**
 * Insertion dans le pipeline diogene_vérifier (Diogène)
 * Fonction s'exécutant à la vérification du formulaire 
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié passé aux suivants
 */
function thalim_skel_diogene_verifier($flux){
	if(($id_diogene = intval(_request('id_diogene'))) && $id_diogene > 0){
		$champs_ajoutes = unserialize(sql_getfetsel("champs_ajoutes","spip_diogenes","id_diogene=".intval($id_diogene)));
		$erreurs = $flux['args']['erreurs'];
		$fichier = false;
		$types_fichier = array('fichier_affiche','fichier_appel','fichier_couverture','fichier_programme','fichier_flyer','fichier_communication','fichier_invitation','fichier_sommaire');
		foreach($types_fichier as $type){
			if(in_array($type,$champs_ajoutes))
				$fichier = true;
		}
		if (is_array($champs_ajoutes) && $fichier){
			include_spip('inc/joindre_document');
			$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
			$files = array();
			if (is_array($post)){
				include_spip('action/ajouter_documents');
				foreach ($post as $name => $file) {
					if (is_array($file['name'])){
						while (count($file['name'])){
							$test=array(
								'error'=>array_shift($file['error']),
								'name'=>array_shift($file['name']),
								'tmp_name'=>array_shift($file['tmp_name']),
								'type'=>array_shift($file['type']),
								);
							if (!($test['error'] == 4)){
								if (is_string($err = joindre_upload_error($test['error'])))
									$erreur[$name] = $err; // un erreur upload
								if (!is_array(verifier_upload_autorise($test['name'])))
									$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$test['name']));
								if(!isset($erreur[$name]))
									$files[$name]=$test;
							}
						}
					}
					else {
						//UPLOAD_ERR_NO_FILE
						if (!($file['error'] == 4)){
							if (is_string($err = joindre_upload_error($file['error'])))
								$erreur[$name] = $err; // un erreur upload
							if (!is_array(verifier_upload_autorise($file['name'])))
								$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$file['name']));
							if(!isset($erreur[$name]))
								$files[$name]=$file;
						}
					}
				}
			}
			/**
			 * Ces fichiers peuvent être soit des PDFs, soit des images
			 */
			$pdf_image = array('fichier_affiche','fichier_couverture','fichier_flyer','fichier_invitation','fichier_sommaire');
			foreach($pdf_image as $type_fichier_pdf_image){
				if(in_array($type_fichier_pdf_image,$champs_ajoutes) && isset($files[$type_fichier_pdf_image])){
					$infos_doc = fixer_extension_document($files[$type_fichier_pdf_image]);
					if(!in_array($infos_doc[0],array('pdf','jpg','gif','png')))
						$erreurs[$type_fichier_pdf_image] = _T('thalim:erreur_doc_image_pdf');
				}
			}
			/**
			 * Ces fichiers doivent être des PDFs
			 */
			$pdf = array('fichier_programme','fichier_appel','fichier_communication','fichier_resumes');
			foreach($pdf as $type_fichier_pdf){
				if(in_array($type_fichier_pdf,$champs_ajoutes) && isset($files[$type_fichier_pdf])){
					$infos_doc = fixer_extension_document($files[$type_fichier_pdf]);
					if(!in_array($infos_doc[0],array('pdf')))
						$erreurs[$type_fichier_pdf] = _T('thalim:erreur_doc_pdf');
				}
			}
			if(!_request('titre') || _request('titre') == "")
				$erreurs['titre'] = _T('info_obligatoire');
		}
		$flux['data'] = array_merge($flux['data'], $erreurs);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_vérifier (Diogène)
 * Fonction s'exécutant à la vérification du formulaire 
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié passé aux suivants
 */
function thalim_skel_diogene_traiter($flux){
	if(($id_diogene = intval(_request('id_diogene'))) && $id_diogene > 0){
		$champs_ajoutes = unserialize(sql_getfetsel("champs_ajoutes","spip_diogenes","id_diogene=".intval($id_diogene)));
		$fichier = false;
		$types_fichier = array('fichier_affiche','fichier_appel','fichier_couverture','fichier_programme','fichier_flyer','fichier_communication','fichier_invitation','fichier_sommaire');
		$id_objet = $flux['args']['id_objet'];
		foreach($types_fichier as $type){
			if(in_array($type,$champs_ajoutes))
				$fichier = true;
		}
		if (is_array($champs_ajoutes) && $fichier){
			include_spip('inc/joindre_document');
			$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
			$files = array();
			$fichiers_ajoutes = array();
			if (is_array($post)){
				include_spip('action/ajouter_documents');
				foreach ($post as $name => $file) {
					if (is_array($file['name'])){
						while (count($file['name'])){
							$test=array(
								'error'=>array_shift($file['error']),
								'name'=>array_shift($file['name']),
								'tmp_name'=>array_shift($file['tmp_name']),
								'type'=>array_shift($file['type']),
								);
							if (!($test['error'] == 4)){
								if (is_string($err = joindre_upload_error($test['error'])))
									$erreur[$name] = $err; // un erreur upload
								if (!is_array(verifier_upload_autorise($test['name'])))
									$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$test['name']));
								if(!isset($erreur[$name]))
									$files[$name]=$test;
							}
						}
					}
					else {
						//UPLOAD_ERR_NO_FILE
						if (!($file['error'] == 4)){
							if (is_string($err = joindre_upload_error($file['error'])))
								$erreur[$name] = $err; // un erreur upload
							if (!is_array(verifier_upload_autorise($file['name'])))
								$erreur[$name] = _T('medias:erreur_upload_type_interdit',array('nom'=>$file['name']));
							if(!isset($erreur[$name]))
								$files[$name]=$file;
						}
					}
				}
			}
			if(is_array($files) && count($files) > 0){
				$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				include_spip('action/editer_document');
				$mode = 'document';
				$nb_docs = 0;
				foreach($files as $name => $fichier){
					if($fichier['name'] != '' && $fichier['error'] != 4){
						$type_document = str_replace('fichier_','',$name);
						if(in_array($type_document,array('affiche','couverture','programme','flyer','invitation','sommaire','appel','communication','resumes'))){
							$id_document = sql_getfetsel('doc.id_document',
														 'spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document',
														 'lien.objet="article" AND lien.id_objet='.intval($id_objet).' AND doc.document_type='.sql_quote($type_document));
							$nouveau_doc = $ajouter_documents($id_document,array($fichier),'article',$id_objet,$mode);
							$test = document_modifier($nouveau_doc[0],array('document_type'=>$type_document));
							$fichiers_ajoutes[] = $name;
							$nb_docs++;
						}
					}
				}
			}
			foreach($types_fichier as $type){
				include_spip('action/dissocier_document');
				if(_request('supprimer_'.$type) && !in_array($type,$fichiers_ajoutes)){
					$type_document = str_replace('fichier_','',$type);
					$id_document = sql_getfetsel('doc.id_document',
												 'spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document',
												 'lien.objet="article" AND lien.id_objet='.intval($id_objet).' AND doc.document_type='.sql_quote($type_document));
					if($id_document)
						dissocier_document($id_document, 'article', $id_objet, true);
				}
			}
		}
		if(in_array('doctorants',$champs_ajoutes)){
			include_spip('inc/invalideur');
			include_spip('action/editer_auteur');
	
			$auteurs_liste = array();
			$auteurs = sql_allfetsel("auteur.id_auteur","spip_auteurs as auteur LEFT join spip_auteurs_liens as auteur_lien ON auteur.id_auteur=auteur_lien.id_auteur","auteur_lien.objet=".sql_quote('article')." AND auteur.role='doctorant' AND auteur_lien.id_objet=".intval($id_objet));
			foreach($auteurs as $auteur){
				$auteurs_liste[] = $auteur['id_auteur'];
			}
			/**
			 * diogene_gerer_auteurs n'est pas un array, on supprime tous les auteurs sauf soi même si on n'est pas admin
			 */
			
			foreach($auteurs_liste as $auteur){
				/**
				 * On ne peut pas s'enlever soit même des auteurs si l'on n'est pas admin
				 */
				if($auteur != _request('diogene_doctorants')){
					$suppr = auteur_dissocier($auteur,array($type=>$id_objet));
					suivre_invalideur("id='id_auteur/$auteur'",true);
				}
			}
			
			if(intval(_request('diogene_doctorants')) > 0 && !in_array($auteurs_liste,_request('diogene_doctorants'))){
				
				$ajout = auteur_associer(_request('diogene_doctorants'),array('article'=>$id_objet));
				suivre_invalideur("id='id_auteur/$auteur'",true);
			}
		}
		if($id_diogene == 9){
			sql_updateq('spip_articles',array('seminaire'=>'on'),'id_article='.$id_objet);
		}
	}
	return $flux;
}

function thalim_skel_diogene_avant_formulaire($flux){
	$type = $flux['args']['diogene_identifiant'];
	if($js = find_in_path('javascript/diogene_'.$type.'.js')){
// 		$flux['data'] .= "<script type='text/javascript' src='".parametre_url($js,'timestamp',mktime())."'></script>";
		$flux['data'] .= "<script type='text/javascript' src='".parametre_url($js,'timestamp',time())."'></script>";
	}
	return $flux;
}

function thalim_skel_porte_plume_barre_pre_charger($barres){
	$barre = &$barres['edition'];
	
	$barre->cacher('stroke_through');

	$module_barre = "barre_outils";
	if (intval($GLOBALS['spip_version_branche'])>2)
		$module_barre = "barreoutils";

	// Petites capitales
	$barre->ajouterApres('italic', array(
		"id"          => 'exposant',
		"name"        => _T('thalim:barre_exposant'),
		"className"   => "outil_exposant",
		"openWith"    => "<sup>", 
		"closeWith"   => "</sup>",
		"display"     => true,
		"selectionType" => "word",
	));
	
	return $barres;
}

function thalim_skel_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_exposant' => array('exposant.png','0'),
	));
}

function thalim_skel_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteurs'){
		$flux['data'] .= '<div class="nettoyeur"></div>';
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/configurer_membres');
	}
	return $flux;
}
?>
