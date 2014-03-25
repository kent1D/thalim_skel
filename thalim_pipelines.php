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
	if(isset($flux['args']['contexte']['id_article']) && isset($flux['args']['contexte']['type-page'])
		&& ($flux['args']['fond'] == 'structure')
		&& ($flux['args']['contexte']['id_article'] > 0)
		&& $flux['args']['contexte']['type-page'] == 'article'
		&& sql_getfetsel('id_evenement','spip_evenements','id_article='.intval($flux['args']['contexte']['id_article']))
		&& sql_getfetsel('seminaire','spip_articles','id_article='.intval($flux['args']['contexte']['id_article'])) != 'on'){
			$flux['args']['contexte']['composition'] = 'evenement';
			$flux['data'] = evaluer_fond('structure', $flux['args']['contexte']);
	}
	else if(isset($flux['args']['contexte']['id_article']) && isset($flux['args']['contexte']['type-page'])
		&& ($flux['args']['fond'] == 'structure')
		&& ($flux['args']['contexte']['id_article'] > 0)
		&& $flux['args']['contexte']['type-page'] == 'article'
		&& sql_getfetsel('id_rubrique','spip_articles','id_article='.intval($flux['args']['contexte']['id_article'])) == '-1'){
			$flux['args']['contexte']['composition'] = 'unique';
			$flux['data'] = evaluer_fond('structure', $flux['args']['contexte']);
	}
	else if(isset($flux['args']['contexte']['id_auteur']) && isset($flux['args']['contexte']['type-page'])
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

function thalim_skel_jquery_plugins($plugins){
	if(!test_espace_prive())
		$plugins[] = 'javascript/thalim.js';
	return $plugins;
}
?>