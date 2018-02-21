<?php
/**
 * Plugin Abomailmanss
 * (c) 2009-2011 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/abomailmans');
include_spip('inc/distant');

/**
 * Les abomailmans de chaque liste peuvent se faire par cron
 * base sur les champs remplis de chaque liste
 * automatique tout les /periodicite/ jours
 * @param unknown_type $time
 */
function genie_abomailmans_envois_dist($time) {
    
    /**
     * Les listes dont la date_envoi < maintenant+periodicite
     * pour tester on peut mettre a MINUTE penser a remettre a DAY !!
     */
    /* $where = "periodicite!='' AND desactive='0' AND email!=''
        AND date_envoi < DATE_SUB(NOW(), INTERVAL periodicite MINUTE)"; 
    $id_liste = sql_getfetsel("id_abomailman", "spip_abomailmans", $where, '', "date_envoi", "1"); */
    
    // périodicité revue!
    $where = "`periodicite` NOT LIKE '' AND desactive='0' AND email!='' AND ((ABS(MONTH(NOW())-MONTH(`date_envoi`))>=`periodicite` AND DAY(NOW())=30) OR (MONTH(NOW())=2 AND DAY(NOW())=28))";
    $id_liste = sql_getfetsel("id_abomailman", "spip_abomailmans", $where, '', "date_envoi", "1");


    if ($id_liste) {
        spip_log("il faut traiter liste id=$id_liste", "abomailmans");
        $res2 = liste_a_jour($id_liste);
    } else $res2 = true;
    
    /**
     * nul, si la tache n'a rien a faire
     * positif, si la tache a ete traitee
     * negatif, si la tache a commence, mais doit se poursuivre. 
     * Cela permet d'effectuer des taches par lots (pour eviter des timeout sur les executions des scripts PHP 
     * a cause de traitements trop longs).
     * Dans ce cas la, le nombre negatif indique correspond au nombre de secondes d'intervalle 
     * pour la prochaine execution.
     */
    return ($res1 OR $res2) ? 0 : $id_liste;
}     
    
    

