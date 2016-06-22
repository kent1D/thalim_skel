<?php
/**
 * Déclaration de la barre d'outil simple
 *
 * @plugin Thalim pour SPIP
 * @license GPL
 * @package SPIP\Thalim\BarreOutils
 */
if (!defined("_ECRIRE_INC_VERSION")) return;



/**
 * Définition de la barre 'simple' pour markitup
 *
 * @return Barre_outils La barre d'outil
 */
function barre_outils_simple(){
	// on modifie simplement la barre d'edition
	$edition = charger_fonction('edition','barre_outils');
	$barre = $edition();
	$barre->nameSpace = 'simple';
	$barre->cacherTout();
	$barre->afficher(array(
		'bold','italic',
		'sepGuillemets',
		'sepCaracteres','guillemets', 'guillemets_simples',
		'guillemets_de', 'guillemets_de_simples',
		'guillemets_autres', 'guillemets_autres_simples',
		'A_grave', 'E_aigu', 'E_grave', 'aelig', 'AElig', 'oe', 'OE', 'Ccedil',
	));
	return $barre;
}


?>
