<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Autoriser les utilisateurs à modifier leur profil
 * 
 * On garde les autorisations par défaut pour les administrateurs et les rédacteurs
 * Par contre on autorise les visiteurs (6forum) à modifier un profil:
 * -* s'il sont eux même l'utilisateur à modifier
 * -* s'ils ont le bon statut
 * -* si on ne souhaite pas modifier le statut
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 */
if(!function_exists('autoriser_auteur_modifier')){
	function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt) {
		if (in_array($qui['statut'], array('0minirezo', '1comite')))
			return autoriser_auteur_modifier_dist($faire, $type, $id, $qui, $opt);
		else
			return
				!$opt['statut']
				AND $qui['statut'] == '6forum'
				AND $id == $qui['id_auteur'];
	}
}

function autoriser_crayonner($faire, $type, $id, $qui, $opt){
	if(!in_array($qui['statut'],array('0minirezo')))
		return false;
	return autoriser_crayonner_dist($faire, $type, $id, $qui, $opt);
}
?>
