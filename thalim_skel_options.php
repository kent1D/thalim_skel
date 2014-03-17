<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS['forcer_lang'] = true;
$GLOBALS['table_des_traitements']['ROLE'][]= "thalim_role_chaine(%s)";
define('_COPIE_LOCALE_MAX_SIZE',1002097152);
if(!test_espace_prive())
	define('_MAILCRYPT_FONCTION_JS_LANCER_LIEN','thalim_email');
?>