function liste_a_jour($id_liste) {
    date_default_timezone_set("Europe/Paris");
    setlocale(LC_TIME, 'fr_FR');

    $envoi_ok=true;
    $t = sql_fetsel("*", "spip_abomailmans", "id_abomailman=$id_liste");
    if (!$t) {
        spip_log("requete null ...","abomailmans");
        return;
    } else spip_log("envoi teste avec cron abomailmans", "abomailmans");
        
    $datas = array();
    $nom_site = lire_meta("nom_site");
    $email_webmaster = lire_meta("email_webmaster");
    $charset = lire_meta('charset');


    $sujet=$t['titre'];
    if (substr($sujet, 0, 7) == "#thalim") {
        $startDate = explode('_', $sujet);
        $newSubj = "";
        foreach ($startDate as $tok) {
            switch ($tok) {
                case '#thalim':
                    break;
                case 'thisMonth':
                    $newSubj .= ucfirst(strftime("%B", time()));
                    break;
                case 'nextMonth':
                    $tmpTime = (strftime("%d", time()) >= 28) ? time() + (7 * 24 * 60 * 60) : time() + (30 * 24 * 60 * 60);
                    $newSubj .= ucfirst(strftime("%B", $tmpTime));
                    break;
                case 'twoMonth':
                    $tmpTime = (strftime("%d", time()) >= 28) ? time() + (7 * 24 * 60 * 60) + (30 * 24 * 60 * 60) : time() + (30 * 24 * 60 * 60) + (30 * 24 * 60 * 60);
                    $newSubj .= ucfirst(strftime("%B", $tmpTime));
                    break;
                case 'thisYear':
                    $newSubj .= strftime("%Y", time());
                    break;
                case 'yearNextMonth':
                    $tmpTime = (strftime("%d", time()) >= 28) ? time() + (7 * 24 * 60 * 60) : time() + (30 * 24 * 60 * 60);
                    $newSubj .= strftime("%Y", $tmpTime);
                    break;
                case 'yearTwoMonth':
                    $tmpTime = (strftime("%d", time()) >= 28) ? time() + (7 * 24 * 60 * 60) + (30 * 24 * 60 * 60) : time() + (30 * 24 * 60 * 60) + (30 * 24 * 60 * 60);
                    $newSubj .= strftime("%Y", $tmpTime);
                    break;
                case 'yearIfDifferent':
                    $tmpTime = (strftime("%d", time()) >= 28) ? time() + (7 * 24 * 60 * 60) : time() + (30 * 24 * 60 * 60);
                    $tmpTime2 = (strftime("%d", time()) >= 28) ? time() + (7 * 24 * 60 * 60) + (30 * 24 * 60 * 60) : time() + (30 * 24 * 60 * 60) + (30 * 24 * 60 * 60);
                    if (strftime("%Y", $tmpTime) != strftime("%Y", $tmpTime2)) {
                        $newSubj .= " ".strftime("%Y", $tmpTime);
                    }
                    break;
                default:
                    $newSubj .= $tok;
                    break;
            }
        }
        $sujet = $newSubj;
    }
    $date_envoi=$t['date_envoi']; 
    $email_receipt=$t['email'];
    $modele_defaut=$t['modele_defaut'];
    
    $recuptemplate = explode('&', $modele_defaut);
        
    include_spip('abomailmans_fonctions');
    $paramplus = recup_param($modele_defaut); //pour url
    $periodicite=intval($t['periodicite']);

    /**
     * la page a envoyer doit etre testee a maintenant moins periodicite
     */
    // $time = time() - (3600 * 24 * $periodicite);
    $time = time() - (60 * $periodicite); // minutes for test
    
    /**
     * construction du query
     */
    parse_str($paramplus, $query);
    $query['id_abomailman'] = $t['id_abomailman'];
    $query['template'] = $recuptemplate[0];
    $query['date'] = date('Y-m-d H:i:s', $time);

    /**
     * on peut verifier le fond grace à l'url
     */
    $url_genere = generer_url_public('abomailman_template',$query,'&'); 
    $fond = recuperer_fond('abomailman_template',$query);

    $body = array(
        'html'=>$fond,
    ); 
    /* Format Texte */
    $query['envoi_txt'] = "oui";
    $body['texte'] = recuperer_fond('abomailman_template',$query);

    //Si la page renvoie un contenu
    if (strlen($fond) > 10) {
                
        // email denvoi depuis config facteur
        if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
            AND $GLOBALS['meta']['facteur_adresse_envoi_email'])
            $from_email = $GLOBALS['meta']['facteur_adresse_envoi_email'];
        else
            $from_email = $email_webmaster;
        // nom denvoi depuis config facteur
        if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
            AND $GLOBALS['meta']['facteur_adresse_envoi_nom'])
            $from_nom = $GLOBALS['meta']['facteur_adresse_envoi_nom'];
        else
            $from_nom = $nom_site;
                
        if (abomailman_mail($from_nom, $from_email, "", $email_receipt, $sujet, $body, true, $charset)) {
            spip_log("envoi ok = $url_genere tous les $periodicite jours sujet =".$sujet,"abomailmans");
        } else {
            spip_log("!! envoi nok = $url_genere tous les $periodicite jours sujet =".$sujet,"abomailmans."._LOG_ERREUR);
            $envoi_ok=false;
        }
    }
    else {
        spip_log("maintenant=".date('Y-m-d H:i:s', time())." date demande = ".$query['date']." non envoye =$url_genere : rien de neuf depuis $periodicite jours", "abomailmans"); 
    }
    
    if($envoi_ok) {
        // Noter que l'envoi est OK meme si envoi echoue faute de contenu, on reessaiera dans /periodicite/ jours
        sql_updateq("spip_abomailmans", array("date_envoi" => date('Y-m-d H:i:s', time())), "id_abomailman=".$t['id_abomailman']);
    }
    return false; # c'est bon
}

?>
