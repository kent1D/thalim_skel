<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function thalim_skel_declarer_champs_extras($champs = array()) {

	// Table : spip_articles
	if (!is_array($champs['spip_articles'])) {
		$champs['spip_articles'] = array();
	}

	$champs['spip_articles']['autres_auteurs'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'autres_auteurs',
		    'label' => 'Autres auteurs',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_articles']['editeur'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'editeur',
		    'label' => 'Nom de l\'éditeur',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_articles']['direction_ouvrage'] = array (
		  'saisie' => 'case',
		  'options' => 
		  array (
		    'nom' => 'direction_ouvrage',
		    'label' => 'Direction d\'ouvrage',
		    'label_case' => 'Est-ce une direction d\'ouvrage ?',
		    'sql' => 'varchar(3) DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_articles']['directeurs'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'directeurs',
		    'label' => 'Dirigé par (pour les articles dans des ouvrages collectif)',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_TYPO',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_articles']['garants_thalim'] = array (
		  'saisie' => 'auteurs',
		  'options' => 
		  array (
		    'nom' => 'garants_thalim',
		    'label' => 'Garant THALIM',
		    'option_intro' => '--',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_articles']['garants'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'garants',
		    'label' => 'Autres garants',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_articles']['extra'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'label' => 'Extra',
		    'cols' => '40',
		    'rows' => '5',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'nom' => 'extra',
		  ),
		);

	$champs['spip_articles']['id_programme'] = array (
		  'saisie' => 'programme_recherche',
		  'options' => 
		  array (
		    'nom' => 'id_programme',
		    'label' => 'Programme de recherche',
		    'restrictions' => 
		    array (
		      'secteurs' => '250:262:247:251:3:271:267:270',
// 		      'voir' => 
// 		      array (
// 		        'auteur' => 'webmestre',
// 		      ),
// 		      'modifier' => 
// 		      array (
// 		        'auteur' => 'webmestre',
// 		      ),
		    ),
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		    'versionner' => 'on',
		  ),
		);


	// Table : spip_auteurs
	if (!is_array($champs['spip_auteurs'])) {
		$champs['spip_auteurs'] = array();
	}

	$champs['spip_auteurs']['prenom'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'prenom',
		    'label' => 'Prénom',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['nom_famille'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'nom_famille',
		    'label' => 'Nom de famille',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['statut_fonction'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'nom' => 'statut_fonction',
		    'label' => 'Statut/fonctions',
		    'rows' => '4',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_RACCOURCIS',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['publications'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'nom' => 'publications',
		    'label' => 'Principales publications',
		    'explication' => 'Publications ne figurant pas dans le site lui-même, qui ne seront pas automatiquement listées à partir du contenu du site.',
		    'rows' => '20',
		    'cols' => '40',
		    'inserer_barre' => 'edition',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_RACCOURCIS',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['membre_qualite'] = array (
		  'saisie' => 'selection',
		  'options' => 
		  array (
		    'nom' => 'membre_qualite',
		    'label' => 'Qualité de membre',
		    'datas' => 
		    array (
		      'membre' => 'Membre',
		      'membre_associe' => 'Membre associé',
		      'doctorant' => 'Doctorant(e)',
		    ),
		    'restrictions' => 
		    array (
		      'voir' => 
		      array (
		        'auteur' => 'webmestre',
		      ),
		      'modifier' => 
		      array (
		        'auteur' => 'webmestre',
		      ),
		    ),
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_auteurs']['role'] = array (
		  'saisie' => 'selection',
		  'options' => 
		  array (
		    'nom' => 'role',
		    'label' => 'Rôle',
		    'datas' => 
		    array (
		      'directeur_recherche' => 'Directeur de recherche (DR)',
		      'charge_recherche' => 'Chargés de recherche (CR)',
		      'doctorant' => 'Doctorant(e)',
		      'post_doctorant' => 'Postdoctorant(e)',
		      'cdd_doctorant' => 'CDD Doctorant',
		      'docteur' => 'Docteur(e)',
		      'charge_cours' => 'Chargé(e) de cours',
		      'professeur' => 'Professeur (PR)',
		      'prof_emerite' => 'Professeurs émérites (PREM)',
		      'maitre_conference' => 'Maîtres de conférence (MCF)',
		      'enseignant' => 'Enseignant(e)',
		      'chercheur' => 'Chercheur',
		      'ecrivain' => 'Écrivain',
		      '10_secretaire_gestionnaire' => 'Secrétariat et gestion',
		      '20_communication' => 'Information, diffusion et médias',
		      '30_Edition' => 'Conception graphique et éditoriale, site web',
		      16 => 'Ressources documentaires',
		    ),
		    'restrictions' => 
		    array (
		      'voir' => 
		      array (
		        'auteur' => 'webmestre',
		      ),
		      'modifier' => 
		      array (
		        'auteur' => 'webmestre',
		      ),
		    ),
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['fonction_complement'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'fonction_complement',
		    'label' => 'Complément pour la fonction',
		    'explication' => 'Émérite...',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'restrictions' => 
		    array (
		      'voir' => 
		      array (
		        'auteur' => 'webmestre',
		      ),
		      'modifier' => 
		      array (
		        'auteur' => 'webmestre',
		      ),
		    ),
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_TYPO',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['these_titre'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'these_titre',
		    'label' => 'Titre de la thèse',
		    'type' => 'text',
		    'size' => '15',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['these_directeur'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'these_directeur',
		    'label' => 'Directeur de thèse Thalim',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['these_directeur_autre'] = array (
		  'saisie' => 'input',
		  'options' => 
		  array (
		    'nom' => 'these_directeur_autre',
		    'label' => 'Autres directeurs de Thèse',
		    'type' => 'text',
		    'size' => '40',
		    'autocomplete' => 'defaut',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	$champs['spip_auteurs']['these_resume'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'nom' => 'these_resume',
		    'label' => 'Résumé de la thèse',
		    'rows' => '10',
		    'cols' => '40',
		    'inserer_barre' => 'edition',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_RACCOURCIS',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['domaines_recherche'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'nom' => 'domaines_recherche',
		    'label' => 'Domaines de recherche',
		    'rows' => '10',
		    'cols' => '40',
		    'inserer_barre' => 'edition',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_RACCOURCIS',
		    'versionner' => 'on',
		  ),
		);

	$champs['spip_auteurs']['recherches_en_cours'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'nom' => 'recherches_en_cours',
		    'label' => 'Recherches en cours',
		    'rows' => '10',
		    'cols' => '40',
		    'inserer_barre' => 'edition',
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher' => 'on',
		    'rechercher_ponderation' => '2',
		    'traitements' => '_TRAITEMENT_RACCOURCIS',
		    'versionner' => 'on',
		  ),
		);


	// Table : spip_mots
	if (!is_array($champs['spip_mots'])) {
		$champs['spip_mots'] = array();
	}

	$champs['spip_mots']['extra'] = array (
		  'saisie' => 'textarea',
		  'options' => 
		  array (
		    'label' => 'Extra',
		    'cols' => 40,
		    'rows' => 5,
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'nom' => 'extra',
		  ),
		);


	// Table : spip_documents
	if (!is_array($champs['spip_documents'])) {
		$champs['spip_documents'] = array();
	}

	$champs['spip_documents']['document_type'] = array (
		  'saisie' => 'selection',
		  'options' => 
		  array (
		    'nom' => 'document_type',
		    'label' => 'Type de document',
		    'datas' => 
		    array (
		      0 => ' -',
		      'affiche' => 'Affiche',
		      'programme' => 'Programme',
		      'flyer' => 'Flyer',
		      'invitation' => 'Invitation',
		      'bibliographie' => 'Bibliographie',
		      'appel' => 'Appel à contribution',
		      'couverture' => 'Couverture',
		      'sommaire' => 'Sommaire',
		      'presentation_publication' => 'Présentation de la publication',
		      'resumes' => 'Résumés',
		      'communication' => 'Communication',
		      'article' => 'Article',
		      'autres' => 'Autres (illustration...)',
		      'cv' => 'C.V.',
		    ),
		    'sql' => 'text DEFAULT \'\' NOT NULL',
		    'rechercher_ponderation' => '2',
		  ),
		);

	return $champs;
}