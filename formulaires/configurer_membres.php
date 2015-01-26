<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_membres_charger_dist(){
	// travailler sur des meta fraiches
	include_spip('inc/meta');
	lire_metas();
	
	$valeurs = array();
	foreach(array('membres') as $k)
		$valeurs[$k] = isset($GLOBALS['meta'][$k])?$GLOBALS['meta'][$k]:'';
		
	return $valeurs;
}

function formulaires_configurer_membres_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_configurer_membres_traiter_dist(){
	include_spip('inc/config');

	include_spip('inc/meta');
	foreach(array('membres') as $k)
		ecrire_meta($k,_request($k));
	
	include_spip('inc/invalider');
	suivre_invalideur(1);
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>
