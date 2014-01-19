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
			spip_log('evenement','test.'._LOG_ERREUR);
			spip_log($flux['args']['contexte']['composition'],'test.'._LOG_ERREUR);
			$flux['args']['contexte']['composition'] = 'evenement';
			$flux['data'] = evaluer_fond('structure', $flux['args']['contexte']);
	}
	return $flux;
}
?>