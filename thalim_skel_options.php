<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS['forcer_lang'] = true;
$GLOBALS['table_des_traitements']['ROLE'][]= "thalim_role_chaine(%s)";
define('_COPIE_LOCALE_MAX_SIZE',1002097152);
if(!test_espace_prive())
	define('_MAILCRYPT_FONCTION_JS_LANCER_LIEN','thalim_email');

define('_DIOGENE_REDIRIGE_PUBLICATION','oui');

if(isset($GLOBALS['visiteur_session']) && isset($GLOBALS['visiteur_session']['statut']) && in_array($GLOBALS['visiteur_session']['statut'],array('0minirezo','1comite')) && !isset($_COOKIE['spip_admin'])){
	$activer_cookie = charger_fonction('cookie','action');
	$activer_cookie("@".$GLOBALS['visiteur_session']['login']);
}

if(!function_exists('autoriser_ecrire')){
	function autoriser_ecrire($faire, $type, $id, $qui, $opt) {
		return ($qui['statut'] =='0minirezo');
	}
}
?>