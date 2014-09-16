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
	if(!test_espace_prive())
		$plugins[] = 'javascript/thalim.js';
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
		foreach($types_fichier as $type){
			if(in_array($type,$champs_ajoutes))
				$fichier = true;
		}
		if (is_array($champs_ajoutes) && $fichier){
			$id_objet = $flux['args']['id_objet'];
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
	}
	return $flux;
}

function thalim_skel_diogene_avant_formulaire($flux){
	$type = $flux['args']['diogene_identifiant'];
	if($js = find_in_path('javascript/diogene_'.$type.'.js')){
		$flux['data'] .= "<script type='text/javascript' src='".parametre_url($js,'timestamp',mktime())."'></script>";
	}
	return $flux;
}
?>