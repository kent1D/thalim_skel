<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation/maj des tables evenements et participants...
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function thalim_skel_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	$maj['create'] = array(
		array('thalim_recuperer_documents',array())
	);

	$maj['0.0.2'] = array(
		array('thalim_types_documents',array())
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function thalim_recuperer_documents(){
	$copier_local = charger_fonction('copier_local','action');
	$documents = sql_allfetsel('id_document','spip_documents','distant="oui"');
	foreach($documents as $document){
		$copier_local($document['id_document']);
	}
}

function thalim_types_documents(){
	sql_updateq('spip_documents',array('document_type'=>'affiche'),'document_type="" AND titre LIKE "%affiche%"');
	sql_updateq('spip_documents',array('document_type'=>'programme'),'document_type="" AND titre LIKE "%programme%"');
	sql_updateq('spip_documents',array('document_type'=>'couverture'),'document_type="" AND titre LIKE "%couverture%"');
	sql_updateq('spip_documents',array('document_type'=>'flyer'),'document_type="" AND titre LIKE "%flyer%"');
	sql_updateq('spip_documents',array('document_type'=>'affiche'),'document_type="" AND fichier LIKE "%affiche%"');
	sql_updateq('spip_documents',array('document_type'=>'programme'),'document_type="" AND fichier LIKE "%programme%"');
	sql_updateq('spip_documents',array('document_type'=>'couverture'),'document_type="" AND fichier LIKE "%couverture%"');
	sql_updateq('spip_documents',array('document_type'=>'flyer'),'document_type="" AND fichier LIKE "%flyer%"');
}
?>
