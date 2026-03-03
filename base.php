<?php

require_once 'base.civix.php';

use CRM_Base_ExtensionUtil as E;

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function base_civicrm_install(): void {
  _base_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function base_civicrm_enable(): void {
  _base_civix_civicrm_enable();
}

function base_civicrm_config(&$config) {

    $extdebug = 0;

//  CRM_Core_Smarty::singleton()->registerPlugin('modifier', 'strtotime', 'strip_tags', 'truncate');
//  CRM_Core_Smarty::singleton()->registerPlugin('modifier', 'truncate');

    //  if (is_array($smarty->security_settings)) {
//          array_push($smarty->security_settings['MODIFIER_FUNCS'], 'lower', 'upper', 'strtotime', 'ltrim');
//          array_push($smarty->security_settings['MODIFIER_FUNCS'], 'ltrim');
    //  }

    //      $smarty = CRM_Core_Smarty::singleton();
    //  allow the explode() php function to be used as a "modifier" in Smarty templates
	//  array_push($smarty->security_settings['MODIFIER_FUNCS'], 'lower', 'upper', 'strtotime', 'ltrim', 'trim', 'truncate');
    //  array_push($smarty->security_settings['MODIFIER_FUNCS'], 'lower', 'upper', 'strtotime', 'ltrim', 'trim', 'truncate');
    // 	array_push($smarty->security_settings['MODIFIER_FUNCS'], 'lower', 'upper', 'strtotime', 'ltrim', 'trim', 'truncate');

/*
  // Smarty custom email based template policy:
  $smarty_security_policy = array(
     'mail' => array(
         'php_functions' => array(
             'strstr',
             'ltrim',
         ),
     ),
  );
*/


}

function addPhpModifiers(&$smarty)
    {

        $functions = array(
            'array_key_exists',
            'ceil',
            'count',
            'date',
            'explode',
            'floatval',
            'htmlentities',
            'md5',
            'in_array',
            'ip2long',
            'is_array',
            'is_numeric',
            'intval',
            'json_encode',
            'mb_strtoupper',
            'md',
            'nl2br',
            'number_format',
            'round',
            'strip_tags',
            'stripslashes',
            'strlen',
            'strstr',
            'strtolower',
            'strtotime',
            'strtoupper',
            'trim',
            'urlencode',
        );

        foreach($functions as &$f) {
            $smarty->registerPlugin('modifier', $f, $f);
        }

    }

function wachthond(int $extdebug, int $severity, string $message, $arrayvalue = null) {

    // 0. Override check: Forceer weergave als de globale variabele 'force_watchdog' op true staat
    $force_all = isset($GLOBALS['force_watchdog']) && $GLOBALS['force_watchdog'] === true;

    // 1. Snelle exit checks (Debug level filters)
    $debuglevel = intval($extdebug);
    
    // Mapping van severity naar gedrag
    $debug_map = [
        7 => 0, // params (0 = off, 1 = on)
        8 => 1, // notice
        9 => 0  // result
    ];

    // Alleen filteren als de override NIET actief is
    if (!$force_all) {
        if (isset($debug_map[$severity]) && $debug_map[$severity] === 0) {
            return;
        }
        $writelog = ($severity <= $debuglevel || (isset($debug_map[$severity]) && $debug_map[$severity] === 1 && $debuglevel >= 1));

        if (!$writelog) {
            return;
        }
    }

    // 2. Severity bepalen (Gebruik de Drupal constanten, geen strings)
    $drupal_severity = ($severity == 8) ? WATCHDOG_NOTICE : WATCHDOG_DEBUG;

    // 3. Uitlijning berekenen (Tabs/Spaces)
    $tabshort   = 56;
    $tablong    = 72;
    $message    = trim($message);
    $length     = strlen($message);
    
    $tabtarget  = ($length <= $tabshort) ? $tabshort : $tablong;
    $fillspacelength = max(0, $tabtarget - $length);
    
    // Genereer HTML spaces voor de uitlijning in de Drupal log viewer
    $spaces     = str_repeat("&nbsp;", $fillspacelength);

    // 4. Data voorbereiden (PHP 8 veilig maken)
    // Als het '0' is of een gevulde waarde, tonen we de divider
    $divider    = (empty($arrayvalue) && $arrayvalue !== 0 && $arrayvalue !== '0') ? "" : " : ";
    
    // Zorg dat $arrayvalue altijd veilig geprint kan worden
    // print_r($var, true) is veilig, maar we trimmen het voor de netheid
    $formatted_value = print_r($arrayvalue, true);

    // 5. Schrijf naar Drupal Watchdog
    // We gebruiken <pre> zodat de &nbsp; en line-breaks goed overkomen
    watchdog('php', "<pre>$message$spaces$divider$formatted_value</pre>", NULL, $drupal_severity);
}

function wachthond_org (int $extdebug, int $severity, string $message, $arrayvalue = null) {

  $debuglevel     = intval($extdebug);
  //  $debuglevel     = 2; // 0 - 5 
  $debugparams    = 0; // 0 or 1
  $debugresult    = 0; // 0 or 1
  $debugnotice    = 1; // 0 or 1

  if ($severity == 7 AND $debugparams == 0) {
    return;
  }
  if ($severity == 9 AND $debugresult == 0) {
    return;
  }
  if ($severity == 8 AND $debugnotice == 0) {
    return;
  }

  if ($severity <= $debuglevel) {
    $writelog = 1;
  } else {
    $writelog = 0;
  }

  if ($severity == 7  AND $debugparams == 1 AND $debuglevel >= 1) {
    $writelog = 1;
  }
  if ($severity == 9  AND $debugresult == 1 AND $debuglevel >= 1) {
    $writelog = 1;
  }
  if ($severity == 8  AND $debugnotice == 1 AND $debuglevel >= 1) {
    $writelog = 1;
  }

  if ($severity == 8) {
    $wachthond_severity = 'WATCHDOG_NOTICE';
  } else {
    $wachthond_severity = 'WATCHDOG_DEBUG';
  }

  $tabshort       = 56;
  $tablong        = 72;

  $message        = trim($message);
  $messagelenght  = strlen($message);

  if ($messagelenght <= $tabshort) {
    $tabtarget = $tabshort;
  }
  if ($messagelenght  > $tabshort) {
    $tabtarget = $tablong;
  }

  $fillspacelenght  = ($tabtarget - $messagelenght);

  ######### SOLUTION WITH SPACES ###############

    $space1 = " ";
    $space2 = "  ";
    $space3 = "   ";
    $space4 = "    ";
    $space5 = "     ";  
    $space6 = "      "; 
    $space7 = "       ";  
    $space8 = "        "; 
    $space9 = "         ";  

    if ($fillspacelenght > 0) {
      $spaces = str_repeat("&nbsp;", $fillspacelenght);
  } else {
    $spaces = NULL;
  }

  ######### SOLUTION WITH TABS ###############

  $tabsrequired_dec = (($fillspacelenght) / 4);
  $tabsrequired_rnd = CEIL($tabsrequired_dec);

  $fulltabs         = ($tabsrequired_rnd-1);
  $tabspacelenght   = (($tabsrequired_rnd-1) * 4);
  $spacesleft       = $fillspacelenght - $tabspacelenght;
  $totallenght      = $messagelenght + $tabspacelenght + $spacesleft;

  $tab  = "\t";
  $tab1 = "\t";
  $tab2 = "\t\t";
  $tab3 = "\t\t\t";
  $tab4 = "\t\t\t\t";
  $tab5 = "\t\t\t\t\t";

  if ($tabsrequired_rnd == 1) { $tab = $tab1; }
  if ($tabsrequired_rnd == 2) { $tab = $tab2; }
  if ($tabsrequired_rnd == 3) { $tab = $tab3; }
  if ($tabsrequired_rnd == 4) { $tab = $tab4; }
  if ($tabsrequired_rnd == 5) { $tab = $tab5; }

  ######### CONFIGURE DIVIDER ###############

  if (empty($arrayvalue)) {
    $wachthonddivider = NULL;
  } else {
    $wachthonddivider = ":";
  }
  if ($arrayvalue == '0') {
    $wachthonddivider = ":";    
  }

/*
  wachthond($extdebug, 1, 'messagelenght',    $messagelenght);
  wachthond($extdebug, 1, 'fillspacelenght',  $fillspacelenght);
  wachthond($extdebug, 1, 'tabsrequired_dec', $tabsrequired_dec);
  wachthond($extdebug, 1, 'tabsrequired_rnd', $tabsrequired_rnd);
  wachthond($extdebug, 1, 'spacesleft',       $spacesleft);
*/

  ######### WRITE TO SYSLOG ###############

  if ($severity == 8) {
    $wachthond_severity = 'WATCHDOG_NOTICE';
  } else {
    $wachthond_severity = 'WATCHDOG_DEBUG';
  }


  if ($writelog == 1 AND $severity == 8) {

//  watchdog('php', "<pre>$message $spaces $wachthonddivider ".print_r($arrayvalue,TRUE)." ($fillspacelenght)</pre>",NULL,WATCHDOG_DEBUG);
//  watchdog('php', "<pre>$message $spaces $wachthonddivider ".print_r($arrayvalue,TRUE)."</pre>",NULL, $wachthond_severity);
    watchdog('php', "<pre>$message $spaces $wachthonddivider ".print_r($arrayvalue,TRUE)."</pre>",NULL,WATCHDOG_NOTICE);
  }

  if ($writelog == 1 AND $severity != 8) {
    watchdog('php', "<pre>$message $spaces $wachthonddivider ".print_r($arrayvalue,TRUE)."</pre>",NULL,WATCHDOG_DEBUG);    
  }


}

/**
 * Centrale configuratie voor Event Types.
 * Vervangt de losse arrays in 5 verschillende functies.
 */
function get_event_types() {

    static $event_type_cache = NULL;

    // Als de cache gevuld is, direct teruggeven.
    if ($event_type_cache !== NULL) {
        return $event_type_cache;
    }

    // 1. De Basis Definities
    $types = [
        'deel'     => [11, 12, 13, 14, 21, 22, 23, 24, 33], // Deelnemers (Kampen + Topkamp)
        'deeltop'  => [33],                                 // Specifiek Topkamp
        'leid'     => [1],                                  // Leiding (Kader)
        'meet'     => [2],                                  // Kampstaf / Meetup
        'toer'     => [3],                                  // Toerusting
        'deeltest' => [102],                                // Test Deelnemer
        'leidtest' => [101],                                // Test Leiding
        'toptest'  => [103],                                // Test Top
    ];
    
    // 2. De Samengestelde Lijsten (Automatisch gegenereerd)
    // We gebruiken array_unique omdat sommige ID's in meerdere basisgroepen kunnen zitten.
    
    // Productie (Alles behalve test)
    $types['prod']     = array_unique(array_merge($types['deel'], $types['deeltop'], $types['leid']));
    
    // Test (Alles wat test is)
    $types['test']     = array_unique(array_merge($types['deeltest'], $types['leidtest'], $types['toptest']));
    
    // Alles (Productie + Test)
    $types['all']      = array_unique(array_merge($types['prod'], $types['test']));
    
    // Alle Deelnemers (Prod + Test + Top)
    $types['deel_all'] = array_unique(array_merge($types['deel'], $types['deeltop'], $types['deeltest'], $types['toptest']));
    
    // Alle Leiding (Prod + Test + Staf)
    $types['leid_all'] = array_unique(array_merge($types['leid'], $types['leidtest'], $types['meet']));

    // Sla het resultaat op in de statische variabele voor de volgende aanroep.
    $event_type_cache = $types;

    return $types;
}

function find_fiscalyear() {

    // 0. Statische cache
    static $static_fiscal_cache = null;

    if ($static_fiscal_cache !== null) {
        return $static_fiscal_cache;
    }

    $extdebug   = 0; 
    $cache      = Civi::cache();
    $cache_key  = 'intake_bundle';

    // 1. Probeer Civi-cache
    $stored_bundle = $cache->get($cache_key);
    if ($stored_bundle && is_array($stored_bundle)) {
        $static_fiscal_cache = $stored_bundle;
        return $static_fiscal_cache;
    }

    // 2. Cache miss: Berekenen
    wachthond($extdebug, 1, "### BEPAAL BASISWAARDEN (CACHE MISS)", "[START]");

    $today_obj              = new DateTime(); 
    $today_datetime         = $today_obj->format("Y-m-d H:i:s");
    
    $today_fiscalyear       = curriculum_civicrm_fiscalyear($today_obj);
    $today_fiscalyear_start = $today_fiscalyear['fiscalyear_start'] ?? NULL;
    $today_fiscalyear_einde = $today_fiscalyear['fiscalyear_einde'] ?? NULL;
    
    // Kampjaar
    $start_obj              = new DateTime($today_fiscalyear_start);
    $today_kampjaar         = (clone $start_obj)->modify('+6 months')->format('Y');

    // --- BEREKEN DAGEN TOT EINDE ---
    $einde_obj              = new DateTime($today_fiscalyear_einde);
    $diff                   = $today_obj->diff($einde_obj);
    $daysuntil_fyeinde      = (int)$diff->format('%a'); // <--- De nieuwe naam

    // Volgend jaar
    $next_obj               = (clone $today_obj)->modify('+1 year');
    $nexty_fiscalyear       = curriculum_civicrm_fiscalyear($next_obj);
    $nexty_fiscalyear_start = $nexty_fiscalyear['fiscalyear_start'] ?? NULL;
    $nexty_fiscalyear_einde = $nexty_fiscalyear['fiscalyear_einde'] ?? NULL;
    $next_start_obj         = new DateTime($nexty_fiscalyear_start);
    $nexty_kampjaar         = (clone $next_start_obj)->modify('+6 months')->format('Y');

    // Grenzen
    $grensvognoggoed1       = $today_fiscalyear_start;
    $grensrefnoggoed1       = $today_fiscalyear_start;
    $grensvognoggoed3       = (clone $start_obj)->modify('-2 years')->format('Y-m-d');
    $grensrefnoggoed3       = $grensvognoggoed3;   

    // 3. Vul array
    $static_fiscal_cache    = [
        'today_date'         => $today_datetime,
        'today_start'        => $today_fiscalyear_start,
        'today_einde'        => $today_fiscalyear_einde,
        'today_jaar'         => $today_kampjaar,
        'daysuntil_fyeinde'  => $daysuntil_fyeinde, // <--- Opgeslagen onder nieuwe key
        
        'nexty_start'        => $nexty_fiscalyear_start,
        'nexty_einde'        => $nexty_fiscalyear_einde,
        'nexty_jaar'         => $nexty_kampjaar,
        
        'refnoggoed1'        => $grensrefnoggoed1,
        'vognoggoed1'        => $grensvognoggoed1,
        'vognoggoed3'        => $grensvognoggoed3,
        'refnoggoed3'        => $grensrefnoggoed3,
        'noggoed1'           => $grensvognoggoed1,
        'noggoed3'           => $grensvognoggoed3,
    ];

    // 4. Save & Return
    $cache->set($cache_key, $static_fiscal_cache);
    wachthond($extdebug, 1, "### BEPAAL BASISWAARDEN (CACHE SET)", "Days left: $daysuntil_fyeinde");

    return $static_fiscal_cache;
}

/**
 * Haalt de datums op van het vorige en volgende kamp.
 * Gebruikt interne static cache voor snelheid en Civi cache voor persistentie.
 */
function find_lastnext($referencedate = NULL) {

    $extdebug = 0;
    $apidebug = FALSE;

    // --- 0. STATIC CACHE (Directe winst binnen dezelfde paginaload) ---
    static $lastnext_cache = [];
    $ref_key = $referencedate ?? 'today';
    if (isset($lastnext_cache[$ref_key])) return $lastnext_cache[$ref_key];

    // --- 1. DATUM PREPARATIE ---
    $today_datetime      = date("Y-m-d H:i:s");
    $referencedate       = $referencedate ?? $today_datetime;
    
    $referencedate_past  = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($referencedate)));
    $referencedate_next  = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($referencedate)));

    // --- 2. CACHE CHECK (CiviCRM Persistent Cache) ---
    $cache_results = Civi::cache()->get('cache_refdate_lastnext');

    if ($cache_results && $referencedate == $today_datetime) {
        wachthond($extdebug, 1, "### [FROM CACHE] find_lastnext data geladen", "[CACHE]");
        $lastnext_cache[$ref_key] = $cache_results;
        return $cache_results;
    }

    // --- 3. PARAMETERS DEFINIËREN ---
    $event_types  = get_event_types();
    $target_types = $event_types['deel']; 

    wachthond($extdebug, 2, "########################################################################");
    wachthond($extdebug, 1, "### OPZOEKEN LAST/NEXT EVENTS VOOR: $referencedate", "[CALC]");
    wachthond($extdebug, 2, "########################################################################");

    // --- 4. QUERY LAST EVENT ---
    $params_last = [
        'checkPermissions' => FALSE,
        'limit'            => 1,
        'select'           => ['start_date', 'end_date'],
        'where'            => [
            ['start_date', '>', $referencedate_past],
            ['start_date', '<', $referencedate],
            ['event_type_id', 'IN', $target_types],
        ],
        'orderBy'          => ['start_date' => 'DESC'],
    ];
    $result_last = civicrm_api4('Event', 'get', $params_last);

    // --- 5. QUERY NEXT EVENT ---
    $params_next = [
        'checkPermissions' => FALSE,
        'limit'            => 1,
        'select'           => ['start_date', 'end_date'],
        'where'            => [
            ['start_date', '>', $referencedate],
            ['start_date', '<', $referencedate_next],
            ['event_type_id', 'IN', $target_types],
        ],
        'orderBy'          => ['start_date' => 'ASC'],
    ];
    $result_next = civicrm_api4('Event', 'get', $params_next);

    // --- 6. RESULTATEN VERWERKEN ---
    $last_event_start_date = $result_last[0]['start_date'] ?? NULL;
    $last_event_einde_date = $result_last[0]['end_date']   ?? NULL;
    $last_event_einde_year = $last_event_einde_date ? date('Y', strtotime($last_event_einde_date)) : NULL;

    $next_event_start_date = $result_next[0]['start_date'] ?? NULL;
    $next_event_einde_date = $result_next[0]['end_date']   ?? NULL;
    $next_event_start_year = $next_event_start_date ? date('Y', strtotime($next_event_start_date)) : NULL;

    $refdate_lastnext_array = [
        'reference_date'  => $referencedate,
        'last_start_date' => $last_event_start_date,
        'last_einde_date' => $last_event_einde_date,
        'last_einde_year' => $last_event_einde_year,
        'next_start_date' => $next_event_start_date,
        'next_einde_date' => $next_event_einde_date,
        'next_start_year' => $next_event_start_year,
    ];

    // --- 7. DEBUG & CACHE WEGSCHRIJVEN ---
    wachthond($extdebug, 2, "########################################################################");
    wachthond($extdebug, 2, 'last_event_einde_date', "[NAAR CACHE: $last_event_einde_date]");
    wachthond($extdebug, 2, 'last_event_einde_year', "[NAAR CACHE: $last_event_einde_year]");
    wachthond($extdebug, 2, 'next_event_start_date', "[NAAR CACHE: $next_event_start_date]");
    wachthond($extdebug, 2, 'next_event_start_year', "[NAAR CACHE: $next_event_start_year]");
    wachthond($extdebug, 3, "########################################################################");

    if ($referencedate == $today_datetime) {
        Civi::cache()->set('cache_refdate_lastnext', $refdate_lastnext_array, 3600);
    }

    $lastnext_cache[$ref_key] = $refdate_lastnext_array;

    return $refdate_lastnext_array;
}

/**
 * Haalt de datums op van het vorige en volgende kamp waar deze SPECIFIEKE contactpersoon aan deelnam.
 */
function find_lastnext_part($contactid, $referencedate = NULL) {

    $extdebug = 0;
    $apidebug = FALSE;

    // --- 0. STATIC CACHE (Snelheid bij herhaalde aanroep) ---
    static $part_lastnext_cache = [];
    $cache_key = $contactid . '_' . ($referencedate ?? 'today');
    if (isset($part_lastnext_cache[$cache_key])) return $part_lastnext_cache[$cache_key];

    // --- 1. CONFIGURATIE & DATUMS ---
    $event_types     = get_event_types(); // Gebruik je centrale bibiliotheek!
    $eventtypesall   = $event_types['all'];
    $today_datetime  = date("Y-m-d H:i:s");
    $referencedate   = $referencedate ?? $today_datetime;

    $referencedate_past = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($referencedate)));
    $referencedate_next = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($referencedate)));

    wachthond($extdebug, 2, "########################################################################");
    wachthond($extdebug, 1, "### OPZOEKEN DEELNAME LAST/NEXT VOOR CID: $contactid", "[PART]");
    wachthond($extdebug, 2, "########################################################################");

    // --- 2. QUERY LAST PARTICIPATION ---
    $params_last = [
        'checkPermissions' => FALSE,
        'limit'            => 1,
        'select'           => ['PART.PART_kampeinde'],
        'where'            => [
            ['contact_id',              '=',  $contactid],
            ['event_id.start_date',     '>',  $referencedate_past],
            ['event_id.start_date',     '<',  $referencedate],
            ['event_id.event_type_id',  'IN', $eventtypesall],
            ['is_test',                 'IN', [TRUE, FALSE]], 
        ],
        'orderBy'          => ['event_id.start_date' => 'DESC'],
    ];
    $result_last = civicrm_api4('Participant', 'get', $params_last);

    // --- 3. QUERY NEXT PARTICIPATION ---
    $params_next = [
        'checkPermissions' => FALSE,
        'limit'            => 1,
        'select'           => ['PART.PART_kampstart'],
        'where'            => [
            ['contact_id',              '=',  $contactid],
            ['event_id.start_date',     '>',  $referencedate],
            ['event_id.start_date',     '<',  $referencedate_next],
            ['event_id.event_type_id',  'IN', $eventtypesall],
            ['is_test',                 'IN', [TRUE, FALSE]], 
        ],
        'orderBy'          => ['event_id.start_date' => 'ASC'],
    ];
    $result_next = civicrm_api4('Participant', 'get', $params_next);

    // --- 4. RESULTATEN VERWERKEN ---
    $last_event_einde_date = $result_last[0]['PART.PART_kampeinde'] ?? NULL;
    $last_event_einde_year = $last_event_einde_date ? date('Y', strtotime($last_event_einde_date)) : NULL;

    $next_event_start_date = $result_next[0]['PART.PART_kampstart'] ?? NULL;
    $next_event_start_year = $next_event_start_date ? date('Y', strtotime($next_event_start_date)) : NULL;

    // --- 5. DEFAULT FALLBACK (Als er geen volgende deelname is gevonden) ---
    if (!$next_event_start_date) {
        $d = new DateTime(date('Y').'-08-01 16:00:00');
        if ($d < new DateTime()) {
            $d->modify('+1 year');
        }
        $next_event_start_date = $d->format('Y-m-d H:i:s');
        $next_event_start_year = $d->format('Y');
    }

    $refdate_lastnext_array = [
        'reference_date'  => $referencedate,
        'last_einde_date' => $last_event_einde_date,
        'last_einde_year' => $last_event_einde_year,
        'next_start_date' => $next_event_start_date,
        'next_start_year' => $next_event_start_year,
    ];

    // --- 6. DEBUG LOGGING ---
    wachthond($extdebug, 2, "########################################################################");
    wachthond($extdebug, 2, 'last_event_einde_date', "[RESULT: $last_event_einde_date]");
    wachthond($extdebug, 2, 'last_event_einde_year', "[RESULT: $last_event_einde_year]");
    wachthond($extdebug, 2, 'next_event_start_date', "[RESULT: $next_event_start_date]");
    wachthond($extdebug, 2, 'next_event_start_year', "[RESULT: $next_event_start_year]");
    wachthond($extdebug, 3, "########################################################################");

    $part_lastnext_cache[$cache_key] = $refdate_lastnext_array;

    return $refdate_lastnext_array;
}

/**
 * Haalt alle ParticipantStatusTypes op uit CiviCRM en groepeert ze per 'class'.
 * * Deze functie maakt gebruik van CiviCRM caching. Als de data al eerder is opgehaald,
 * wordt de database niet belast.
 * * @return array Geeft een array terug in het formaat:
 * ['ids' => [
 * 'Positive' => [1, 2, ...],
 * 'Pending'  => [3, ...],
 * 'Waiting'  => [4, ...],
 * 'Negative' => [5, ...]
 * ]]
 */
function find_partstatus() {
    // =========================================================================
    // STAP 1: STATIC MEMORY CACHE (Snelst)
    // =========================================================================
    // We slaan de data op in een statische variabele. Dit overleeft zolang het script draait.
    // Hiermee lossen we de bug op van de "$posneg" variabele die niet bestond.
    static $memory_cache = null;

    if ($memory_cache !== null) {
        return $memory_cache;
    }

    $extdebug = 0;          // Debug niveau
    $apidebug = FALSE;      // Zet op TRUE voor API debugging

    // =========================================================================
    // STAP 2: SESSION CACHE (Snel)
    // =========================================================================
    // We gebruiken één sleutel voor de hele georganiseerde lijst.
    // Dit scheelt 4x ophalen en 4x wegschrijven (performance winst).
    $cache_key = 'ozk_cache_partstatus_all';
    $cached_data = Civi::cache('session')->get($cache_key);

    if ($cached_data) {
        // Gevonden in session cache! Opslaan in memory cache voor de volgende aanroep.
        $memory_cache = $cached_data;
        return $cached_data;
    }

    // =========================================================================
    // STAP 3: DATABASE QUERY (Traagst - Alleen bij lege cache)
    // =========================================================================
    
    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("START find_partstatus [DB QUERY]"), NULL, WATCHDOG_DEBUG);
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,1, "### VIND GECONFIGUREERDE DEELNAME STATUSSEN","[DB QUERY - CACHE MISS]");
    wachthond($extdebug,3, "########################################################################");

    // Jouw specifieke API Format:
    $params_partstatus = [
        'select' => [
            'id', 'name', 'label', 'class'
        ],
        'where' => [
            ['class',     'IN', ['Positive', 'Pending', 'Waiting', 'Negative']],
            ['is_active', '=',  1],
        ],
        'orderBy' => [
            'weight' => 'ASC'
        ],
        'checkPermissions' => FALSE,
        'debug' => $apidebug,
    ];

    wachthond($extdebug,7, 'params_partstatus', $params_partstatus);
    $result_partstatus = civicrm_api4('ParticipantStatusType', 'get', $params_partstatus);
    wachthond($extdebug,9, 'result_partstatus', $result_partstatus);

    // =========================================================================
    // STAP 4: SORTEREN & ORGANISEREN
    // =========================================================================
    // We initialiseren de output structuur die de rest van je code verwacht.
    $output = [
        'ids' => [
            'Positive' => [],
            'Pending'  => [],
            'Waiting'  => [],
            'Negative' => [],
        ]
    ];

    // Loop door de resultaten en stop ID's in het juiste bakje
    foreach ($result_partstatus as $stat) {
        $class = $stat['class'];
        $id    = (int)$stat['id'];

        if (isset($output['ids'][$class])) {
            $output['ids'][$class][] = $id;
        }
    }

    // =========================================================================
    // STAP 5: OPSLAAN IN CACHE
    // =========================================================================
    
    // 1. Opslaan in Civi Session Cache (voor volgende page loads)
    Civi::cache('session')->set($cache_key, $output);

    // 2. Opslaan in Static Memory Cache (voor verdere aanroepen in dit script)
    $memory_cache = $output;

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("EINDE find_partstatus"), NULL, WATCHDOG_DEBUG);
    }

    return $output;
}

function find_eventids() {

    $cache = Civi::cache();
    $res = $cache->get('cache_all_event_ids_v2');
    if ($res !== NULL) {
        return $res;
    }

    $extdebug = 0; 

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("START find_eventids"), NULL, WATCHDOG_DEBUG);
    }
    
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,1, "### VIND ALLE EVENT LEIDING & DEELNEMERS VOOR DIT JAAR");
    wachthond($extdebug,3, "########################################################################");

    // 1. CACHE CHECK: Hebben we dit al berekend?
    // We slaan de hele resultaat-array op in één cache key.
    $cached_data = Civi::cache()->get('cache_all_event_ids_v2');
    
    if ($cached_data) {
        if ($extdebug) wachthond(1, "EVENT IDS HIT CACHE", "Geen DB query nodig");
        return $cached_data;
    }

    $apidebug = FALSE;
    wachthond($extdebug, 1, "### REBUILD EVENT ID CACHE ###", "Querying DB...");

    // 2. CONFIGURATIE: Alle types op een rij
    $types = [
        'deel'      => [11, 12, 13, 14, 21, 22, 23, 24, 33], // Kampen
        'deeltop'   => [33],                                 // Topkamp
        'leid'      => [1],                                  // Leiding
        'meet'      => [2],                                  // Kampstaf/Meetup
        'toer'      => [3],                                  // Toerusting
        'deeltest'  => [102],                                // Test Deelnemer
        'leidtest'  => [101],                                // Test Leiding
        'toptest'   => [103]                                 // Test Top
    ];

    // Verzamel alle unieke ID's voor de query
    $all_types_flat = [];
    foreach($types as $t) {
        $all_types_flat = array_merge($all_types_flat, $t);
    }
    $all_types_flat = array_unique($all_types_flat);

    // 3. QUERY: Haal ALLES op in één keer (veel sneller dan 8x los)
    $params = [
        'checkPermissions' => FALSE,
        'select' => ['id', 'event_type_id', 'title'],
        'where' => [
            ['event_type_id', 'IN', $all_types_flat],
            ['start_date', '=', 'this.fiscal_year'], 
        ],
    ];

    // Log de params voor de API call
    wachthond($extdebug, 2, "find_eventids: API Params", $params);

    $events = civicrm_api4('Event', 'get', $params);

    // Log het resultaat van de API call
    wachthond($extdebug, 1, "find_eventids: API Resultaat", [
        'count' => count($events),
        'sample' => $events[0] ?? 'GEEN DATA'
    ]);

    // 4. SORTEREN: Verdeel de resultaten over de bakjes
    $res = [
        'deel' => [], 'top' => [], 'leid' => [], 'meet' => [], 'toer' => [],
        'deeltest' => [], 'leidtest' => [], 'toptest' => []
    ];

    foreach ($events as $ev) {
        $eid = (int)$ev['id'];
        $tid = (int)$ev['event_type_id'];
        $ttl = $ev['title'] ?? '';

        // BELANGRIJK: Detecteer of het een test-event is o.b.v. titel
        $is_test_title = (stripos($ttl, 'TEST') !== false);

        // --- PRODUCTIE BAKJES (ALLEEN ALS GEEN TEST TITEL) ---
        if (in_array($tid, $types['deel'])      && !$is_test_title)    $res['deel'][] = $eid;
        if (in_array($tid, $types['deeltop'])   && !$is_test_title)    $res['top'][]  = $eid;
        if (in_array($tid, $types['leid'])      && !$is_test_title)    $res['leid'][] = $eid;
        if (in_array($tid, $types['meet'])      && !$is_test_title)    $res['meet'][] = $eid;
        if (in_array($tid, $types['toer'])      && !$is_test_title)    $res['toer'][] = $eid;

        // --- TEST BAKJES (TITEL MAAKT NIET UIT, TYPE IS LEIDEND) ---
        if (in_array($tid, $types['deeltest'])) $res['deeltest'][] = $eid;
        if (in_array($tid, $types['leidtest'])) $res['leidtest'][] = $eid;
        if (in_array($tid, $types['toptest']))  $res['toptest'][]  = $eid;
    }

    // 5. SAMENSTELLEN: Maak de gecombineerde lijsten
    $res['deel_top']  = array_merge($res['deel'], $res['top']);
    $res['deel_all']  = array_merge($res['deel'], $res['top'], $res['deeltest']);
    $res['leid_all']  = array_merge($res['leid'], $res['leidtest']);
    $res['deel_leid'] = array_merge($res['deel'], $res['top'], $res['leid']); 
    $res['all']       = array_merge($res['deel_all'], $res['leid_all'], $res['meet'], $res['toer']);
    $res['test_all']  = array_merge($res['deeltest'], $res['leidtest'], $res['toptest']);

    // Unieke waarden en sorteren voor de combi-lijsten
    // FIX: Gebruik array_unique i.p.v. array_flip om corruptie [0,1,2] te voorkomen
    $target_keys = ['deel_top', 'deel_all', 'leid_all', 'deel_leid', 'all', 'test_all', 'deel', 'top', 'leid', 'meet', 'toer'];
    foreach ($target_keys as $k) {
        if (isset($res[$k]) && is_array($res[$k])) {
            $res[$k] = array_unique($res[$k]);
            sort($res[$k], SORT_NUMERIC);
            $res[$k] = array_values($res[$k]); 
        }
    }

    // Laatste check op de samengestelde resultaten
    wachthond($extdebug, 1, "find_eventids: EINDRESULTAAT VOOR CACHE", [
        'totaal_uniek_all' => count($res['all']),
        'sample_all_ids'   => array_slice($res['all'], 0, 10)
    ]);

    // 6. OPSLAAN: Schrijf naar Cache
    Civi::cache()->set('cache_all_event_ids_v2', $res);

    // OPSLAAN: Oude losse cache keys voor backward compatibility
    $cache_map = [
        'cache_kampids_deel'      => $res['deel'],
        'cache_kampids_top'       => $res['top'],
        'cache_kampids_leid'      => $res['leid'],
        'cache_kampids_meet'      => $res['meet'],
        'cache_kampids_toer'      => $res['toer'],
        'cache_kampids_deel_top'  => $res['deel_top'],
        'cache_kampids_deel_all'  => $res['deel_all'],
        'cache_kampids_leid_all'  => $res['leid_all'],
        'cache_kampids_deel_leid' => $res['deel_leid'],
        'cache_kampids_all'       => $res['all'],
        'cache_kampids_test_deel' => $res['deeltest'],
        'cache_kampids_test_leid' => $res['leidtest'],
        'cache_kampids_test_all'  => $res['test_all']
    ];

    foreach ($cache_map as $key => $val) {
        Civi::cache()->set($key, $val);
    }

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("EINDE find_eventids"), NULL, WATCHDOG_DEBUG);
    }

    return $res;
}

function find_contact(string $inputtype, $input) {

    static $localCache = [];

    $key = $inputtype . ':' . $input;
    if (isset($localCache[$key])) {
        return $localCache[$key];
    }

    $extdebug = 0;  // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    if ($inputtype == NULL) {
        wachthond($extdebug,2, 'find_contact', "[NO INPUTTYPE]");
        return;
    }
    if ($inputtype == 'contactid') {
        $var_params_contact = [
            ['id',          '=', $input],
        ];
    }
    if ($inputtype == 'username') {
        $var_params_contact = [
            ['job_title',       '=', $input],
        ];
    }
    if ($inputtype == 'drupalid') {
        $var_params_contact = [
            ['external_identifier', '=', $input],
        ];
    }

    if ($var_params_contact) {

        $params_cid2cont_get = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'select' => [
                'row_count',
                'id', 
                'external_identifier',
                'job_title',
                'display_name',
            ],
            'where' => $var_params_contact,
        ];
        wachthond($extdebug,7, 'params_contact_get',                $params_cid2cont_get);
        $result_cid2cont_get        = civicrm_api4('Contact','get', $params_cid2cont_get);
        $result_cid2cont_get_count  =                               $result_cid2cont_get->count();
        wachthond($extdebug,4, "result_contact_get_count",          $result_cid2cont_get_count);
    }

    // RESULT
    if ($result_cid2cont_get_count == 1 ) {

        $check_contact          = new stdClass();
        $check_contact->cid     = $result_cid2cont_get[0]['id']                     ?? NULL;
        $check_contact->cmsid   = $result_cid2cont_get[0]['external_identifier']    ?? NULL;
        $check_contact->name    = $result_cid2cont_get[0]['job_title']              ?? NULL;
        $check_contact->naam    = $result_cid2cont_get[0]['display_name']           ?? NULL;

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "FIND_CONTACT FOR $inputtype: $input",     "FOUND! [$check_contact->naam]");
        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,4, "check_contact",                                 $check_contact);
        wachthond($extdebug,4, "########################################################################");
        wachthond($extdebug,4, "check_contact_contactid",                       $check_contact->cid);
        wachthond($extdebug,4, "check_contact_externalid  (drupal id)",         $check_contact->cmsid);
        wachthond($extdebug,4, "check_contact_drupalnaam  (user_name)",         $check_contact->name);
        wachthond($extdebug,4, "check_contact_displayname (civicrm)",           $check_contact->naam);
        wachthond($extdebug,4, "########################################################################");

        $localCache[$key] = $check_contact;
        return $check_contact;

    } else {

        wachthond($extdebug,1, "FIND_CONTACT FOR $inputtype: $input",   "[NO CRM CONTACT FOUND]");    
        return;
    }
}

function find_ufmatch(string $inputtype, $input) {

    static $cache = [];

    $key = $inputtype . ':' . $input;
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $extdebug = 0;  // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    if ($inputtype == NULL) {
        // wachthond($extdebug,2, 'find_ufmatch', "[NO INPUTTYPE]");
        return;
    }
    if ($inputtype == 'ufmatchid') {
        $var_params_ufmatch_get = [
            ['id',            '=', $input],
        ];
    }
    if ($inputtype == 'drupalid') {
        $var_params_ufmatch_get = [
            ['uf_id',         '=', $input],
        ];
    }
    if ($inputtype == 'usermail') {
        $var_params_ufmatch_get = [
            ['uf_name',       '=', $input],
        ];
    }
    if ($inputtype == 'contactid') {
        $var_params_ufmatch_get = [
            ['contact_id',    '=', $input],
        ];
    }

    if ($var_params_ufmatch_get) {

        $params_ufmatch_get = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,           
            'select' => [
                'row_count', 
                'id', 
                'uf_id', 
                'uf_name', 
                'contact_id',
            ],
            'where' => $var_params_ufmatch_get,
        ];
        wachthond($extdebug,7,"params_ufmatch_get",         $params_ufmatch_get);
        $result_ufmatch_get = civicrm_api4('UFMatch','get', $params_ufmatch_get);
        $result_ufmatch_get_count =                         $result_ufmatch_get->count();
        wachthond($extdebug,9,"result_ufmatch_get_count",   $result_ufmatch_get_count);
    }

  // HANDLE RESULTS
  if ($result_ufmatch_get_count == 1 ) {

    $check_ufmatch      = new stdClass();//create a new
    $check_ufmatch->ufid  = $result_ufmatch_get[0]['uf_id']         ?? NULL;
    $check_ufmatch->cid   = $result_ufmatch_get[0]['contact_id']    ?? NULL;
    $check_ufmatch->id    = $result_ufmatch_get[0]['id']            ?? NULL;
    $check_ufmatch->name  = $result_ufmatch_get[0]['uf_name']       ?? NULL;

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "FIND_UFMATCH FOR $inputtype: $input",   "FOUND! [$check_ufmatch->name]");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,4, "check_ufmatch",                         $check_ufmatch);
    wachthond($extdebug,4, "########################################################################");
    wachthond($extdebug,3, "check_ufmatch ufid (drupal uid)",       $check_ufmatch->ufid);
    wachthond($extdebug,3, "check_ufmatch  cid (civicrm id)",       $check_ufmatch->cid);
    wachthond($extdebug,3, "check_ufmatch   id (ufmatch id)",       $check_ufmatch->id);
    wachthond($extdebug,3, "check_ufmatch mail (ufmatch name)",     $check_ufmatch->name);
    wachthond($extdebug,3, "########################################################################");

    return $check_ufmatch;

  } elseif ($result_ufmatch_get_count > 1) {
    wachthond($extdebug,2, "FIND_UFMATCH FOR $inputtype: $input",   "[>1 UFMATCH FOUND]");
    return;
  } else {
    wachthond($extdebug,2, "FIND_UFMATCH FOR $inputtype: $input",   "[NO UFMATCH FOUND]");
    return;
  }

}

function diag_ufmatch(string $inputtype, $inputcid, $input_ufmatch, $input_username, $input_jobtitle, $input_extid) {

    // 1. Maak een unieke sleutel op basis van ALLE parameters die de uitkomst beïnvloeden
    // We pakken de ID van het ufmatch object (als het er is) om de key uniek te maken.
    $ufmatch_id = is_object($input_ufmatch) ? ($input_ufmatch->id ?? '0') : '0';
    
    // De separator '|' maakt de key leesbaar en voorkomt botsingen (bijv 1 en 11 vs 11 en 1)
    $key = $inputtype . '|' . $inputcid . '|' . $ufmatch_id . '|' . $input_username . '|' . $input_extid;

    // 2. Static Cache Check
    static $done = [];
    if (isset($done[$key])) {
        return $done[$key];
    }

    $extdebug = 0; 
    $apidebug = FALSE;

    if ($inputtype == NULL) {
        wachthond($extdebug, 2, 'find_ufmatch', "[NO INPUTTYPE]");
        // Sla ook 'niet gevonden' of errors op in de cache om herhaling te voorkomen
        $done[$key] = null; 
        return;
    }

    watchdog('civicrm_timing', core_microtimer("START diag_ufmatch"), NULL, WATCHDOG_DEBUG);

    $need2create_account    = 0;
    $need2update_account    = 0;
    $need2create_ufmatch    = 0;
    $need2update_ufmatch    = 0;
    $need2repair_ufmatch    = 0;
    $need2update_extid      = 0;
    $need2update_jobtitle   = 0;

    $safe2create_account    = 0;
    $safe2update_account    = 0;
    $safe2create_ufmatch    = 0;
    $safe2update_ufmatch    = 0;
    $safe2repair_ufmatch    = 0;
    $safe2update_extid      = 0;
    $safe2update_jobtitle   = 0;

    $safe2update_ufmatch = $safe2repair_ufmatch = $safe2update_extid = $safe2update_jobtitle = 0;
    
    $diag_ufid = $diag_cid = $diag_id = $diag_name = 0;
    $valid_ufmatchid = $valid_drupalid = $valid_username = NULL;

    $diag_input_ufmatch     = $input_ufmatch;

    wachthond($extdebug,2, "diag_input_ufmatch",        $diag_input_ufmatch);
    if ($diag_input_ufmatch->cid > 0) {
        $diag_cid   = 1;
        wachthond($extdebug,3, "diag_input_ufmatch->cid",     $diag_input_ufmatch->cid);
    }

    // DOEL     :   DIAGNOSE UFMATCH OBV INPUT (CID)
    // CHECK 1  :   KLOPT UFMATCH DRUPAL ID?
    //        a) IS UFMATCH UID  ZELFDE ALS CONNECTED UID (VIA CRM & CMS API)
    //        b) IS UFMATCH NAME ZELFDE ALS USER_NAME (HOEFT NIET, KAN REPAIR NODIG MAKEN)
    // CHECK 2  : IS ER GEEN UFMATCH VIA CID?
    //        a) IS ER WEL EEN DRUPAL ACCOUNT VIA LOAD-BY-NAME? ZO JA, BESTAAT DEZE IN ANDERE UF_MATCH?
    //        b) IS ER WEL EEN DRUPAL ACCOUNT VIA LOAD-BY-MAIL? ZO JA, BESTAAT DEZE IN ANDERE UF_MATCH?
    //        c) BESTAAT USER_NAME IN EEN ANDERE UF_MATCH?

    wachthond($extdebug, 3, "########################################################################");
    wachthond($extdebug, 2, "### 1. CHECK VIA CIVICRM API IF CID: $inputcid HAS A CONNECTED DRUPAL ACCOUNT");
    wachthond($extdebug, 3, "########################################################################");

    if ($diag_cid == 1) {
        $params_drupaluser = [
            'return'     => ["id", "name", "email"],
            'contact_id' => $inputcid,
            'sequential' => 1,
        ];

        try {
            wachthond($extdebug, 7, 'params_drupaluser',    $params_drupaluser);
            $result_drupaluser = civicrm_api3('User','get', $params_drupaluser);
            wachthond($extdebug, 9, 'result_drupaluser',    $result_drupaluser);

            if (!empty($result_drupaluser['values'])) {
                $crm_drupal_account_id    = $result_drupaluser['values'][0]['id']     ?? NULL;
                $crm_drupal_account_name  = $result_drupaluser['values'][0]['name']   ?? NULL;
                $crm_drupal_account_mail  = $result_drupaluser['values'][0]['email']  ?? NULL;
                wachthond($extdebug,3, "crm_drupal_account_id",   $crm_drupal_account_id);
                wachthond($extdebug,3, "crm_drupal_account_name", $crm_drupal_account_name);
                wachthond($extdebug,3, "crm_drupal_account_mail", $crm_drupal_account_mail);

                if ($diag_input_ufmatch->ufid == $crm_drupal_account_id) {
                    $diag_crm_drupalaccount = 1;
                }

                if ($crm_drupal_account_name) {
                    wachthond($extdebug,3, "EEN CONNECTED DRUPAL NAAM GEVONDEN",    "[VIA CID $inputcid]");
                    $crm_drupal_account_found  = 1;
                } else {
                    wachthond($extdebug,3, "GEEN CONNECTED DRUPAL NAAM GEVONDEN",   "[VIA CID $inputcid]");
                }

                if ($input_extid && $crm_drupal_account_id && $crm_drupal_account_id != $input_extid) {
                    wachthond($extdebug, 1, "DRUPALID ($crm_drupal_account_id) != CRMEXTID ($input_extid)", "WARNING [DRUPALID DANGER]");
                }
            }
        } catch (Exception $e) {
            wachthond($extdebug, 1, "ERROR APIv3 User get", $e->getMessage());
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### 2. CHECK VIA DRUPAL API IF UFMATCH_UFID ($diag_input_ufmatch->ufid) HAS A CONNECTED DRUPAL ACCOUNT");
    wachthond($extdebug,3, "########################################################################");

    $cms_drupal_account = user_load($diag_input_ufmatch->ufid);

    wachthond($extdebug,3, "cms_drupal_account->uid",     $cms_drupal_account->uid);
    wachthond($extdebug,3, "cms_drupal_account->name",    $cms_drupal_account->name);
    wachthond($extdebug,3, "cms_drupal_account->mail",    $cms_drupal_account->mail);
    wachthond($extdebug,4, "cms_drupal_account",          $cms_drupal_account);   

    if ($cms_drupal_account && $cms_drupal_account->uid > 0) {

        if ($diag_input_ufmatch->ufid == $cms_drupal_account->uid) {
            $diag_ufid = 1;
            wachthond($extdebug, 2, "DIAG_UFID", $diag_ufid);
        }

        if ($cms_drupal_account->name != $input_username) {
            wachthond($extdebug, 2, "VIA UFID DRUPAL ACCOUNT GEVONDEN", "CHECK [WANT ANDERE USER_NAME]");
        }

        if ($cms_drupal_account->name == $input_username && $cms_drupal_account->name == $input_jobtitle) {
            wachthond($extdebug, 1, "DRUPALNAME == CONSTRUCTED USERNAME == civcrm_jobtitle", "PRIMA");
            $diag_name = 1;
        } else {
            wachthond($extdebug, 1, "NEED 2 UPDATE JOBTITLE", "[NAAR $input_username]");
            $need2update_jobtitle = 1;
            $safe2update_jobtitle = 1;
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### 4. CHECK IF DRUPAL UID ($diag_input_ufmatch->ufid) IS PROPERLY CONNECTED");
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,2, "diag_input_ufmatch->uid",     $diag_input_ufmatch->ufid);
    wachthond($extdebug,2, "input_extid",                 $input_extid);

    // LOGIC: INDIEN GEVONDEN DRUPAL ACCOUNT VIA EXTID GEKOPPELD IS: LEUK!
    // LOGIC: INDIEN NIET DAN IS HET ALSNOG MOGELIJK DAT ER EEN UF_MATCH IS

    // LOGIC: INDIEN EEN GEVONDEN UFMATCH WIJST NAAR HEEL ANDERE CONTACT DAN NIET SAFE
    // LOGIC: INDIEN OOK GEEN UFMATCH DAN IS DRUPAL ACCOUNT EEN ORPHAN (EN KAN MOGELIJK VIA NIEUWE UFMATCH GEKOPPELD WORDEN)

    if ($diag_input_ufmatch->ufid > 0) {
        if ($diag_input_ufmatch->ufid == $input_extid) {
            wachthond($extdebug, 2, "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid)", "PRIMA! [MATCH MET EXTID]");
            $diag_id = 1;
        } else {
            wachthond($extdebug, 2, "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid)", "CHECK! [GEEN EXTID MATCH]");

            // GEBRUIK find_contact
            $diag_contact = find_contact('drupalid', $diag_input_ufmatch->ufid);

            if ($diag_contact && $diag_contact->id > 0) {
                wachthond($extdebug, 1, "INPUT UF MATCH DRUPAL UID HEEFT WEL CIVICRM CONTACT $diag_contact->naam", "CHECK!");

                if ($diag_contact->id == $inputcid) {
                    wachthond($extdebug, 1, "INPUT UF MATCH DRUPAL UID HEEFT EXTID MATCH MET DIT CONTACT", "PRIMA! [WEL UPDATE EXTID NODIG]");
                    $need2update_extid = 1;
                    $safe2update_extid = 1;
                } else {
                    wachthond($extdebug, 1, "INPUT UF MATCH DRUPAL UID HEEFT EXTID MATCH MET ANDER CID", "ERROR! [CONFLICT MET $diag_contact->naam]");
                    $need2repair_ufmatch = 1;
                }
            }

            // GEBRUIK find_ufmatch
            $check_did_ufmatch = find_ufmatch('drupalid', $diag_input_ufmatch->ufid);

            if ($check_did_ufmatch && isset($check_did_ufmatch->id)) {
                if ($check_did_ufmatch->cid == $inputcid) {
                    wachthond($extdebug, 1, "INPUT UF MATCH DRUPAL UID HEEFT UFMATCH MET DIT CONTACT", "HOERA!");
                    $valid_username         = $input_username;
                    $valid_drupalid         = $diag_input_ufmatch->ufid;
                    $valid_ufmatchid        = $check_did_ufmatch->id;
                    $need2update_extid      = 1;
                    $safe2update_extid      = 1;
                } else {
                    wachthond($extdebug, 1, "INPUT UF MATCH DRUPAL UID HEEFT UFMATCH MET ANDER CID", "ERROR!");
                    $valid_username         = $input_username . "_" . $inputcid;
                    $need2update_jobtitle   = 1;
                    $safe2update_jobtitle   = 1;
                }
            } else {
                wachthond($extdebug, 1, "VIA USERNAAM GEVONDEN DRUPAL ACCOUNT KAN GEKOPPELD WORDEN!", "ORPHAN");
                $need2repair_ufmatch = 1;
                if ($diag_input_ufmatch->id > 0) { $need2update_ufmatch = 1; }
                else { $need2create_ufmatch = 1; }
            }
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### 5. BUILD DIAG OBJECT");
    wachthond($extdebug,3, "########################################################################"); 

    watchdog('civicrm_timing', core_microtimer("EINDE diag_ufmatch"), NULL, WATCHDOG_DEBUG);

    if ($diag_input_ufmatch) {

    //  $diag_ufid      = 1;  // = OK IF DRUPAL UID = EXTID (AND ALSO CONNECTED VIA LOADBYNAME) 
    //  $diag_cid       = 1;  // = OK IF CID = CONTACT_ID
    //  $diag_id        = 1;  // = OK IF EXIST AND CID = CONTACT_ID
    //  $diag_name      = 1;  // = OK IF SAME AS USER_NAME & JOB_TITLE (& UNIQUE) & SAME AS LOADBYNAME

        $diag_ufmatch                           = new stdClass();//create a new
        $diag_ufmatch->check_ufid               = $diag_ufid            ?? 0;
        $diag_ufmatch->check_cid                = $diag_cid             ?? 0;
        $diag_ufmatch->check_id                 = $diag_id              ?? 0;
        $diag_ufmatch->check_name               = $diag_name            ?? 0;

        $diag_ufmatch->valid_ufmatchid          = $valid_ufmatchid      ?? NULL;
        $diag_ufmatch->valid_drupalid           = $valid_drupalid       ?? NULL;
        $diag_ufmatch->valid_username           = $valid_username       ?? NULL;

        $diag_ufmatch->need2_update_ufmatch     = $need2update_ufmatch  ?? 0;
        $diag_ufmatch->need2_create_ufmatch     = $need2create_ufmatch  ?? 0;
        $diag_ufmatch->need2_update_extid       = $need2update_extid    ?? 0;
        $diag_ufmatch->need2_update_jobtitle    = $need2update_jobtitle ?? 0;

        $diag_ufmatch->safe2_update_ufmatch     = $safe2update_ufmatch  ?? 0;
        $diag_ufmatch->safe2_create_ufmatch     = $safe2create_ufmatch  ?? 0;
        $diag_ufmatch->safe2_update_extid       = $safe2update_extid    ?? 0;
        $diag_ufmatch->safe2_update_jobtitle    = $safe2update_jobtitle ?? 0;

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,1, "DIAG_UFMATCH FOR $inputtype: $inputcid",$diag_ufmatch);
        wachthond($extdebug,3, "########################################################################");

        $done[$key] = $diag_ufmatch; // Opslaan
        return $diag_ufmatch;

    } else {
        wachthond($extdebug,1, "DIAG_UFMATCH FOR $input_username", "[NO UFMATCH FOUND]");   
        $done[$key] = null; // Opslaan dat we niets vonden
        return;
    }

}

/**
 * Beheert Drupal-rollen voor een specifieke gebruiker.
 * * @param int $cmsid        Het Drupal User ID (UID).
 * @param string $cmsrol    De naam van de rol (bijv. 'Leiding').
 * @param string $cmsaction De actie: 'ADD' of 'REMOVE'.
 */
function drupal_role_change($cmsid, $cmsrol, $cmsaction) {

    $extdebug = 0; 
    $apidebug = FALSE;

    // VALIDATIE: Stop direct als er gegevens ontbreken om fouten te voorkomen.
    if ($cmsid == NULL || $cmsrol == NULL) {
        wachthond($extdebug, 2, 'Fout', "UID of Rolnaam ontbreekt. Actie afgebroken.");
        return;
    }

    // STAP 1: De huidige gebruiker volledig ophalen uit de Drupal database.
    $existinguser = user_load($cmsid);
    if (!$existinguser) {
        wachthond($extdebug, 1, "Fout", "Gebruiker met UID $cmsid niet gevonden.");
        return;
    }

    // STAP 2: Leg de huidige rollen vast voor de 'Dirty Check'.
    $existinguser_roles_org = $existinguser->roles; // De lijst zoals die nu in de DB staat.
    $existinguser_roles_new = $existinguser->roles; // Een kopie die we gaan aanpassen.

    // STAP 3: Controleren of de gebruiker de rol op dit moment al heeft.
    // array_search geeft de sleutel terug als de rol gevonden is, anders FALSE.
    $role_exists = array_search($cmsrol, $existinguser->roles);

    // STAP 4: De logica voor toevoegen of verwijderen.
    if ($cmsaction == 'ADD' && $role_exists === FALSE) {
        // De gebruiker heeft de rol nog niet en we willen hem TOEVOEGEN.
        $role_to_add = user_role_load_by_name($cmsrol);
        if ($role_to_add) {
            // Voeg de rol toe aan de array (Sleutel = RoleID, Waarde = Rolnaam).
            $existinguser_roles_new[$role_to_add->rid] = $role_to_add->name;
            wachthond($extdebug, 1, "Rollen", "Rol '$cmsrol' klaargezet om toe te voegen.");
        }
    } 
    elseif ($cmsaction == 'REMOVE' && $role_exists !== FALSE) {
        // De gebruiker heeft de rol wel en we willen hem VERWIJDEREN.
        // We filteren de rol uit de nieuwe array.
        $existinguser_roles_new = array_diff($existinguser_roles_new, [$cmsrol]);
        wachthond($extdebug, 1, "Rollen", "Rol '$cmsrol' klaargezet om te verwijderen.");
    }

    // STAP 5: DIRTY CHECK
    // Vergelijk de oude lijst met de nieuwe lijst.
    // Als ze gelijk zijn, doen we GEEN user_save. Dit bespaart veel rekentijd.
    if ($existinguser_roles_org != $existinguser_roles_new) {
        $existinguser->roles = $existinguser_roles_new;
        user_save($existinguser); // Sla de wijzigingen op in de Drupal database.
        wachthond($extdebug, 1, "SUCCESS", "Rollen voor UID $cmsid bijgewerkt.");
    } else {
        // Als er geen verschil is, hoeven we niets te doen.
        wachthond($extdebug, 1, "SKIP", "Geen wijziging nodig voor UID $cmsid. Rolstatus was al correct.");
    }

    return $existinguser; 
}

function isValidDate($date, $format = 'Y-m-d') {
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

function isValidDateTime($date, $format = 'm-d-Y H:i') {
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

/**
 * Vergelijkt twee datums en bepaalt welke groter (nieuwer) is.
 * * @return mixed 1 (Source > Target), 0 (Target > Source), 'E' (Gelijk), of FALSE/NULL.
 */
function date_bigger($date_source, $date_target, ?string $date_source_name = NULL, ?string $date_target_name = NULL) {
    
    // 1. Snelle validatie: Afhandeling van lege waarden.
    if ($date_source && empty($date_target))        return TRUE;
    if (empty($date_source) && $date_target)        return FALSE;
    if (empty($date_source) && empty($date_target)) return NULL;

    $extdebug = 0;

    try {
        // 2. Omzetten naar DateTime objecten (als het strings zijn).
        // DateTime herkent vrijwel alle formaten automatisch.
        $obj_source = ($date_source instanceof DateTime) ? $date_source : new DateTime($date_source);
        $obj_target = ($date_target instanceof DateTime) ? $date_target : new DateTime($date_target);

        // 3. Directe vergelijking (PHP handelt dit intern heel efficiënt af).
        if ($obj_source > $obj_target) {
            return 1;   // Source is nieuwer.
        } 
        
        if ($obj_source < $obj_target) {
            return 0;   // Target is nieuwer.
        }

        return "E";     // Exact gelijk.

    } catch (Exception $e) {
        // Stap 2 & 3 gecombineerd: als de string ongeldig is, gooit DateTime een Exception.
        wachthond($extdebug, 2, "Fout", "Ongeldige datum: " . $e->getMessage());
        return FALSE;
    }
}

/**
 * Vergelijkt of de bron-datum groter is dan of gelijk is aan de doel-datum.
 * * @param mixed $date_source De te controleren datum (String of DateTime).
 * @param mixed $date_target De vergelijkingsdatum (String of DateTime).
 * @return mixed 1 (Groter), 0 (Kleiner), 'E' (Exact gelijk), of FALSE/NULL.
 */
function date_biggerequal($date_source, $date_target, ?string $date_source_name = NULL, ?string $date_target_name = NULL) {

    // --- 1. SNELLE VALIDATIE: AFHANDELING LEGE WAARDEN ---
    // We handelen de 'empty' scenario's direct af zonder zware objecten te maken.
    if ($date_source && empty($date_target))        return 1;    // Source wint
    if (empty($date_source) && $date_target)        return 0;    // Target wint
    if (empty($date_source) && empty($date_target)) return NULL; // Beiden leeg

    $extdebug = 0;

    wachthond($extdebug, 2, "########################################################################");
    wachthond($extdebug, 1, "### DATE COMPARISON: $date_source_name vs $date_target_name", "[COMPARE]");
    wachthond($extdebug, 2, "########################################################################");

    try {
        // --- 2. OMZETTEN NAAR DATETIME OBJECTEN ---
        // PHP's DateTime klasse is veel efficiënter in vergelijkingen dan Unix timestamps.
        $obj_source = ($date_source instanceof DateTime) ? $date_source : new DateTime($date_source);
        $obj_target = ($date_target instanceof DateTime) ? $date_target : new DateTime($date_target);

        // --- 3. DE VERGELIJKING ---
        // We controleren eerst op exacte gelijkheid (inclusief tijdstip).
        if ($obj_source == $obj_target) {
            wachthond($extdebug, 1, "Resultaat", "Datums zijn exact gelijk (E)");
            return "E";
        }

        // Daarna controleren we of de bron groter (nieuwer) is.
        if ($obj_source > $obj_target) {
            wachthond($extdebug, 1, "Resultaat", "Source is nieuwer dan Target (1)");
            return 1;
        }

        // Als het niet gelijk is en niet groter, dan is het dus kleiner.
        wachthond($extdebug, 1, "Resultaat", "Source is ouder dan Target (0)");
        return 0;

    } catch (Exception $e) {
        // Als een string geen geldige datum is, vangen we de fout hier op.
        wachthond($extdebug, 2, "Fout", "Ongeldig datumformaat ontvangen: " . $e->getMessage());
        return FALSE;
    }
}

/**
 * Controleert of een specifieke datum valt tussen een start- en einddatum.
 * * @param mixed $date_check De te controleren datum (String of DateTime).
 * @param mixed $date_start De startdatum van de reeks.
 * @param mixed $date_einde De einddatum van de reeks.
 * @return mixed 1 (Binnen reeks), 0 (Buiten reeks), of FALSE bij een fout.
 */
function date_between($date_check, $date_start, $date_einde, ?string $date_check_name = NULL, ?string $date_range_name = NULL) {
    
    // --- 1. SNELLE VALIDATIE ---
    // Als één van de drie datums ontbreekt, kunnen we geen vergelijking maken.
    if (empty($date_check) || empty($date_start) || empty($date_einde)) {
        return FALSE;
    }

    $extdebug = 0;

    try {
        // --- 2. OMZETTEN NAAR DATETIME OBJECTEN ---
        // Dit is veel sneller dan handmatige Unix-conversies via een helper.
        $obj_check = ($date_check instanceof DateTime) ? $date_check : new DateTime($date_check);
        $obj_start = ($date_start instanceof DateTime) ? $date_start : new DateTime($date_start);
        $obj_einde = ($date_einde instanceof DateTime) ? $date_einde : new DateTime($date_einde);

        // --- 3. DE VERGELIJKING ---
        // PHP kan DateTime objecten direct vergelijken met >= en <=.
        if ($obj_check >= $obj_start && $obj_check <= $obj_einde) {
            
            wachthond($extdebug, 1, "Resultaat", "$date_check_name valt BINNEN $date_range_name [1]");
            return 1;
            
        } else {
            
            wachthond($extdebug, 1, "Resultaat", "$date_check_name valt BUITEN $date_range_name [0]");
            return 0;
        }

    } catch (Exception $e) {
        // Foutafhandeling als een string geen geldige datum is.
        wachthond($extdebug, 2, "Fout", "Ongeldige datum in date_between: " . $e->getMessage());
        return FALSE;
    }
}

/**
 * Controleert of een datum binnen het boekjaar van een andere datum valt.
 * * @param mixed $date_check    De te controleren datum (String of DateTime).
 * @param mixed $date_compare  De referentiedatum waarvan we het boekjaar bepalen.
 * @return mixed 1 (Binnen), 'before' (Vóór), 'after' (Na), of FALSE bij fout.
 */
function infiscalyear($date_check, $date_compare, $date_check_name = NULL, $date_range_name = NULL) {
    
    // --- 1. VALIDATIE ---
    if (empty($date_check) || empty($date_compare)) {
        return FALSE;
    }

    $extdebug = 0;

    // --- 2. BOEKJAAR DATA OPHALEN ---
    // We gaan ervan uit dat curriculum_civicrm_fiscalyear strings of objecten teruggeeft.
    $fiscalyear_data = curriculum_civicrm_fiscalyear($date_compare);
    $date_start      = $fiscalyear_data['fiscalyear_start'] ?? NULL;
    $date_einde      = $fiscalyear_data['fiscalyear_einde'] ?? NULL;

    if (!$date_start || !$date_einde) {
        return FALSE;
    }

    try {
        // --- 3. OMZETTEN NAAR DATETIME OBJECTEN ---
        // Native DateTime vergelijkingen zijn sneller en robuuster dan Unix timestamps.
        $obj_check = ($date_check instanceof DateTime) ? $date_check : new DateTime($date_check);
        $obj_start = ($date_start instanceof DateTime) ? $date_start : new DateTime($date_start);
        $obj_einde = ($date_einde instanceof DateTime) ? $date_einde : new DateTime($date_einde);

        // --- 4. DE VERGELIJKING (Sectiekopjes & Uitlijning) ---
        
        // Situatie A: Binnen het boekjaar
        if ($obj_check >= $obj_start && $obj_check <= $obj_einde) {
            wachthond($extdebug, 1, "Boekjaar", "$date_check_name valt BINNEN boekjaar ($date_range_name) [1]");
            return 1;
        } 
        
        // Situatie B: Vóór het boekjaar
        if ($obj_check < $obj_start) {
            wachthond($extdebug, 1, "Boekjaar", "$date_check_name valt VÓÓR het boekjaar [before]");
            return 'before';
        } 
        
        // Situatie C: Na het boekjaar
        if ($obj_check > $obj_einde) {
            wachthond($extdebug, 1, "Boekjaar", "$date_check_name valt NA het boekjaar [after]");
            return 'after';
        }

    } catch (Exception $e) {
        // Foutafhandeling als DateTime de string niet kan parsen.
        wachthond($extdebug, 2, "Fout", "Ongeldige datum in infiscalyear: " . $e->getMessage());
        return FALSE;
    }

    return 0;
}

/**
 * Bepaalt de start- en einddatum van het boekjaar waarin een gegeven datum valt.
 * Gebruikt de instellingen uit CiviCRM (Administer > Localization > Date Formats).
 *
 * @param string|DateTime $inputdatum De datum (String of DateTime object).
 * @return array|null Array met start- en einddatum of null bij lege input.
 */
function curriculum_civicrm_fiscalyear($inputdatum) {

    // --- STAP 1: VALIDATIE ---
    if (empty($inputdatum)) {
        return NULL;
    }

    $extdebug = 0; // 1 = basis // 2 = verbose

    // --- STAP 2: INPUT NORMALISEREN (HYBRIDE OPLOSSING) ---
    // Hier lossen we de Fatal Error op. We checken wat er binnenkomt.
    if ($inputdatum instanceof DateTime) {
        // Het is een modern DateTime object (vanuit find_fiscalyear)
        $input_ts = $inputdatum->getTimestamp();
    } else {
        // Het is een ouderwetse string (legacy support)
        $input_ts = strtotime($inputdatum);
    }

    // Vanaf hier werken we met de timestamp, dus de rest van de logica blijft gelijk
    $input_year = date('Y', $input_ts);
    $input_iso  = date('Y-m-d', $input_ts);

    // --- STAP 3: CONFIGURATIE OPHALEN ---
    // Haal de fiscale instellingen van de stichting op uit de CiviCRM database.
    $config          = CRM_Core_Config::singleton();
    $fiscalYearStart = $config->fiscalYearStart; // Bevat 'M' (maand) en 'd' (dag)

    // --- STAP 4: BEREKEN HET FISCALE JAAR ---
    // We maken eerst een startdatum in het jaar van de inputdatum.
    $f_month = $fiscalYearStart['M'];
    $f_day   = $fiscalYearStart['d'];
    
    $start_this_year = strtotime("$input_year-$f_month-$f_day");

    // LOGICA:
    // Als de inputdatum VÓÓR de fiscale start van dit kalenderjaar ligt, 
    // dan begon het boekjaar in het VORIGE kalenderjaar.
    if ($input_ts < $start_this_year) {
        $fiscal_start_ts = strtotime("-1 year", $start_this_year);
    } else {
        $fiscal_start_ts = $start_this_year;
    }

    // Het boekjaar eindigt altijd precies 1 jaar later minus 1 dag.
    $fiscal_einde_ts = strtotime("+1 year -1 day", $fiscal_start_ts);

    // --- STAP 5: RESULTAAT FORMEREN ---
    $fiscalyear_array = array(
        'fiscalyear_input' => $input_iso,
        'fiscalyear_start' => date('Y-m-d', $fiscal_start_ts),
        'fiscalyear_einde' => date('Y-m-d', $fiscal_einde_ts),
    );

    // --- STAP 6: DEBUGGING ---
    if ($extdebug >= 1) {
        if (function_exists('wachthond')) {
            wachthond($extdebug, 1, "Boekjaar bepaald voor: $input_iso");
            wachthond($extdebug, 1, "Start: " . $fiscalyear_array['fiscalyear_start']);
            wachthond($extdebug, 1, "Einde: " . $fiscalyear_array['fiscalyear_einde']);
        }
    }

    return $fiscalyear_array;
}

function base_cid2cont(int $contactID): ?array {

    // --- 1. VEILIGHEIDSCHECK ---
    if (empty($contactID)) return null;

    // --- 2. STATIC CACHE (PERFORMANCE!) ---
    static $contact_cache = [];
    if (isset($contact_cache[$contactID])) {
        return $contact_cache[$contactID];
    }

    $extdebug = 0; 
    $apidebug = FALSE;

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("START base_cid2cont voor ID: $contactID"), NULL, WATCHDOG_DEBUG);
    }

    // --- 3. DE QUERY ---
    $params_cid2cont = [
        'checkPermissions' => FALSE,
        'limit' => 1,
        'select' => [
            // Core Velden
            'contact_type',
            'contact_sub_type',
            'id',
            'contact_id',
            'image_URL',
            'birth_date',
            'gender_id:label',
            'first_name',
            'middle_name',
            'last_name',
            'nick_name',
            'display_name',
            'job_title',
            'external_identifier',
            'email.email',

            // PRIVACY Groep
            'PRIVACY.Contactvoorkeuren',
            'PRIVACY.Geheim_adres',
            'PRIVACY.Toestemming_beeldgebruik',
            'PRIVACY.notificatie_deel',
            'PRIVACY.notificatie_leid',
            'PRIVACY.notificatie_kamp',
            'PRIVACY.notificatie_staf',

            // Curriculum Groep
            'Curriculum.CV_Deel',
            'Curriculum.CV_Leid',
            'Curriculum.Keren_Deel',
            'Curriculum.Keren_Leid',
            'Curriculum.Keren_Topkamp',
            'Curriculum.Laatste_keer',

            // INTAKE Groep
            'INTAKE.INT_status',
            'INTAKE.INT_nodig',         // Toegevoegd (miste in originele select)
            'INTAKE.Intakegesprek_datum',
            'INTAKE.Intakegesprek_persoon',
            'INTAKE.FOT_status',
            'INTAKE.FOT_Update',
            'INTAKE.NAW_nodig',
            'INTAKE.NAW_status',
            'INTAKE.NAW_gecheckt',
            'INTAKE.BIO_nodig',
            'INTAKE.BIO_status',
            'INTAKE.BIO_ingevuld',
            'INTAKE.BIO_gecheckt',
            'INTAKE.REF_nodig',
            'INTAKE.REF_status',
            'INTAKE.REF_datum',
            'INTAKE.REF_naam',
            'INTAKE.ref_persoon',
            'INTAKE.ref_feedback',
            'INTAKE.VOG_nodig',
            'INTAKE.VOG_status',
            'INTAKE.VOG_laatste',

            // MEDISCH Groep
            'MEDISCH.medisch_issues',
            'MEDISCH.medisch_toelichting',
            'MEDISCH.medisch_medicatie',
            'MEDISCH.medisch_luchtwegklachten',
            'MEDISCH.medisch_notities',
            'MEDISCH.medisch_doublecheck',

            // WERVING Groep
            'WERVING.leeftijd_decimalen',
            'WERVING.nextkamp_decimalen',
            'WERVING.Datum_belangstelling',
            'WERVING.Welke_kampweek',
            'WERVING.Welke_leeftijdsgroep',
            'WERVING.Welke_kampweken',
            'WERVING.mee_komendkampjaar',
            'WERVING.mee_verwachting',
            'WERVING.mee_toelichting',
            'WERVING.mee_update',
            'WERVING.mee_notities',
            'WERVING.vakantieregio',
        ],
        'join' => [
            ['Email AS email', 'LEFT', ['id', '=', 'email.contact_id']],
        ],
        'where' => [
            ['id', '=', $contactID],
            ['contact_type', '=', 'Individual'],
        ],
    ];

    // Debugging (indien nodig)
    if ($extdebug) {
        wachthond($extdebug, 7, 'params_cid2cont', $params_cid2cont);
    }
    
    $result_cid2cont = civicrm_api4('Contact', 'get', $params_cid2cont);
    
    if ($extdebug) {
        wachthond($extdebug, 9, 'result_cid2cont', $result_cid2cont);
    }

    // Haal de eerste rij op
    $result = $result_cid2cont[0] ?? NULL;

    // Als er geen resultaat is, sla NULL op in cache en return
    if (!$result) {
        $contact_cache[$contactID] = null;
        return null;
    }

    // --- 4. FORMATTING ---
    $first_name  = ucfirst(trim($result['first_name'] ?? ''))        ?: NULL;
    $middle_name = strtolower(trim($result['middle_name'] ?? ''))    ?: NULL;
    $last_name   = ucfirst(trim($result['last_name'] ?? ''))         ?: NULL;
    $nick_name   = ucfirst(trim($result['nick_name'] ?? ''))         ?: NULL;

    // Displayname constructie
    if ($first_name && $middle_name && $last_name) {
        $displayname = "$first_name $middle_name $last_name";
    } elseif ($first_name && $last_name) {
        $displayname = "$first_name $last_name";
    } else {
        $displayname = $first_name;
    }

    // Bereken update jaar
    $mee_update_raw  = $result['WERVING.mee_update'] ?? NULL;
    $mee_update_year = $mee_update_raw ? date('Y', strtotime($mee_update_raw)) : NULL;

    // --- 5. RESULTAAT ARRAY (UITGELIJND) ---
    $continfo_array = array(
        'contact_type'              => $result['contact_type']                      ?? NULL,
        'contact_subtype'           => $result['contact_sub_type']                  ?? NULL,
        'contact_id'                => $contactID,
        'contact_foto'              => $result['image_URL']                         ?? NULL,
        'birth_date'                => $result['birth_date']                        ?? NULL,
        'gender'                    => $result['gender_id:label']                   ?? NULL,
        'first_name'                => $first_name,
        'middle_name'               => $middle_name,
        'last_name'                 => $last_name,
        'nick_name'                 => $nick_name,
        'displayname'               => $displayname,
        'crm_drupalnaam'            => trim($result['job_title']                    ?? ''),
        'crm_externalid'            => trim($result['external_identifier']          ?? ''),
        'leeftijd_vantoday_deci'    => $result['WERVING.leeftijd_decimalen']        ?? NULL,
        'leeftijd_nextkamp_deci'    => $result['WERVING.nextkamp_decimalen']        ?? NULL,
        'laatstekeer'               => $result['Curriculum.Laatste_keer']           ?? NULL,
        
        // Curriculum
        'curcv_deel_array'          => $result['Curriculum.CV_Deel']                ?? NULL,
        'curcv_leid_array'          => $result['Curriculum.CV_Leid']                ?? NULL,
        'oldcv_deel_array'          => NULL,
        'oldcv_leid_array'          => NULL,
        'curcv_keer_deel'           => $result['Curriculum.Keren_Deel']             ?? NULL,
        'curcv_keer_leid'           => $result['Curriculum.Keren_Leid']             ?? NULL,
        'curcv_keer_top'            => $result['Curriculum.Keren_Topkamp']          ?? NULL,
        
        // Werving
        'werving_mee_komendkamp'    => $result['WERVING.mee_komendkampjaar']        ?? NULL,
        'werving_mee_verwachting'   => $result['WERVING.mee_verwachting']           ?? NULL,
        'werving_mee_toelichting'   => $result['WERVING.mee_toelichting']           ?? NULL,
        'werving_mee_update'        => $mee_update_raw,
        'werving_mee_update_year'   => $mee_update_year,
        'werving_mee_notities'      => $result['WERVING.mee_notities']              ?? NULL,
        'werving_vakantieregio'     => $result['WERVING.vakantieregio']             ?? NULL,
        
        // Privacy
        'privacy_voorkeuren'        => $result['PRIVACY.Contactvoorkeuren']         ?? NULL,
        'privacy_geheimadres'       => $result['PRIVACY.Geheim_adres']              ?? NULL,
        'privacy_beeldgebruik'      => $result['PRIVACY.Toestemming_beeldgebruik']  ?? NULL,
        'cont_notificatie_deel'     => $result['PRIVACY.notificatie_deel']          ?? NULL,
        'cont_notificatie_leid'     => $result['PRIVACY.notificatie_leid']          ?? NULL,
        'cont_notificatie_kamp'     => $result['PRIVACY.notificatie_kamp']          ?? NULL,
        'cont_notificatie_staf'     => $result['PRIVACY.notificatie_staf']          ?? NULL,
        'datum_belangstelling'      => $result['WERVING.Datum_belangstelling']      ?? NULL,
        
        // Intake & VOG
        'cont_intnodig'             => $result['INTAKE.INT_nodig']                  ?? NULL,
        'cont_intstatus'            => $result['INTAKE.INT_status']                 ?? NULL,
        'cont_intake_datum'         => $result['INTAKE.Intakegesprek_datum']        ?? NULL,
        'cont_intake_persoon'       => $result['INTAKE.Intakegesprek_persoon']      ?? NULL,
        'cont_fotstatus'            => $result['INTAKE.FOT_status']                 ?? NULL,
        'cont_fotupdate'            => $result['INTAKE.FOT_Update']                 ?? NULL,
        'cont_nawnodig'             => $result['INTAKE.NAW_nodig']                  ?? NULL,
        'cont_nawstatus'            => $result['INTAKE.NAW_status']                 ?? NULL,
        'cont_nawgecheckt'          => $result['INTAKE.NAW_gecheckt']               ?? NULL,
        'cont_bionodig'             => $result['INTAKE.BIO_nodig']                  ?? NULL,
        'cont_biostatus'            => $result['INTAKE.BIO_status']                 ?? NULL,
        'cont_bioingevuld'          => $result['INTAKE.BIO_ingevuld']               ?? NULL,
        'cont_biogecheckt'          => $result['INTAKE.BIO_gecheckt']               ?? NULL,
        'cont_refnodig'             => $result['INTAKE.REF_nodig']                  ?? NULL,
        'cont_refstatus'            => $result['INTAKE.REF_status']                 ?? NULL,
        'cont_refdatum'             => $result['INTAKE.REF_datum']                  ?? NULL,
        'cont_refnaam'              => $result['INTAKE.REF_naam']                   ?? NULL,
        'cont_vognodig'             => $result['INTAKE.VOG_nodig']                  ?? NULL,
        'cont_vogstatus'            => $result['INTAKE.VOG_status']                 ?? NULL,
        'cont_voglaatste'           => $result['INTAKE.VOG_laatste']                ?? NULL,
    );

    // --- 6. CACHE OPSLAAN ---
    $contact_cache[$contactID] = $continfo_array;

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("EINDE cid2cont"), NULL, WATCHDOG_DEBUG);
    }

    return $continfo_array;
}

/**
 * Haalt uitgebreide deelname-informatie op basis van een Participant ID.
 * Koppelt Participant, Event en Contact data aan elkaar.
 * * @param int $partid
 * @return array|false De verzamelde deelnamegegevens of false bij fout.
 */
function base_pid2part($partid) {

    // --- 1. STATIC CACHE (Snelheidswinst!) ---
    static $pid_cache = [];
    if (isset($pid_cache[$partid])) {
        return $pid_cache[$partid];
    }

    $extdebug = 0; // 1 = basic // 2 = verbose
    if (empty($partid)) return false;

    // Start timer voor inzicht in de performance logs
    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("START pid2part voor PID: $partid"), NULL, WATCHDOG_DEBUG);
    }

    // Voorbereiding: haal event_types op voor de rol-bepaling
    $eventtypes        = get_event_types(); 
    $eventtypesleidall = $eventtypes['leid_all'];

    // --- 2. DE QUERY: Alle velden strak onder elkaar ---
    $params_pid2part = [
        'checkPermissions' => FALSE,
        'limit'            => 1,
        'select'           => [
            'id',
            'contact_id',
            'contact.display_name',
            'contact.Curriculum.Keren_Deel',
            'contact.Curriculum.Keren_Leid',
            'status_id',
            'status_id:name',
            'role_id',
            'register_date',
            'event_id',
            'event.id',
            'event.title',
            'event.event_type_id',
            'event.event_type_id:label',
            'event.start_date',
            'event.end_date',
            'PART.eventjaar',
            'PART.kampjaar',
            'PART.PART_kampstart',
            'PART.PART_kampeinde',
            'PART.PART_kamptype_naam',
            'PART.PART_kampweek_nr',
            'PART_LEID.Welk_kamp',
            'PART_LEID.Functie',
            'event.Event_Kenmerken.kampnaam',
            'event.Event_Kenmerken.kampkort',
            'event.Event_Kenmerken.kamptype_naam',
            'event.Event_Kenmerken.kamptype_naam:label',
            'event.Event_Kenmerken.kamptype_id',
            'event.Event_Kenmerken.kampsoort',
            'event.Event_Kenmerken.brengen_van',
            'event.Event_Kenmerken.brengen_tot',
            'event.Event_Kenmerken.pres_van',
            'event.Event_Kenmerken.pres_tot',
            'event.Event_Kenmerken.halen_van',
            'event.Event_Kenmerken.halen_tot',
            'Event_Kenmerken_Linkjes.thema_naam',
            'Event_Kenmerken_Linkjes.thema_info',
            'Event_Kenmerken_Linkjes.goeddoel_naam',
            'Event_Kenmerken_Linkjes.goeddoel_info',
            'Event_Kenmerken_Linkjes.goeddoel_link',
            'PART.PART_kamplang',
            'PART.PART_kampkort',
            'PART.PART_1xkeer_deel',
            'PART.PART_1xkeer_leid',
            'PART_DEEL.Groep_klas',
            'PART_DEEL.Voorkeur',
            'PART.PART_kamptype_id',
            'PART.PART_kampfunctie',
            'PART.PART_kamprol',
            'PART.PART_datum_check',
            'PART.vakantieregio',
            'PART_DEEL_INTERN.wachtlijst_erop',
            'PART_DEEL_INTERN.wachtlijst_eraf',
            'PART_DEEL_INTERN.criteriacheck_start',
            'PART_DEEL_INTERN.criteriacheck_einde',
            'PART_DEEL.Tijdslot_brengen',
            'PART_DEEL.Tijdslot_halen',
            'PART_INTERN.groep_letter',
            'PART_INTERN.groep_kleur',
            'PART_INTERN.groep_naam',
            'PART_INTERN.Slaapzaal',
            'PART_KAMPGELD.contribid',
            'PART_KAMPGELD.regeling',
            'PART_KAMPGELD.regeling:label',
            'PART_KAMPGELD.fietshuur',
            'event.Event_Kenmerken.Fietshuur',
            'PART_DEEL_INTERN.criteria_leeftijd',
            'PART_DEEL_INTERN.criteria_school',
            'PART_DEEL_INTERN.criteria_indicatie',
            'PART_DEEL_INTERN.criteria_oordeel',
            'PART_EVALUATIE.DATUM_evaluatie',
            'PART_EVALUATIE.terugblik_score:label',
            'PART_EVALUATIE.kampthema_score:label',
            'PART_EVALUATIE.inhoud_score:label',
            'PART_EVALUATIE.actief_score:label',
            'PART_EVALUATIE.slapen_score:label',
            'PART_EVALUATIE.etendrinken_score:label',
            'PART_EVALUATIE.brengenhalen_score:label',
            'PART_EVALUATIE.kampinfo_score:label',
            'PART_EVALUATIE.aanraden_score:label',
            'PART_LEID_INTERN.INT_nodig',
            'PART_LEID_INTERN.NAW_nodig',
            'PART_LEID_INTERN.BIO_nodig',
            'PART_LEID_INTERN.REF_nodig',
            'PART_LEID_INTERN.VOG_nodig',
            'PART_LEID_INTERN.INT_status',
            'PART_LEID_INTERN.NAW_status',
            'PART_LEID_INTERN.BIO_status',
            'PART_LEID_INTERN.REF_status',
            'PART_LEID_INTERN.VOG_status',
            'PART.NAW_gecheckt',
            'PART.BIO_gecheckt',
            'PART_LEID_REF.REF_persoon',
            'PART_LEID_REF.REF_gevraagd',
            'PART_LEID_REF.REF_feedback',
            'PART_LEID_REFERENTIE.referentie_cid',
            'PART_LEID_REFERENTIE.referentie_naam',
            'PART_LEID_VOG.Datum_verzoek',
            'PART_LEID_VOG.Datum_aanvraag',
            'PART_LEID_VOG.Datum_nieuwe_VOG',
            'PART_LEID_HOOFD.notificatie_deel',
            'PART_LEID_HOOFD.notificatie_leid',
            'PART_LEID_HOOFD.notificatie_kamp',
            'PART_LEID_HOOFD.notificatie_staf',
            'PART_LEID_HOOFD.Jouw_prive_emailadres',
        ],
        'join' => [
            ['Event AS event',   'INNER', ['event_id',   '=', 'event.id']],
            ['Contact AS contact', 'INNER', ['contact_id', '=', 'contact.id']],
        ],
        'where' => [['id', '=', $partid]],
    ];

    // Uitvoering en Debugging
    wachthond($extdebug, 7, 'params_pid2part', $params_pid2part);
    $result_pid2part = civicrm_api4('Participant', 'get', $params_pid2part);
    wachthond($extdebug, 9, 'result_pid2part', $result_pid2part);

    $result = $result_pid2part[0] ?? NULL;
    if (!$result) return false;

    // --- 3. LOGICA BEREKENINGEN ---
    $event_start     = $result['event.start_date'];
    $fiscal          = curriculum_civicrm_fiscalyear($event_start);
    
    // Bepaal Functie en Rol
    $type_id         = $result['event.event_type_id'];
    $leid_functie    = $result['PART_LEID.Functie'] ?? 'kampleiding';
    $part_rol        = in_array($type_id, $eventtypesleidall) || $type_id == 1 ? 'leiding' : 'deelnemer';
    $part_functie    = ($part_rol == 'leiding') ? $leid_functie : 'deelnemer';

    // Kampkort formatting
    $pkort           = $result['PART.PART_kampkort'] ?? '';
    $ekort           = $result['event.Event_Kenmerken.kampkort'] ?? '';

    // --- 4. DE RESULT ARRAY ---
    $partinfo_array = array(
        'id'                            => $result['id']                                    ?? NULL,
        'contact_id'                    => $result['contact_id']                            ?? NULL,
        'displayname'                   => $result['contact.display_name']                  ?? NULL,
        'curcv_keer_deel'               => $result['contact.Curriculum.Keren_Deel']         ?? NULL,
        'curcv_keer_leid'               => $result['contact.Curriculum.Keren_Leid']         ?? NULL,
        'status_id'                     => $result['status_id']                             ?? NULL,
        'status_name'                   => $result['status_id:name']                        ?? NULL,
        'role_id'                       => $result['role_id']                               ?? NULL,
        'part_event_id'                 => $result['event_id']                              ?? NULL,
        'event_id'                      => $result['event.id']                              ?? NULL,
        'event_title'                   => $result['event.title']                           ?? NULL,
        'event_type_id'                 => $result['event.event_type_id']                   ?? NULL,
        'event_type_label'              => $result['event.event_type_id:label']             ?? NULL,
        'register_date'                 => $result['register_date']                         ?? NULL,
        'event_start_date'              => $result['event.start_date']                      ?? NULL,
        'event_end_date'                => $result['event.end_date']                        ?? NULL,
        'event_fiscalyear'              => $fiscal                                          ?? NULL,
        'event_fiscalyear_start'        => $fiscal['fiscalyear_start']                      ?? NULL,
        'event_fiscalyear_einde'        => $fiscal['fiscalyear_einde']                      ?? NULL,
        'part_kampstart'                => $result['PART.PART_kampstart']                   ?? NULL,
        'part_kampeinde'                => $result['PART.PART_kampeinde']                   ?? NULL,
        'part_kampjaar'                 => date('Y', strtotime($event_start))               ?? NULL,
        'part_kampjaar_kort'            => date('y', strtotime($event_start))               ?? NULL,
        'part_kampnaam'                 => $result['PART.PART_kamplang']                    ?? NULL,
        'part_kampkort'                 => $pkort                                           ?? NULL,
        'part_kampkort_low'             => strtolower(preg_replace('/[^ \w-]/','',$pkort))  ?? NULL,
        'part_kampkort_cap'             => strtoupper(preg_replace('/[^ \w-]/','',$pkort))  ?? NULL,
        'part_kamptype_naam'            => $result['PART.PART_kamptype_naam']               ?? NULL,
        'part_kamptype_id'              => $result['PART.PART_kamptype_id']                 ?? NULL,
        'part_kampweek_nr'              => $result['PART.PART_kampweek_nr']                 ?? NULL,
        'part_functie'                  => $part_functie                                    ?? NULL,
        'part_rol'                      => $part_rol                                        ?? NULL,
        'part_leid_functie'             => $result['PART_LEID.Functie']                     ?? NULL,
        'part_vakantieregio'            => $result['PART.vakantieregio']                    ?? NULL,
        'kenmerken_kampkort'            => $ekort                                           ?? NULL,
        'kenmerken_kampkort_low'        => strtolower(preg_replace('/[^ \w-]/','',$ekort))  ?? NULL,
        'kenmerken_kampkort_cap'        => strtoupper(preg_replace('/[^ \w-]/','',$ekort))  ?? NULL,

        'groepklas'                     => $result['PART_DEEL.Groep_klas']                  ?? NULL,
        'voorkeur'                      => $result['PART_DEEL.Voorkeur']                    ?? NULL,

        'criteria_leeftijd'             => $result['PART_DEEL_INTERN.criteria_leeftijd']    ?? NULL,
        'criteria_school'               => $result['PART_DEEL_INTERN.criteria_school']      ?? NULL,
        'criteria_indicatie'            => $result['PART_DEEL_INTERN.criteria_indicatie']   ?? NULL,
        'criteria_oordeel'              => $result['PART_DEEL_INTERN.criteria_oordeel']     ?? NULL,
        'wachtlijst_erop'               => $result['PART_DEEL_INTERN.wachtlijst_erop']      ?? NULL,
        'wachtlijst_eraf'               => $result['PART_DEEL_INTERN.wachtlijst_eraf']      ?? NULL,
        'criteriacheck_start'           => $result['PART_DEEL_INTERN.criteriacheck_start']  ?? NULL,
        'criteriacheck_einde'           => $result['PART_DEEL_INTERN.criteriacheck_einde']  ?? NULL,

        'part_nawgecheckt'              => $result['PART.NAW_gecheckt']                     ?? NULL,
        'part_biogecheckt'              => $result['PART.BIO_gecheckt']                     ?? NULL,
        'part_groepklas'                => $result['PART_DEEL.Groep_klas']                  ?? NULL,
        'part_voorkeur'                 => $result['PART_DEEL.Voorkeur']                    ?? NULL,
        'part_groepsletter'             => $result['PART_INTERN.groep_letter']              ?? NULL,
        'part_groepskleur'              => $result['PART_INTERN.groep_kleur']               ?? NULL,
        'part_groepsnaam'               => $result['PART_INTERN.groep_naam']                ?? NULL,
        'part_slaapzaal'                => $result['PART_INTERN.Slaapzaal']                 ?? NULL,
        'part_kampgeld_contribid'       => $result['PART_KAMPGELD.contribid']               ?? NULL,
        'part_eval_datum'               => $result['PART_EVALUATIE.DATUM_evaluatie']        ?? NULL,
        'part_eval_terugblik'           => $result['PART_EVALUATIE.terugblik_score:label']  ?? NULL,
        'part_intstatus'                => $result['PART_LEID_INTERN.INT_status']           ?? NULL,
        'part_nawstatus'                => $result['PART_LEID_INTERN.NAW_status']           ?? NULL,
        'part_biostatus'                => $result['PART_LEID_INTERN.BIO_status']           ?? NULL,
        'part_refstatus'                => $result['PART_LEID_INTERN.REF_status']           ?? NULL,
        'part_vogstatus'                => $result['PART_LEID_INTERN.VOG_status']           ?? NULL,
        'part_vogdatum'                 => $result['PART_LEID_VOG.Datum_nieuwe_VOG']        ?? NULL,
        'part_refpersoon'               => $result['PART_LEID_REF.REF_persoon']             ?? NULL,
        'part_refgevraagd'              => $result['PART_LEID_REF.REF_gevraagd']            ?? NULL,
        'part_reffeedback'              => $result['PART_LEID_REF.REF_feedback']            ?? NULL,
        'part_refcid'                   => $result['PART_LEID_REFERENTIE.referentie_cid']   ?? NULL,
        'part_refnaam'                  => $result['PART_LEID_REFERENTIE.referentie_naam']  ?? NULL,
    );

    // --- 5. CACHE OPSLAAN & RETURN ---
    // Dit was het ontbrekende puzzelstukje in je oorspronkelijke versie!
    $pid_cache[$partid] = $partinfo_array;

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("EINDE pid2part"), NULL, WATCHDOG_DEBUG);
    }

    return $partinfo_array;
}

/**
 * Haalt uitgebreide event-informatie op basis van een Entity ID (Event ID).
 * * Deze functie is de 'bijbel' voor event-data. Hij kijkt niet alleen naar het basis-event,
 * maar snapt ook de relatie tussen een leiding-inschrijving en het bijbehorende kinderkamp.
 *
 * @param int $entityID   Het CiviCRM ID van het Event.
 * @param int|null $partID Optioneel: Het Participant ID (nodig om voor leiding het juiste kamp te vinden).
 * @return array|false     De verzamelde eventgegevens of false bij een fout.
 */

function base_eid2event(int $entityID, ?int $partID = NULL): ?array {

    // --- VEILIGHEIDSCHECK ---
    // Als we geen ID hebben, kunnen we niets zoeken. Stop direct om server-resources te sparen.
    if (empty($entityID)) return null;

    static $event_cache = [];
    $cache_key = $entityID . '_' . ($partID ?? '0');
    if (isset($event_cache[$cache_key])) return $event_cache[$cache_key];

    $extdebug = 0; 
    $apidebug = FALSE;

    // START PERFORMANCE METING
    // We loggen hoe lang deze functie duurt om bottlenecks in kaart te brengen.
    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("START eid2event voor EID: $entityID"), NULL, WATCHDOG_DEBUG);
    }

    wachthond($extdebug, 2, "########################################################################");
    wachthond($extdebug, 1, "### EID2EVENT: OPHALEN EVENT DATA [EID: $entityID]",            "[EVENT]");
    wachthond($extdebug, 2, "########################################################################");

    // --- 1. CONFIGURATIE: EVENT TYPES ---
    // We halen de centrale lijst met ID's op (bijv. wat is een 'topkamp', wat is 'test').
    // Dit doen we dynamisch zodat we bij een nieuw jaar alleen de centrale lijst hoeven aan te passen.
    $eventtypes             = get_event_types();

    $eventtypesdeel         = $eventtypes['deel'];
    $eventtypesdeeltop      = $eventtypes['deeltop'];
    $eventtypesleid         = $eventtypes['leid'];
    $eventtypesmeet         = $eventtypes['meet'];
    $eventtypesdeeltest     = $eventtypes['deeltest'];
    $eventtypesleidtest     = $eventtypes['leidtest'];
    $eventtypesdeeltoptest  = $eventtypes['toptest']; 

    // Gecombineerde lijsten voor makkelijke 'in_array' checks verderop.
    $eventtypesprod         = $eventtypes['prod'];
    $eventtypestest         = $eventtypes['test'];
    $eventtypesall          = $eventtypes['all'];
    $eventtypesdeelall      = $eventtypes['deel_all'];
    $eventtypesleidall      = $eventtypes['leid_all'];

    // --- 2. INITIALISEER BASIS GEGEVENS ---
    // We halen eerst de 'geboortedata' van het event op: wanneer begint het en wat voor type is het?
    $params_eventtype = [
        'checkPermissions' => FALSE,
        'select'           => ['id', 'start_date', 'end_date', 'event_type_id', 'event_type_id:label'],
        'where'            => [['id', '=', $entityID]],
    ];

    $res_type = civicrm_api4('Event', 'get', $params_eventtype)->first();
    if (!$res_type) return false;

    $ditevent_event_type_id = $res_type['event_type_id'];
    
    // BEPAAL HET BOEKJAAR
    // Veel filters werken op basis van het boekjaar (bijv. juli t/m juni).
    // De functie curriculum_civicrm_fiscalyear rekent dit voor ons uit op basis van de start_date.
    $fiscal                 = curriculum_civicrm_fiscalyear($res_type['start_date']);
    $f_start                = $fiscal['fiscalyear_start'];
    $f_einde                = $fiscal['fiscalyear_einde'];

    // --- 3. LOGICA VOOR LEIDING: ZOEK GEKOPPELD KAMP ---
    // Cruciaal: Een leidinggevende schrijft zich in voor een "Leiding-event", 
    // maar we willen de data zien van het "Kinderkamp" waar ze geplaatst zijn.
    $leid_welkkamp = NULL;
    $leid_functie  = NULL;

    if (in_array($ditevent_event_type_id, $eventtypesleidall) && $partID) {
        // We kijken in de inschrijving (Participant) welk kampkort er is ingevuld bij 'Welk kamp?'.
        $res_welkkamp = civicrm_api4('Participant', 'get', [
            'checkPermissions' => FALSE,
            'select'           => ['PART_LEID.Welk_kamp', 'PART_LEID.Functie'],
            'where'            => [['id', '=', $partID]],
        ])->first();

        $leid_welkkamp = $res_welkkamp['PART_LEID.Welk_kamp'] ?? NULL;
        $leid_functie  = $res_welkkamp['PART_LEID.Functie']  ?? NULL;
    }

    // --- 4. BEPAAL QUERY PARAMETERS VOOR DE HOOFD-DATA ---
    // Hier beslissen we welk event we écht gaan uitvragen in de grote query.
    $params_var = NULL;

    // SCENARIO A: Het is een deelnemer of bestuurslid. 
    // We gebruiken gewoon het ID van het event waar we nu in zitten.
    if (in_array($ditevent_event_type_id, $eventtypesdeelall) || $leid_functie == 'bestuurslid') {
        $params_var = [
            ['start_date', '>=', $f_start],
            ['start_date', '<=', $f_einde],
            ['id',         '=',  $entityID],
        ];
    } 
    // SCENARIO B: Het is leiding.
    // We zoeken het Kinderkamp dat dezelfde 'kampkort' heeft als ingevuld bij de leiding.
    elseif ($leid_welkkamp) {
        $params_var = [
            ['event_type_id',            'IN', $eventtypesdeelall],
            ['start_date',               '>=', $f_start],
            ['start_date',               '<=', $f_einde],
            ['Event_Kenmerken.kampkort', '=',  strtolower(trim($leid_welkkamp))],
            ['Event_Kenmerken.testevent', '!=', 1], // Geen testkampen meenemen in productie
        ];
    }

    // Als we geen match kunnen vinden, stoppen we.
    if (!$params_var) return false;

    // --- 5. DE GROTE DATA QUERY ---
    // Nu halen we ALLES op: kenmerken, linkjes naar video's, thema-info en wie de hoofdleiding is.
    // Alle velden staan hieronder los uitgeschreven voor maximale controle.
    $params_eventkamp = [
        'checkPermissions' => FALSE,
        'select' => [
            'id',
            'start_date',
            'end_date',
            'event_type_id',
            'event_type_id:label',
            'Event_Kenmerken.kampnaam',
            'Event_Kenmerken.kampkort',
            'Event_Kenmerken.kampweek_nr',
            'Event_Kenmerken.kamplocatie',
            'Event_Kenmerken.kampplaats',
            'Event_Kenmerken.kamplocatie:label',
            'Event_Kenmerken.kampplaats:label',
            'Event_Kenmerken.kamptype_naam',
            'Event_Kenmerken.kamptype_naam:label',
            'Event_Kenmerken.kamptype_id',
            'Event_Kenmerken.kampsoort',
            'Event_Kenmerken.brengen_van',
            'Event_Kenmerken.brengen_tot',
            'Event_Kenmerken.pres_van',
            'Event_Kenmerken.pres_tot',
            'Event_Kenmerken.halen_van',
            'Event_Kenmerken.halen_tot',
            'Event_Kenmerken.Fietshuur',
            'Event_Kenmerken.brengen',
            'Event_Kenmerken.halen',
            'Event_Kenmerken.afsluiting',
            'Event_Kenmerken_Linkjes.thema_naam',
            'Event_Kenmerken_Linkjes.thema_info',
            'Event_Kenmerken_Linkjes.goeddoel_naam',
            'Event_Kenmerken_Linkjes.goeddoel_info',
            'Event_Kenmerken_Linkjes.goeddoel_link',
            'Event_Kenmerken_Linkjes.welkomvideo',
            'Event_Kenmerken_Linkjes.slotvideo',
            'Event_Kenmerken_Linkjes.extrabagage',
            'Event_Kenmerken_Linkjes.doc_link',
            'Event_Kenmerken_Linkjes.doc_info',
            'Event_Kenmerken_Linkjes.playlist',
            'Event_Kenmerken_Linkjes.foto_vraag',
            'Event_Kenmerken_Linkjes.foto_album',
            'Taken_rollen.hoofdleiding_1',
            'Taken_rollen.hoofdleiding_2',
            'Taken_rollen.hoofdleiding_3',
            'Taken_rollen.kernteam_1',
            'Taken_rollen.kernteam_2',
            'Taken_rollen.kernteam_3',
            'Taken_rollen.hoofd_keuken',
            'Taken_rollen.hoofd_keuken_1',
            'Taken_rollen.hoofd_keuken_2',
            'Taken_rollen.hoofd_keuken_3',
            'Taken_rollen.hoofd_gedrag',
            'Taken_rollen.gedrag_team_1',
            'Taken_rollen.gedrag_team_2',
            'Taken_rollen.hoofd_boekje',
            'Taken_rollen.boekje_team_1',
            'Taken_rollen.boekje_team_2',
            'Taken_rollen.hoofd_ehbo',
            'Taken_rollen.ehbo_team_1',
            'Taken_rollen.ehbo_team_2',
            'Taken_rollen.ehbo_team_3',
            'Taken_rollen.hoofd_bhv',
            'Taken_rollen.hoofd_fotos',
            'Taken_rollen.hoofd_blogvlog',
        ],
        'where'  => $params_var,
    ];

    $result = civicrm_api4('Event', 'get', $params_eventkamp)->first();
    if (!$result) return null;

    // We prepareren de kampkort even los om berekeningen in de array te voorkomen.
    $kkort = $result['Event_Kenmerken.kampkort'] ?? '';

    // --- 6. DE RESULTAAT ARRAY ---
    // We bouwen een overzichtelijke 'map' van alle data.
    // ?? NULL zorgt ervoor dat als een veld leeg is in de DB, de code niet crasht.
    $eventinfo_array = array(
        'eventkamp_event_id'            => $result['id']                                    ?? NULL,
        'eventkamp_event_type_id'       => $result['event_type_id']                         ?? NULL,
        'eventkamp_event_type_id_label' => $result['event_type_id:label']                   ?? NULL,
        'eventkamp_kamptype_naam'       => $result['Event_Kenmerken.kamptype_naam']         ?? NULL,
        'eventkamp_kamptype_label'      => $result['Event_Kenmerken.kamptype_naam:label']   ?? NULL,
        'eventkamp_kamptype_id'         => $result['Event_Kenmerken.kamptype_id']           ?? NULL,
        'eventkamp_kampsoort'           => $result['Event_Kenmerken.kampsoort']             ?? NULL,
        'eventkamp_kampnaam'            => $result['Event_Kenmerken.kampnaam']              ?? NULL,
        'eventkamp_kampkort'            => $kkort                                           ?? NULL,
        'eventkamp_kampkort_low'        => strtolower(preg_replace('/[^ \w-]/','',$kkort))  ?? NULL,
        'eventkamp_kampkort_cap'        => strtoupper(preg_replace('/[^ \w-]/','',$kkort))  ?? NULL,
        'eventkamp_event_start'         => $result['start_date']                            ?? NULL,
        'eventkamp_event_einde'         => $result['end_date']                              ?? NULL,
        'eventkamp_event_weeknr'        => $result['Event_Kenmerken.kampweek_nr']           ?? NULL,
        'eventkamp_fiscalyear_start'    => $f_start,
        'eventkamp_fiscalyear_einde'    => $f_einde,
        'eventkamp_kampjaar'            => date('Y', strtotime($result['start_date'])),
        'eventkamp_kampjaarkort'        => date('y', strtotime($result['start_date'])),
        'eventkamp_plek'                => $result['Event_Kenmerken.kamplocatie']           ?? NULL,
        'eventkamp_stad'                => $result['Event_Kenmerken.kampplaats']            ?? NULL,
        'eventkamp_pleklang'            => $result['Event_Kenmerken.kamplocatie:label']     ?? NULL,
        'eventkamp_stadlang'            => $result['Event_Kenmerken.kampplaats:label']      ?? NULL,
        'eventkamp_fietsevent'          => $result['Event_Kenmerken.Fietshuur']             ?? NULL,
        'eventkamp_brengen_van'         => $result['Event_Kenmerken.brengen_van']           ?? NULL,
        'eventkamp_brengen_tot'         => $result['Event_Kenmerken.brengen_tot']           ?? NULL,
        'eventkamp_pres_van'            => $result['Event_Kenmerken.pres_van']              ?? NULL,
        'eventkamp_pres_tot'            => $result['Event_Kenmerken.pres_tot']              ?? NULL,
        'eventkamp_halen_van'           => $result['Event_Kenmerken.halen_van']             ?? NULL,
        'eventkamp_halen_tot'           => $result['Event_Kenmerken.halen_tot']             ?? NULL,
        'eventkamp_thema_naam'          => $result['Event_Kenmerken_Linkjes.thema_naam']    ?? NULL,
        'eventkamp_thema_info'          => $result['Event_Kenmerken_Linkjes.thema_info']    ?? NULL,
        'eventkamp_goeddoel_naam'       => $result['Event_Kenmerken_Linkjes.goeddoel_naam'] ?? NULL,
        'eventkamp_welkomvideo'         => $result['Event_Kenmerken_Linkjes.welkomvideo']   ?? NULL,
        'eventkamp_slotvideo'           => $result['Event_Kenmerken_Linkjes.slotvideo']     ?? NULL,
        'eventkamp_playlist'            => $result['Event_Kenmerken_Linkjes.playlist']      ?? NULL,
        'eventkamp_foto_album'          => $result['Event_Kenmerken_Linkjes.foto_album']    ?? NULL,
        'event_hldn1_id'                => $result['Taken_rollen.hoofdleiding_1']           ?? NULL,
        'event_hldn2_id'                => $result['Taken_rollen.hoofdleiding_2']           ?? NULL,
        'event_hldn3_id'                => $result['Taken_rollen.hoofdleiding_3']           ?? NULL,
        'event_kern1_id'                => $result['Taken_rollen.kernteam_1']               ?? NULL,
        'event_kern2_id'                => $result['Taken_rollen.kernteam_2']               ?? NULL,
        'event_kern3_id'                => $result['Taken_rollen.kernteam_3']               ?? NULL,
        'event_keuken0_id'              => $result['Taken_rollen.hoofd_keuken']             ?? NULL,
        'event_keuken1_id'              => $result['Taken_rollen.hoofd_keuken_1']           ?? NULL,
        'event_keuken2_id'              => $result['Taken_rollen.hoofd_keuken_2']           ?? NULL,
        'event_keuken3_id'              => $result['Taken_rollen.hoofd_keuken_3']           ?? NULL,
        'event_gedrag0_id'              => $result['Taken_rollen.hoofd_gedrag']             ?? NULL,
        'event_gedrag1_id'              => $result['Taken_rollen.gedrag_team_1']            ?? NULL,
        'event_gedrag2_id'              => $result['Taken_rollen.gedrag_team_2']            ?? NULL,
        'event_boekje0_id'              => $result['Taken_rollen.hoofd_boekje']             ?? NULL,
        'event_boekje1_id'              => $result['Taken_rollen.boekje_team_1']            ?? NULL,
        'event_boekje2_id'              => $result['Taken_rollen.boekje_team_2']            ?? NULL,
        'event_ehbo0_id'                => $result['Taken_rollen.hoofd_ehbo']               ?? NULL,
        'event_ehbo1_id'                => $result['Taken_rollen.ehbo_team_1']              ?? NULL,
        'event_ehbo2_id'                => $result['Taken_rollen.ehbo_team_2']              ?? NULL,
        'event_ehbo3_id'                => $result['Taken_rollen.ehbo_team_3']              ?? NULL,
        'event_bhv_id'                  => $result['Taken_rollen.hoofd_bhv']                ?? NULL,
        'event_fotos_id'                => $result['Taken_rollen.hoofd_fotos']              ?? NULL,
        'event_blogs_id'                => $result['Taken_rollen.hoofd_blogvlog']           ?? NULL,
    );

    // --- 7. CACHE OPSLAAN ---
    $event_cache[$entityID] = $eventinfo_array;

    // STOP PERFORMANCE METING
    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("EINDE eid2event"), NULL, WATCHDOG_DEBUG);
    }

    return $eventinfo_array;
}

function base_find_hldn_info($hldn_id) {

    ##########################################################################################
    # VIND DE HOOFDLEIDING INFO VAN DIT EVENEMENT
    ##########################################################################################

    if (empty($hldn_id)) {
        return false;
    }

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    $eventkamp_event_hldn_id   = $hldn_id;

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "### BASE - GET HOOFDLEIDING INFO VAN DIT EVENT");
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,3, 'eventkamp_hoofdleid_id',    $eventkamp_event_hldn_id);

    #######################################################################################
    // HOOFDLEIDING
    #######################################################################################
    $params_contact = [
        'checkPermissions' => FALSE,
        'debug' => $apidebug,
            'select' => [
                'display_name', 'first_name', 'image_URL',
            ],
            'where' => [
                ['id', 'IN', [$eventkamp_event_hldn_id]],
            ],
    ];
    $params_phone = [
        'checkPermissions' => FALSE,
        'debug' => $apidebug,
            'select' => [
                'phone', 'contact_id.do_not_phone',
            ],
            'where' => [
                ['contact_id',       'IN', [$eventkamp_event_hldn_id]],
                ['location_type_id', '=', 1],
            ],
    ];
//  wachthond($extdebug,7, 'params_contact',    $params_contact);
    $result1 = civicrm_api4('Contact',  'get',  $params_contact);
//  wachthond($extdebug,7, 'params_phone',      $params_phone);
    $result2 = civicrm_api4('Phone',    'get',  $params_phone);

    if (isset($eventkamp_event_hldn_id))   {
        $event_hoofdleiding_displname = $result1[0]['display_name']             ?? NULL;
        $event_hoofdleiding_firstname = $result1[0]['first_name']               ?? NULL;
        $event_hoofdleiding_image     = $result1[0]['image_URL']                ?? NULL;

        $event_hoofdleiding_image_bn  = basename($event_hoofdleiding_image);                      # BASENAME
        wachthond($extdebug,2, 'event_hoofdleiding_image_bn',       $event_hoofdleiding_image_bn);

        if ($event_hoofdleiding_image_bn) {
            $event_hoofdleiding_image_bn  = str_replace("imagefile?photo=", "", $event_hoofdleiding_image_bn);
            wachthond($extdebug,2, 'event_hoofdleiding_image_bn',   $event_hoofdleiding_image_bn);
        }
        $event_hoofdleiding_image_bn  = substr($event_hoofdleiding_image_bn, 0, strpos($event_hoofdleiding_image_bn, "?"));
        wachthond($extdebug,2, 'event_hoofdleiding_image_bn',       $event_hoofdleiding_image_bn);

        $event_hoofdleiding_phone     = $result2[0]['phone']                    ?? NULL;
        $event_hoofdleiding_dontphone = $result2[0]['contact_id.do_not_phone']  ?? NULL;
        wachthond($extdebug,2, 'hoofdleiding_displname', $event_hoofdleiding_displname);
    } else {
        $event_hoofdleiding_displname = "";
        $event_hoofdleiding_firstname = "";
        $event_hoofdleiding_image     = "";
        $event_hoofdleiding_image_bn  = "";
        $event_hoofdleiding_phone     = "";
        $event_hoofdleiding_dontphone = "";
    }

    $hldn_info_array = array(
        'eventkamp_event_hldn1_id'      => $eventkamp_event_hldn_id,
        'event_hoofdleiding_displname'  => $event_hoofdleiding_displname,
        'event_hoofdleiding_firstname'  => $event_hoofdleiding_firstname,
        'event_hoofdleiding_image'      => $event_hoofdleiding_image,        
        'event_hoofdleiding_image_bn'   => $event_hoofdleiding_image_bn,
        'event_hoofdleiding_phone'      => $event_hoofdleiding_phone,
        'event_hoofdleiding_dontphone'  => $event_hoofdleiding_dontphone,
    );
    return $hldn_info_array;

}

function base_find_allpart($contactid, $refdate = NULL) {

    // --- STATIC CACHE ---
    static $local_cache = [];
    
    // --- VEILIGHEIDSCHECK ---
    if (empty($contactid)) return false;
    $contact_id = (int)$contactid;

    $extdebug = 0; 
    $apidebug = FALSE;

    // Als we dit contact al hebben gedaan in deze sessie, geef cache terug
    if (isset($local_cache[$contact_id])) {
        wachthond($extdebug, 1, "CACHE HIT base_find_allpart", "CID: $contact_id");
        return $local_cache[$contact_id];
    } else {
        wachthond($extdebug, 1, "CACHE MISS base_find_allpart", "CID: $contact_id / Date: $refdate");
    }

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("START base_find_allpart"), NULL, WATCHDOG_DEBUG);
    }

    // --- DATUM CONFIGURATIE ---
    if (empty($refdate)) $refdate = date("Y-m-d H:i:s");
    $refyear = date('Y', strtotime($refdate));

    // =========================================================================
    // STAP 1: SNELLE CACHE DATA OPHALEN
    // =========================================================================
    $event_id_data = find_eventids();
    $status_data   = find_partstatus();

    // Filter Arrays (uit cache)
    $alle_ids = array_unique(array_merge($event_id_data['all'] ?? [], $event_id_data['test_all'] ?? []));
    $ids_deel = $event_id_data['deel_all'] ?? []; 
    $ids_leid = $event_id_data['leid_all'] ?? [];

    // DEBUG: Zijn de filters gevuld vanuit de cache?
    wachthond($extdebug, 2, "base_find_allpart: Cache Filters Geladen", [
        'contact_id'      => $contact_id,
        'totaal_alle_ids' => count($alle_ids),
        'totaal_deel_ids' => count($ids_deel),
        'totaal_leid_ids' => count($ids_leid),
        'sample_ids'      => array_slice($alle_ids, 0, 5)
    ]);

    // Status Arrays
    $status_positive = $status_data['ids']['Positive'] ?? [];
    $status_pending  = $status_data['ids']['Pending']  ?? [];
    $status_waiting  = $status_data['ids']['Waiting']  ?? [];
    $status_negative = $status_data['ids']['Negative'] ?? [];

    // =========================================================================
    // STAP 2: VARIABELEN INITIALISEREN
    // =========================================================================
    
    // --- Tellers ---
    $result_allpart_pen_count      = 0;  $result_allpart_wait_count      = 0;  $result_allpart_neg_count      = 0;
    $result_allpart_pos_count      = 0;  $result_allpart_pos_deel_count  = 0;  $result_allpart_pos_leid_count  = 0;
    $result_allpart_all_count      = 0;  $result_allpart_all_deel_count  = 0;  $result_allpart_all_leid_count  = 0;

    // --- Variabelen "One" (Laatste item) ---
    $diteventjaar_one_part_id       = NULL;    $diteventjaar_one_deel_part_id       = NULL;    $diteventjaar_one_leid_part_id       = NULL;
    $diteventjaar_one_event_id      = NULL;    $diteventjaar_one_deel_event_id      = NULL;    $diteventjaar_one_leid_event_id      = NULL;
    $diteventjaar_one_event_type_id = NULL;    $diteventjaar_one_deel_event_type_id = NULL;    $diteventjaar_one_leid_event_type_id = NULL;
    $diteventjaar_one_status_id     = NULL;    $diteventjaar_one_deel_status_id     = NULL;    $diteventjaar_one_leid_status_id     = NULL;
    $diteventjaar_one_kampfunctie   = NULL;    $diteventjaar_one_deel_kampfunctie   = NULL;    $diteventjaar_one_leid_kampfunctie   = NULL;
    $diteventjaar_one_kampkort      = NULL;    $diteventjaar_one_deel_kampkort      = NULL;    $diteventjaar_one_leid_kampkort      = NULL;
    $diteventjaar_one_kamprol       = NULL;    $diteventjaar_one_event_start        = NULL;    $diteventjaar_one_event_einde        = NULL;

    // --- Variabelen "Pos" (Laatste positieve) ---
    $diteventjaar_pos_part_id       = NULL;    $diteventjaar_pos_deel_part_id       = NULL;    $diteventjaar_pos_leid_part_id       = NULL;
    $diteventjaar_pos_event_id      = NULL;    $diteventjaar_pos_deel_event_id      = NULL;    $diteventjaar_pos_leid_event_id      = NULL;
    $diteventjaar_pos_event_type_id = NULL;    $diteventjaar_pos_deel_event_type_id = NULL;    $diteventjaar_pos_leid_event_type_id = NULL;
    $diteventjaar_pos_status_id     = NULL;    $diteventjaar_pos_deel_status_id     = NULL;    $diteventjaar_pos_leid_status_id     = NULL;
    $diteventjaar_pos_kampfunctie   = NULL;    $diteventjaar_pos_deel_kampfunctie   = NULL;    $diteventjaar_pos_leid_kampfunctie   = NULL;
    $diteventjaar_pos_kampkort      = NULL;    $diteventjaar_pos_deel_kampkort      = NULL;    $diteventjaar_pos_leid_kampkort      = NULL;
    $diteventjaar_pos_kamprol       = NULL;    $diteventjaar_pos_event_start        = NULL;    $diteventjaar_pos_event_einde        = NULL;

    // --- Variabelen "Wait" (Laatste wachtlijst) ---
    $diteventjaar_wait_part_id      = NULL;    $diteventjaar_wait_deel_part_id      = NULL;    $diteventjaar_wait_leid_part_id      = NULL;
    $diteventjaar_wait_event_id     = NULL;    $diteventjaar_wait_deel_event_id     = NULL;    $diteventjaar_wait_leid_event_id     = NULL;
    $diteventjaar_wait_event_type_id= NULL;    $diteventjaar_wait_deel_event_type_id= NULL;    $diteventjaar_wait_leid_event_type_id= NULL;
    $diteventjaar_wait_status_id    = NULL;    $diteventjaar_wait_deel_status_id    = NULL;    $diteventjaar_wait_leid_status_id    = NULL;
    $diteventjaar_wait_kampkort     = NULL;    $diteventjaar_wait_deel_kampkort     = NULL;    $diteventjaar_wait_leid_kampkort     = NULL;

    // --- Variabelen "Pen" (Laatste pending) ---
    $diteventjaar_pen_part_id       = NULL;    $diteventjaar_pen_deel_part_id       = NULL;    $diteventjaar_pen_leid_part_id       = NULL;
    $diteventjaar_pen_event_id      = NULL;    $diteventjaar_pen_deel_event_id      = NULL;    $diteventjaar_pen_leid_event_id      = NULL;
    $diteventjaar_pen_event_type_id = NULL;    $diteventjaar_pen_deel_event_type_id = NULL;    $diteventjaar_pen_leid_event_type_id = NULL;
    $diteventjaar_pen_status_id     = NULL;    $diteventjaar_pen_deel_status_id     = NULL;    $diteventjaar_pen_leid_status_id     = NULL;
    $diteventjaar_pen_kampkort      = NULL;    $diteventjaar_pen_deel_kampkort      = NULL;    $diteventjaar_pen_leid_kampkort      = NULL;

    // =========================================================================
    // STAP 3: DE QUERY (APIv4)
    // =========================================================================
    $params_allpart = [
        'checkPermissions'  => FALSE,
        'debug'             => $apidebug,
        'orderBy'           => ['event_id' => 'ASC'],
        'select'            => [
            'id', 
            'contact_id', 
            'contact_id.display_name', 
            'status_id', 
            'status_id:name', 
            'role_id', 
            'register_date', 
            'event_id',
            'PART.PART_kamplang', 
            'PART.PART_kampkort', 
            'PART.PART_kamptype_naam', 
            'PART.PART_kamptype_id', 
            'PART.PART_kampfunctie', 
            'PART.PART_kamprol', 
            'PART_LEID.Welk_kamp', 
            'PART_LEID.Functie',
        ],
        'where' => [
            ['contact_id', '=',     $contact_id],
            ['event_id',   'IN',    $alle_ids], 
        ], 
    ];

    wachthond($extdebug,3, "params_allpart",            $params_allpart);
    $result_allpart = civicrm_api4('Participant','get', $params_allpart);
    wachthond($extdebug,3, "result_allpart",            $result_allpart);
    
    // DEBUG: API Resultaat controle
    wachthond($extdebug, 1, "base_find_allpart: API Output", [
        'aantal_gevonden' => count($result_allpart),
        'contact_id'      => $contact_id,
        'eerste_event_id' => $result_allpart[0]['event_id'] ?? 'GEEN'
    ]);

    $displayname = $result_allpart[0]['contact_id.display_name'] ?? NULL;

    // =========================================================================
    // STAP 4: DE LOOP (BEREKENINGEN)
    // =========================================================================
    foreach ($result_allpart as $part) {
        $eid = $part['event_id'];
        $pid = $part['id'];
        $sid = $part['status_id'];
        
        // Verrijk met Event Data (Supersnel uit cache)
        $event_details = base_eid2event($eid, $pid);
        if (!$event_details) {
            wachthond($extdebug, 2, "base_find_allpart: SKIP PID $pid (geen event details voor EID $eid)");
            continue;
        }

        // Lokale vars
        $tid   = $event_details['eventkamp_event_type_id'];
        $start = $event_details['eventkamp_event_start'];
        $end   = $event_details['eventkamp_event_einde'];
        $kort  = $part['PART.PART_kampkort'] ?? $event_details['eventkamp_kampkort']; 
        $func  = $part['PART.PART_kampfunctie'] ?? $part['PART_LEID.Functie'];
        $rol   = $part['PART.PART_kamprol'];

        $is_leid = in_array($eid, $ids_leid);
        $is_deel = in_array($eid, $ids_deel);

        // DEBUG: Details per verwerkt record
        if ($extdebug >= 3) {
            wachthond($extdebug, 3, "base_find_allpart: Verwerk PID $pid", [
                'EID' => $eid,
                'Kort' => $kort,
                'Status' => $sid,
                'Type' => ($is_leid ? 'LEID ' : '') . ($is_deel ? 'DEEL' : '')
            ]);
        }

        // --- A. TELLERS ---
        $result_allpart_all_count++;
        if ($is_deel) {
            $result_allpart_all_deel_count++;
        }
        if ($is_leid) {
            $result_allpart_all_leid_count++;
        }

        if (in_array($sid, $status_positive)) {
            $result_allpart_pos_count++;
            if ($is_deel) {
                $result_allpart_pos_deel_count++;
            }
            if ($is_leid) {
                $result_allpart_pos_leid_count++;
            }
        }
        if (in_array($sid, $status_pending)) {
            $result_allpart_pen_count++;
        }
        if (in_array($sid, $status_waiting)) {
            $result_allpart_wait_count++;
        }
        if (in_array($sid, $status_negative)) {
            $result_allpart_neg_count++;
        }

        // --- B. "ONE" VARIABELEN (Laatste item) ---
        $diteventjaar_one_part_id       = $pid;
        $diteventjaar_one_event_id      = $eid;
        $diteventjaar_one_event_type_id = $tid;
        $diteventjaar_one_status_id     = $sid;
        $diteventjaar_one_kamprol       = $rol;
        $diteventjaar_one_event_start   = $start;
        $diteventjaar_one_event_einde   = $end;
        $diteventjaar_one_kampfunctie   = $func;
        $diteventjaar_one_kampkort      = $kort;

        if ($is_deel) {
            $diteventjaar_one_deel_part_id       = $pid;
            $diteventjaar_one_deel_event_id      = $eid;
            $diteventjaar_one_deel_event_type_id = $tid;
            $diteventjaar_one_deel_status_id     = $sid;
            $diteventjaar_one_deel_kampfunctie   = $func;
            $diteventjaar_one_deel_kampkort      = $kort;
        }
        if ($is_leid) {
            $diteventjaar_one_leid_part_id       = $pid;
            $diteventjaar_one_leid_event_id      = $eid;
            $diteventjaar_one_leid_event_type_id = $tid;
            $diteventjaar_one_leid_status_id     = $sid;
            $diteventjaar_one_leid_kampfunctie   = $func;
            $diteventjaar_one_leid_kampkort      = $kort;
        }

        // --- C. "POS" VARIABELEN ---
        if (in_array($sid, $status_positive)) {
            $diteventjaar_pos_part_id       = $pid;
            $diteventjaar_pos_event_id      = $eid;
            $diteventjaar_pos_event_type_id = $tid;
            $diteventjaar_pos_status_id     = $sid;
            $diteventjaar_pos_kamprol       = $rol;
            $diteventjaar_pos_event_start   = $start;
            $diteventjaar_pos_event_einde   = $end;
            $diteventjaar_pos_kampfunctie   = $func;
            $diteventjaar_pos_kampkort      = $kort;

            if ($is_deel) {
                $diteventjaar_pos_deel_part_id       = $pid;
                $diteventjaar_pos_deel_event_id      = $eid;
                $diteventjaar_pos_deel_event_type_id = $tid;
                $diteventjaar_pos_deel_status_id     = $sid;
                $diteventjaar_pos_deel_kampfunctie   = $func;
                $diteventjaar_pos_deel_kampkort      = $kort;
            }
            if ($is_leid) {
                $diteventjaar_pos_leid_part_id       = $pid;
                $diteventjaar_pos_leid_event_id      = $eid;
                $diteventjaar_pos_leid_event_type_id = $tid;
                $diteventjaar_pos_leid_status_id     = $sid;
                $diteventjaar_pos_leid_kampfunctie   = $func;
                $diteventjaar_pos_leid_kampkort      = $kort;
            }
        }

        // --- D. "WAIT" VARIABELEN ---
        if (in_array($sid, $status_waiting)) {
            $diteventjaar_wait_part_id       = $pid;
            $diteventjaar_wait_event_id      = $eid;
            $diteventjaar_wait_event_type_id = $tid;
            $diteventjaar_wait_status_id     = $sid;
            $diteventjaar_wait_kampkort      = $kort;

            if ($is_deel) {
                $diteventjaar_wait_deel_part_id       = $pid;
                $diteventjaar_wait_deel_event_id      = $eid;
                $diteventjaar_wait_deel_event_type_id = $tid;
                $diteventjaar_wait_deel_status_id     = $sid;
                $diteventjaar_wait_deel_kampkort      = $kort;
            }
            if ($is_leid) {
                $diteventjaar_wait_leid_part_id       = $pid;
                $diteventjaar_wait_leid_event_id      = $eid;
                $diteventjaar_wait_leid_event_type_id = $tid;
                $diteventjaar_wait_leid_status_id     = $sid;
                $diteventjaar_wait_leid_kampkort      = $kort;
            }
        }

        // --- E. "PEN" VARIABELEN ---
        if (in_array($sid, $status_pending)) {
            $diteventjaar_pen_part_id       = $pid;
            $diteventjaar_pen_event_id      = $eid;
            $diteventjaar_pen_event_type_id = $tid;
            $diteventjaar_pen_status_id     = $sid;
            $diteventjaar_pen_kampkort      = $kort;

            if ($is_deel) {
                $diteventjaar_pen_deel_part_id       = $pid;
                $diteventjaar_pen_deel_event_id      = $eid;
                $diteventjaar_pen_deel_event_type_id = $tid;
                $diteventjaar_pen_deel_status_id     = $sid;
                $diteventjaar_pen_deel_kampkort      = $kort;
            }
            if ($is_leid) {
                $diteventjaar_pen_leid_part_id       = $pid;
                $diteventjaar_pen_leid_event_id      = $eid;
                $diteventjaar_pen_leid_event_type_id = $tid;
                $diteventjaar_pen_leid_status_id     = $sid;
                $diteventjaar_pen_leid_kampkort      = $kort;
            }
        }
    }

    // =========================================================================
    // STAP 5: RETOURNEREN
    // =========================================================================
    $eventjaar_array = array(
        'contact_id'                            => $contact_id,
        'displayname'                           => $displayname,
        'refdate'                               => $refdate,
        'refyear'                               => $refyear,

        'result_allpart_pen_count'              => $result_allpart_pen_count,
        'result_allpart_wait_count'             => $result_allpart_wait_count,
        'result_allpart_neg_count'              => $result_allpart_neg_count,

        'result_allpart_pos_count'              => $result_allpart_pos_count,
        'result_allpart_pos_deel_count'         => $result_allpart_pos_deel_count,
        'result_allpart_pos_leid_count'         => $result_allpart_pos_leid_count,

        'result_allpart_all_count'              => $result_allpart_all_count,
        'result_allpart_all_deel_count'         => $result_allpart_all_deel_count,
        'result_allpart_all_leid_count'         => $result_allpart_all_leid_count,

        'result_allpart_one_part_id'            => $diteventjaar_one_part_id,
        'result_allpart_one_deel_part_id'       => $diteventjaar_one_deel_part_id,
        'result_allpart_one_leid_part_id'       => $diteventjaar_one_leid_part_id,

        'result_allpart_one_event_id'           => $diteventjaar_one_event_id,
        'result_allpart_one_deel_event_id'      => $diteventjaar_one_deel_event_id,
        'result_allpart_one_leid_event_id'      => $diteventjaar_one_leid_event_id,

        'result_allpart_one_event_type_id'      => $diteventjaar_one_event_type_id,
        'result_allpart_one_deel_event_type_id' => $diteventjaar_one_deel_event_type_id,
        'result_allpart_one_leid_event_type_id' => $diteventjaar_one_leid_event_type_id,

        'result_allpart_one_status_id'          => $diteventjaar_one_status_id,
        'result_allpart_one_deel_status_id'     => $diteventjaar_one_deel_status_id,
        'result_allpart_one_leid_status_id'     => $diteventjaar_one_leid_status_id,

        'result_allpart_one_kamprol'            => $diteventjaar_one_kamprol,
        'result_allpart_one_event_start'        => $diteventjaar_one_event_start,
        'result_allpart_one_event_einde'        => $diteventjaar_one_event_einde,

        'result_allpart_one_kampfunctie'        => $diteventjaar_one_kampfunctie,
        'result_allpart_one_deel_kampfunctie'   => $diteventjaar_one_deel_kampfunctie,
        'result_allpart_one_leid_kampfunctie'   => $diteventjaar_one_leid_kampfunctie,

        'result_allpart_one_kampkort'           => $diteventjaar_one_kampkort,
        'result_allpart_one_deel_kampkort'      => $diteventjaar_one_deel_kampkort,
        'result_allpart_one_leid_kampkort'      => $diteventjaar_one_leid_kampkort,

        'result_allpart_pos_part_id'            => $diteventjaar_pos_part_id,
        'result_allpart_pos_deel_part_id'       => $diteventjaar_pos_deel_part_id,
        'result_allpart_pos_leid_part_id'       => $diteventjaar_pos_leid_part_id,

        'result_allpart_pos_event_id'           => $diteventjaar_pos_event_id,
        'result_allpart_pos_deel_event_id'      => $diteventjaar_pos_deel_event_id,
        'result_allpart_pos_leid_event_id'      => $diteventjaar_pos_leid_event_id,

        'result_allpart_pos_event_type_id'      => $diteventjaar_pos_event_type_id,
        'result_allpart_pos_deel_event_type_id' => $diteventjaar_pos_deel_event_type_id,
        'result_allpart_pos_leid_event_type_id' => $diteventjaar_pos_leid_event_type_id,

        'result_allpart_pos_status_id'          => $diteventjaar_pos_status_id,
        'result_allpart_pos_deel_status_id'     => $diteventjaar_pos_deel_status_id,
        'result_allpart_pos_leid_status_id'     => $diteventjaar_pos_leid_status_id,

        'result_allpart_pos_kamprol'            => $diteventjaar_pos_kamprol,
        'result_allpart_pos_event_start'        => $diteventjaar_pos_event_start,
        'result_allpart_pos_event_einde'        => $diteventjaar_pos_event_einde,

        'result_allpart_pos_kampfunctie'        => $diteventjaar_pos_kampfunctie,
        'result_allpart_pos_deel_kampfunctie'   => $diteventjaar_pos_deel_kampfunctie,
        'result_allpart_pos_leid_kampfunctie'   => $diteventjaar_pos_leid_kampfunctie,

        'result_allpart_pos_kampkort'           => $diteventjaar_pos_kampkort,
        'result_allpart_pos_deel_kampkort'      => $diteventjaar_pos_deel_kampkort,
        'result_allpart_pos_leid_kampkort'      => $diteventjaar_pos_leid_kampkort,

        'result_allpart_wait_part_id'           => $diteventjaar_wait_part_id,
        'result_allpart_wait_deel_part_id'      => $diteventjaar_wait_deel_part_id,
        'result_allpart_wait_leid_part_id'      => $diteventjaar_wait_leid_part_id,

        'result_allpart_wait_event_id'          => $diteventjaar_wait_event_id,
        'result_allpart_wait_deel_event_id'     => $diteventjaar_wait_deel_event_id,
        'result_allpart_wait_leid_event_id'     => $diteventjaar_wait_leid_event_id,

        'result_allpart_wait_event_type_id'     => $diteventjaar_wait_event_type_id,
        'result_allpart_wait_deel_event_type_id'=> $diteventjaar_wait_deel_event_type_id,
        'result_allpart_wait_leid_event_type_id'=> $diteventjaar_wait_leid_event_type_id,

        'result_allpart_wait_status_id'         => $diteventjaar_wait_status_id,
        'result_allpart_wait_deel_status_id'    => $diteventjaar_wait_deel_status_id,
        'result_allpart_wait_leid_status_id'    => $diteventjaar_wait_leid_status_id,

        'result_allpart_wait_kampkort'          => $diteventjaar_wait_kampkort,
        'result_allpart_wait_deel_kampkort'     => $diteventjaar_wait_deel_kampkort,
        'result_allpart_wait_leid_kampkort'     => $diteventjaar_wait_leid_kampkort,

        'result_allpart_pen_part_id'            => $diteventjaar_pen_part_id,
        'result_allpart_pen_deel_part_id'       => $diteventjaar_pen_deel_part_id,
        'result_allpart_pen_leid_part_id'       => $diteventjaar_pen_leid_part_id,

        'result_allpart_pen_event_id'           => $diteventjaar_pen_event_id,
        'result_allpart_pen_deel_event_id'      => $diteventjaar_pen_deel_event_id,
        'result_allpart_pen_leid_event_id'      => $diteventjaar_pen_leid_event_id,

        'result_allpart_pen_event_type_id'      => $diteventjaar_pen_event_type_id,
        'result_allpart_pen_deel_event_type_id' => $diteventjaar_pen_deel_event_type_id,
        'result_allpart_pen_leid_event_type_id' => $diteventjaar_pen_leid_event_type_id,

        'result_allpart_pen_status_id'          => $diteventjaar_pen_status_id,
        'result_allpart_pen_deel_status_id'     => $diteventjaar_pen_deel_status_id,
        'result_allpart_pen_leid_status_id'     => $diteventjaar_pen_leid_status_id,

        'result_allpart_pen_kampkort'           => $diteventjaar_pen_kampkort,
        'result_allpart_pen_deel_kampkort'      => $diteventjaar_pen_deel_kampkort,
        'result_allpart_pen_leid_kampkort'      => $diteventjaar_pen_leid_kampkort,
    );

    // DEBUG: Eindresultaat voor retour
    wachthond($extdebug, 2, "base_find_allpart: EINDRESULTAAT", [
        'pos_count' => $result_allpart_pos_count,
        'pos_leid'  => $result_allpart_pos_leid_count,
        'one_eid'   => $diteventjaar_one_event_id
    ]);

    // --- CACHE VULLEN (DIT MISTE JE!) ---
    $local_cache[$contact_id] = $eventjaar_array;

    if (function_exists('core_microtimer')) {
        watchdog('civicrm_timing', core_microtimer("EINDE base_find_allpart"), NULL, WATCHDOG_DEBUG);
    }

    return $eventjaar_array;
}


if (!function_exists('format_civicrm_string')) {
    /**
     * Formateert CiviCRM multi-select strings (^Awaarde^A)
     * Voorkomt PHP 8 TypeErrors en logt afwijkingen naar Drupal Watchdog.
     */
    function format_civicrm_string($input) {
        // Als het leeg is, direct een lege string teruggeven
        if (empty($input)) {
            return '';
        }

        // DEBUG & LOGGING: Als de input een array is, loggen we dit
        if (is_array($input)) {
            $debug_data = print_r($input, TRUE);
            $backtrace  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller     = isset($backtrace[1]['function']) ? $backtrace[1]['function'] : 'unknown';

            if (function_exists('watchdog')) {
                watchdog('civicrm_type_debug', 
                    'format_civicrm_string ontving een array in plaats van een string/waarde in functie: %caller. Data: <pre>%data</pre>', 
                    array('%caller' => $caller, '%data' => $debug_data), 
                    WATCHDOG_NOTICE
                );
            }
        }

        // VERWERKING: Zorg dat we altijd met een array eindigen voor implode
        $array_to_process = is_array($input) ? $input : array($input);

        // Opschonen: verwijder lege waarden en zorg dat elk item een string is
        $clean_array = array();
        foreach ($array_to_process as $item) {
            if ($item !== '' && $item !== NULL) {
                $clean_array[] = (string)$item;
            }
        }

        if (empty($clean_array)) {
            return '';
        }

        // Return het CiviCRM formaat: ^Awaarde1^Awaarde2^A
        return '' . implode('', array_values(array_unique($clean_array))) . '';
    }
}

/**
 * Dwingt datumvelden naar Unix Timestamps voor Drupal Entity compatibiliteit.
 * Maakt gebruik van CiviCRM metadata om te bepalen of een veld een datum is.
 * Inclusief caching voor optimale snelheid.
 */
/**
 * Hybride sweep voor Drupal Entity compatibiliteit.
 * Repareert zowel diepe arrays (Civi hooks) als platte arrays (Webform/Drupal login).
 */
function drupal_timestamp_sweep(array &$params): void {

    static $dateFieldMetadataCache = [];

    foreach ($params as $key => $param) {
        $customFieldId = null;
        $columnName = '';
        $currentVal = null;

        // --- STAP 1: IDENTIFICEER STRUCTUUR EN WAARDE ---
        if (is_array($param) && isset($param['column_name'])) {
            // Structuur: CiviCRM Hook (diep)
            $columnName     = $param['column_name'];
            $currentVal     = $param['value'] ?? null;
            $target         = &$params[$key]['value'];
        } else {
            // Structuur: Webform / Platte array (zoals custom_1505 => 20260103)
            $columnName     = $key;
            $currentVal     = $param;
            $target         = &$params[$key];
        }

        if (empty($currentVal)) continue;

        // --- STAP 2: BEPAAL OF HET EEN DATUMVELD IS ---
        $isDateField = false;

        // Check Custom Field ID (bv custom_1505 of naw_gecheckt_1505)
        if (preg_match('/(?:custom_|_|)(\d+)$/', $columnName, $matches)) {
            $customFieldId = (int)$matches[1];
            if (isset($dateFieldMetadataCache[$customFieldId])) {
                $isDateField = $dateFieldMetadataCache[$customFieldId];
            } else {
                try {
                    $field = civicrm_api4('CustomField', 'get', [
                        'select' => ['data_type'],
                        'where' => [['id', '=', $customFieldId]],
                        'checkPermissions' => FALSE,
                    ])->first();
                    $isDateField = (($field['data_type'] ?? '') === 'Date');
                    $dateFieldMetadataCache[$customFieldId] = $isDateField;
                } catch (\Exception $e) {
                    $isDateField = false;
                }
            }
        } else {
            // Core velden fallback
            $coreDateFields = ['birth_date', 'created_date', 'modified_date', 'register_date', 'receive_date'];
            if (in_array($columnName, $coreDateFields)) {
                $isDateField = true;
            }
        }

        // --- STAP 3: CONVERTEER NAAR TIMESTAMP VOOR DRUPAL ---
        if ($isDateField && is_string($currentVal)) {
            $timestamp = strtotime($currentVal);
            if ($timestamp !== false) {
                // CiviCRM verwacht voor 'Timestamp' velden vaak dit formaat: YYYYMMDDHHIISS
                $target = date('YmdHis', $timestamp); 
            }
        }
    }
}

/**
 * Gepantserde helper die op basis van CiviCRM metadata bepaalt hoe een waarde opgeslagen moet worden.
 * Ondersteunt zowel kolomnamen (naw_gecheckt_1505) als APIv4 namen (Curriculum.CV_Deel).
 * * @param mixed $input De ruwe waarde (string, array, null).
 * @param string $fieldName De technische naam van het veld.
 * @return string De technisch correcte waarde voor CiviCRM/Database.
 */
function format_civicrm_smart($input, string $fieldName): string {

    // Definieer debug niveau (kan later via een globale variabele)
    $smart_debug = 0; 

    // 1. Snelle exit bij leeg (behoud de waarde 0 als valide input)
    if (empty($input) && $input !== 0 && $input !== '0') {
        return '';
    }

    static $fieldMetaCache = [];

    // 2. Metadata ophalen (Cache check om database te sparen)
    if (!isset($fieldMetaCache[$fieldName])) {
        try {
            $field = null;

            // Stap 2a: Probeer eerst een numeriek ID te vinden (voor kolomnamen zoals naw_gecheckt_1505)
            if (preg_match('/_(\d+)$/', $fieldName, $matches)) {
                $fieldId = (int)$matches[1];
                $field = civicrm_api4('CustomField', 'get', [
                    'select'    => ['data_type', 'html_type', 'label'],
                    'where'     => [['id', '=', $fieldId]],
                    'checkPermissions' => FALSE,
                ])->first();
                
                if ($field) {
                    wachthond($smart_debug, 4, "SmartHelper: Metadata gevonden via ID [$fieldId] voor $fieldName", $field['label']);
                }
            }

            // Stap 2b: Geen ID? Probeer de technische naam (voor APIv4 namen zoals Curriculum.CV_Deel)
            if (!$field) {
                // Pak alles NA de laatste punt. Als er geen punt is, pak de hele naam.
                $pureName = str_contains($fieldName, '.') ? substr(strrchr($fieldName, "."), 1) : $fieldName;
                
                $field = civicrm_api4('CustomField', 'get', [
                    'select'    => ['data_type', 'html_type', 'label'],
                    'where'     => [['name', '=', $pureName]],
                    'checkPermissions' => FALSE,
                ])->first();

                if ($field) {
                    wachthond($smart_debug, 4, "SmartHelper: Metadata gevonden via naam [$pureName] voor $fieldName", $field['label']);
                }
            }

            // Sla resultaat op in cache (of een fallback als er niets gevonden is)
            $fieldMetaCache[$fieldName] = $field ?? ['data_type' => 'String', 'html_type' => 'Text', 'label' => 'Unknown'];

        } catch (\Exception $e) {
            wachthond(1, "SmartHelper FOUT: Metadata lookup mislukt voor $fieldName", $e->getMessage());
            $fieldMetaCache[$fieldName] = ['data_type' => 'String', 'html_type' => 'Text', 'label' => 'Error'];
        }
    }

    $meta       = $fieldMetaCache[$fieldName];
    $dataType   = $meta['data_type'] ?? '';
    $htmlType   = $meta['html_type'] ?? '';

    // --- 3. FORMATTEREN OP BASIS VAN METADATA ---

    // TYPE A: DATUMS (Garandeer ISO formaat voor PHP 8, leeg blijft leeg)
    if ($dataType === 'Date' || $dataType === 'Timestamp') {
        $inputString = is_array($input) ? (string)reset($input) : (string)$input;
        if (empty(trim($inputString))) return '';
        
        $ts = strtotime($inputString);
        $output = ($ts !== false) ? date('Y-m-d H:i:s', $ts) : '';
        
        if ($inputString !== $output) {
            wachthond($smart_debug, 3, "SmartHelper: Datum geformatteerd voor $fieldName", "In: $inputString | Uit: $output");
        }
        return $output;
    }

    // TYPE B: MULTI-VALUE (CheckBox / Multi-Select - Voorkom mysqli array crashes)
    if ($htmlType === 'CheckBox' || $htmlType === 'Multi-Select') {
        // 1. Zorg voor een array, maar vlak geneste arrays direct af (flatten)
        $raw_values = is_array($input) ? $input : explode("\x01", trim((string)$input, "\x01"));
        
        $clean = [];
        foreach ($raw_values as $v) {
            // Als de waarde zelf een array is, pakken we de eerste waarde daarvan (flattening)
            if (is_array($v)) {
                $v = reset($v);
            }
            
            $v = trim((string)$v);
            
            // Voeg alleen toe als het geen rommel is (niet leeg, en niet de tekst "Array")
            if ($v !== '' && strtolower($v) !== 'array') {
                $clean[] = $v;
            }
        }
        
        // 2. Opschonen: uniek maken en sorteren
        $clean = array_unique($clean);
        sort($clean);
        
        $output = empty($clean) ? '' : "\x01" . implode("\x01", $clean) . "\x01";
        
        if (is_array($input) && $smart_debug >= 3) {
            wachthond($smart_debug, 3, "SmartHelper: Array omgezet naar Multi-select string voor $fieldName", $output);
        }
        return $output;
    } 

    // TYPE C: SINGLE-VALUE (Voorkom 'Array to string conversion' errors)
    if (is_array($input)) {
        $output = (string)reset($input);
        wachthond($smart_debug, 2, "SmartHelper WAARSCHUWING: Array ontvangen voor single-value veld $fieldName. Alleen eerste waarde gebruikt.", $output);
        return trim($output);
    }

    return trim((string)$input);
}

/**
 * HELPER: Zoekt naar een match van één of meer substrings in een tekst.
 * * FUNCTIONELE UITLEG:
 * Deze functie controleert of een woord uit de lijst ($needles) voorkomt als ONDERDEEL 
 * van de tekst. Dit is handig voor algemene detectie waar het woord overal mag staan.
 * Let op: deze functie is "gevaarlijk" voor korte woorden (bijv. 'add' matcht ook op 'modder').
 *
 * TECHNISCHE UITLEG:
 * - Gebruikt str_contains voor snelheid.
 * - Bij !case_sensitive worden zowel haystack als needle naar lowercase omgezet.
 * - Geeft de eerste gevonden needle (string) terug, of false bij geen match.
 */
function str_contains_any_reporting($haystack, $needles, $case_sensitive = false) {
    if (empty($haystack) || empty($needles)) return false;

    foreach ($needles as $needle) {
        if ($case_sensitive) {
            // Exacte match (hoofdlettergevoelig)
            if (str_contains($haystack, $needle)) return $needle;
        } else {
            // Case-insensitive: dwing alles naar kleine letters voor vergelijking
            if (str_contains(strtolower($haystack), strtolower($needle))) return $needle;
        }
    }
    return false;
}

/**
 * HELPER: Zoekt naar een EXACTE match van een heel woord in een tekst.
 * * * FUNCTIONELE UITLEG:
 * Deze functie is de "veilige" variant. Hij kijkt of het woord op zichzelf staat 
 * (word boundaries). Voorbeeld: 'lithium' wordt wel gevonden in "gebruikt lithium", 
 * maar NIET in "lithiumbatterij". 
 * * * CLEANUP:
 * De functie schoont zelf de input (haystack) op door vreemde tekens (zoals komma's, 
 * punten en CiviCRM control-characters) te vervangen door spaties. 
 * Dit garandeert dat de woordgrenzen (\b) altijd correct werken.
 *
 * * TECHNISCHE UITLEG:
 * - preg_replace: Vervangt alles wat geen letter/cijfer is door een spatie.
 * - \b: Regex woordgrens.
 * - preg_quote: Escapet de zoekterm.
 * - modifier: Regelt case-sensitivity.
 */
function str_contains_word_reporting($haystack, $words, $case_sensitive = false) {
    if (empty($haystack) || empty($words)) return false;

    // 1. CLEANUP (INTERN)
    // We vervangen alle niet-alfanumerieke tekens door spaties.
    // Hierdoor komen woorden die tegen een leesteken plakken (bv "test,test") los te staan ("test test").
    // We gebruiken /u modifier voor UTF-8 support (zodat é en ö niet verdwijnen als je dat zou willen).
    // Let op: De oorspronkelijke Regex '/[^a-z0-9 ]/i' is hier prima voor medicatie.
    $haystack_clean = preg_replace('/[^a-z0-9 ]/i', ' ', $haystack);

    // 2. BEPAAL MODIFIER
    // 'i' zorgt voor case-insensitive match in de regex.
    $modifier = $case_sensitive ? '' : 'i';

    foreach ($words as $word) {
        // 3. CHECK MET REGEX
        // \b garandeert dat we alleen hele woorden vinden in onze schone haystack.
        if (preg_match("/\b" . preg_quote($word, '/') . "\b/$modifier", $haystack_clean)) {
            return $word;
        }
    }
    return false;
}

if (!function_exists('get_valid_options')) {
    function get_valid_options($columnName, $customGroupId = NULL) {
        try {
            // Initialiseer de filters
            $where = [
                ['column_name', 'LIKE', $columnName . '%']
            ];

            // VOORKOM TYPEERROR: Voeg het filter toe als een enkele array aan de lijst
            if ($customGroupId) {
                $where[] = ['custom_group_id', '=', (int)$customGroupId];
            }

            $field = civicrm_api4('CustomField', 'get', [
                'select' => ['option_group_id'],
                'where' => $where,
            ])->first();

            if (!$field || empty($field['option_group_id'])) {
                return [];
            }

            return civicrm_api4('OptionValue', 'get', [
                'select' => ['value'],
                'where' => [['option_group_id', '=', $field['option_group_id']]],
                'limit' => 0,
            ])->column('value');

        } catch (\Exception $e) { 
            // In geval van nood (bijv. APIv4 niet beschikbaar) geven we een lege array terug
            return []; 
        }
    }
}

/**
 * Hook: civicrm_post
 * Wordt aangeroepen na elke database actie. We gebruiken dit om nieuwe contacten
 * direct te voorzien van alle benodigde custom field rijen.
 */
function mymodule_civicrm_post($op, $objectName, $objectId, &$objectRef) {

    // 1. Alleen actie ondernemen bij het AANMAKEN van een CONTACT
    if ($objectName === 'Contact' && $op === 'create') {
        
        // 2. Extra check: we doen dit alleen voor personen (Individuals)
        // Sommige installaties sturen het type mee in $objectRef
        $contactType = $objectRef->contact_type ?? NULL;
        
        // Als het type niet in de ref zit, halen we het even snel op uit de DAO
        if (!$contactType) {
            $contactType = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $objectId, 'contact_type');
        }

        if ($contactType === 'Individual') {
            // Roep onze verbeterde functie aan
            ensure_custom_rows_for_contact($objectId);
        }
    }
}

/**
 * Forceert rijen in custom groepen voor een specifiek CONTACT.
 */
function ensure_custom_rows_for_contact($contact_id) {
    
    $extdebug = 0; // 1=Error, 2=Status, 3=Config, 4=Detail check
    $newly_created_groups = [];

    if (!$contact_id || !is_numeric($contact_id)) {
        wachthond($extdebug, 1, "ensure_custom_rows: AFGEBROKEN - Ongeldig Contact ID", $contact_id);
        return $newly_created_groups;
    }

    static $group_configs = NULL;
    if ($group_configs === NULL) {
        $dao = CRM_Core_DAO::executeQuery("
            SELECT id, name, title, table_name FROM civicrm_custom_group 
            WHERE extends = 'Individual' AND is_active = 1 AND is_multiple = 0
        ");
        $group_configs = [];
        while ($dao->fetch()) {
            $group_configs[] = ['id' => $dao->id, 'name' => $dao->name, 'title' => $dao->title, 'table_name' => $dao->table_name];
        }
        wachthond($extdebug, 3, "ensure_custom_rows: " . count($group_configs) . " contact-tabeldefinities geladen.");
    }

    foreach ($group_configs as $group) {
        $tableName = $group['table_name'];
        $params = [1 => [$contact_id, 'Integer']];
        
        try {
            $existingId = CRM_Core_DAO::singleValueQuery("SELECT id FROM $tableName WHERE entity_id = %1", $params);
            if (!$existingId) {
                wachthond($extdebug, 2, "ensure_custom_rows: Gat in " . $group['title'] . " ($tableName)");
                CRM_Core_DAO::executeQuery("INSERT INTO $tableName (entity_id) VALUES (%1)", $params);
                $newly_created_groups[] = $group['name'];
                wachthond($extdebug, 2, "ensure_custom_rows: SUCCESS - Rij ingevoegd in $tableName");
            } else {
                wachthond($extdebug, 4, "ensure_custom_rows: OK - Rij aanwezig voor CID $contact_id in $tableName");
            }
        } catch (\Exception $e) {
            wachthond($extdebug, 1, "ensure_custom_rows: DATABASE FOUT bij $tableName", $e->getMessage());
        }
    }

    if (count($newly_created_groups) > 0) {
        wachthond($extdebug, 2, "ensure_custom_rows: FINISH - " . count($newly_created_groups) . " rijen geforceerd voor CID $contact_id.");
    } else {
        wachthond($extdebug, 2, "ensure_custom_rows: Alles reeds aanwezig voor CID $contact_id.");
    }
    return $newly_created_groups;
}

/**
 * Forceert rijen in custom groepen voor een specifieke PARTICIPANT (deelnemer).
 */
function ensure_custom_rows_for_participant($participant_id) {

    $extdebug = 0; 
    $newly_created_groups = [];

    if (!$participant_id || !is_numeric($participant_id)) {
        wachthond($extdebug, 1, "ensure_participant_rows: AFGEBROKEN - Ongeldig Participant ID", $participant_id);
        return $newly_created_groups;
    }

    static $part_group_configs = NULL;
    if ($part_group_configs === NULL) {
        $dao = CRM_Core_DAO::executeQuery("
            SELECT id, name, title, table_name FROM civicrm_custom_group 
            WHERE extends = 'Participant' AND is_active = 1 AND is_multiple = 0
        ");
        $part_group_configs = [];
        while ($dao->fetch()) {
            $part_group_configs[] = ['id' => $dao->id, 'name' => $dao->name, 'title' => $dao->title, 'table_name' => $dao->table_name];
        }
        wachthond($extdebug, 3, "ensure_participant_rows: " . count($part_group_configs) . " participant-tabeldefinities geladen.");
    }

    foreach ($part_group_configs as $group) {
        $tableName = $group['table_name'];
        $params = [1 => [$participant_id, 'Integer']];
        
        try {
            $existingId = CRM_Core_DAO::singleValueQuery("SELECT id FROM $tableName WHERE entity_id = %1", $params);
            if (!$existingId) {
                wachthond($extdebug, 2, "ensure_participant_rows: Gat in " . $group['title'] . " ($tableName)");
                CRM_Core_DAO::executeQuery("INSERT INTO $tableName (entity_id) VALUES (%1)", $params);
                $newly_created_groups[] = $group['name'];
                wachthond($extdebug, 2, "ensure_participant_rows: SUCCESS - Participant-rij ingevoegd in $tableName");
            } else {
                wachthond($extdebug, 4, "ensure_participant_rows: OK - Rij aanwezig voor deelnemer $participant_id in $tableName");
            }
        } catch (\Exception $e) {
            wachthond($extdebug, 1, "ensure_participant_rows: DATABASE FOUT bij $tableName", $e->getMessage());
        }
    }

    if (count($newly_created_groups) > 0) {
        wachthond($extdebug, 2, "ensure_participant_rows: FINISH - " . count($newly_created_groups) . " rijen geforceerd voor PID $participant_id.");
    } else {
        wachthond($extdebug, 2, "ensure_participant_rows: Alles reeds aanwezig voor PID $participant_id.");
    }
    return $newly_created_groups;
}

/**
 * Forceert rijen in custom groepen voor een specifieke CONTRIBUTION (betaling).
 */
function ensure_custom_rows_for_contribution($contribution_id) {

    $extdebug = 0; 
    $newly_created_groups = [];

    if (!$contribution_id || !is_numeric($contribution_id)) {
        wachthond($extdebug, 1, "ensure_contribution_rows: AFGEBROKEN - Ongeldig Contribution ID", $contribution_id);
        return $newly_created_groups;
    }

    static $contr_group_configs = NULL;
    if ($contr_group_configs === NULL) {
        $dao = CRM_Core_DAO::executeQuery("
            SELECT id, name, title, table_name FROM civicrm_custom_group 
            WHERE extends = 'Contribution' AND is_active = 1 AND is_multiple = 0
        ");
        $contr_group_configs = [];
        while ($dao->fetch()) {
            $contr_group_configs[] = ['id' => $dao->id, 'name' => $dao->name, 'title' => $dao->title, 'table_name' => $dao->table_name];
        }
        wachthond($extdebug, 3, "ensure_contribution_rows: " . count($contr_group_configs) . " contribution-tabeldefinities geladen.");
    }

    foreach ($contr_group_configs as $group) {
        $tableName = $group['table_name'];
        $params = [1 => [$contribution_id, 'Integer']];
        
        try {
            $existingId = CRM_Core_DAO::singleValueQuery("SELECT id FROM $tableName WHERE entity_id = %1", $params);
            if (!$existingId) {
                wachthond($extdebug, 2, "ensure_contribution_rows: Gat in " . $group['title'] . " ($tableName)");
                CRM_Core_DAO::executeQuery("INSERT INTO $tableName (entity_id) VALUES (%1)", $params);
                $newly_created_groups[] = $group['name'];
                wachthond($extdebug, 2, "ensure_contribution_rows: SUCCESS - Contribution-rij ingevoegd in $tableName");
            } else {
                wachthond($extdebug, 4, "ensure_contribution_rows: OK - Rij aanwezig voor betaling $contribution_id in $tableName");
            }
        } catch (\Exception $e) {
            wachthond($extdebug, 1, "ensure_contribution_rows: DATABASE FOUT bij $tableName", $e->getMessage());
        }
    }

    if (count($newly_created_groups) > 0) {
        wachthond($extdebug, 2, "ensure_contribution_rows: FINISH - " . count($newly_created_groups) . " rijen geforceerd voor TRID $contribution_id.");
    } else {
        wachthond($extdebug, 2, "ensure_contribution_rows: Alles reeds aanwezig voor TRID $contribution_id.");
    }
    return $newly_created_groups;
}

/**
 * Forceert rijen in custom groepen voor een specifiek MEMBERSHIP (lidmaatschap).
 */
function ensure_custom_rows_for_membership($membership_id) {

    $extdebug = 0; // 1=Error, 2=Status, 3=Config, 4=Detail check
    $newly_created_groups = [];

    // STAP 1: Basis validatie
    if (!$membership_id || !is_numeric($membership_id)) {
        wachthond($extdebug, 1, "ensure_membership_rows: AFGEBROKEN - Ongeldig Membership ID", $membership_id);
        return $newly_created_groups;
    }

    // STAP 2: Haal Membership-specifieke custom groepen op uit metadata
    static $memb_group_configs = NULL;
    if ($memb_group_configs === NULL) {
        $dao = CRM_Core_DAO::executeQuery("
            SELECT id, name, title, table_name FROM civicrm_custom_group 
            WHERE extends = 'Membership' AND is_active = 1 AND is_multiple = 0
        ");
        $memb_group_configs = [];
        while ($dao->fetch()) {
            $memb_group_configs[] = ['id' => $dao->id, 'name' => $dao->name, 'title' => $dao->title, 'table_name' => $dao->table_name];
        }
        wachthond($extdebug, 3, "ensure_membership_rows: " . count($memb_group_configs) . " membership-tabeldefinities geladen.");
    }

    // STAP 3: Loop door de membership tabellen
    foreach ($memb_group_configs as $group) {
        $tableName = $group['table_name'];
        $params = [1 => [$membership_id, 'Integer']];
        
        try {
            // STAP 4: Directe SQL check
            $existingId = CRM_Core_DAO::singleValueQuery("SELECT id FROM $tableName WHERE entity_id = %1", $params);
            
            if (!$existingId) {
                // STAP 5: Fysieke INSERT
                wachthond($extdebug, 2, "ensure_membership_rows: Gat in " . $group['title'] . " ($tableName)");
                CRM_Core_DAO::executeQuery("INSERT INTO $tableName (entity_id) VALUES (%1)", $params);
                $newly_created_groups[] = $group['name'];
                wachthond($extdebug, 2, "ensure_membership_rows: SUCCESS - Membership-rij ingevoegd in $tableName");
            } else {
                wachthond($extdebug, 4, "ensure_membership_rows: OK - Rij aanwezig voor lidmaatschap $membership_id in $tableName");
            }
        } catch (\Exception $e) {
            wachthond($extdebug, 1, "ensure_membership_rows: DATABASE FOUT bij $tableName", $e->getMessage());
        }
    }

    // STAP 6: Eindrapportage
    if (count($newly_created_groups) > 0) {
        wachthond($extdebug, 2, "ensure_membership_rows: FINISH - " . count($newly_created_groups) . " rijen geforceerd voor MID $membership_id.");
    } else {
        wachthond($extdebug, 2, "ensure_membership_rows: Alles reeds aanwezig voor MID $membership_id.");
    }
    return $newly_created_groups;
}

/*
    $results = civicrm_api4('Note', 'create', [
        'values' => [
            'entity_table' => 'civicrm_contact',            
            'entity_id' => 27,
            'contact_id' => 1,              
            'note' => 'DIT IS EEN TEST',
            'subject' => 'testonderwerp',
            'note_date' => '2024-05-01',
            'privacy' => 2,
        ],
        'checkPermissions' => TRUE,
    ]);
*/

/*
function base_civicrm_alterMailParams(&$params, $context) {
    $masterTemplateId = 533;

    if (empty($params['contactId']) || !isset($params['html'])) {
        return;
    }

    try {
        // 1. Haal de RUWE Master op
        $master = civicrm_api3('MessageTemplate', 'getsingle', [
            'id' => $masterTemplateId,
            'return' => ['msg_html'],
        ]);

        if (!empty($master['msg_html'])) {
            // 2. Voeg de onderdelen samen (Ruw)
            $fullHtml = $master['msg_html'] . $params['html'];

            // 3. FORCEER Smarty verwerking
            // We gebruiken de CiviCRM Smarty instantie om de samengevoegde tekst te parsen
            $smarty = CRM_Core_Smarty::singleton();
            
            // Maak de contactId beschikbaar voor de Smarty logica in de master
            $smarty->assign('contactID', $params['contactId']); 
            
            // Render de volledige HTML inclusief de zojuist geplakte Master-logica
            $params['html'] = $smarty->fetch("string:$fullHtml");
            
            // Optioneel: de banner toevoegen voor visueel bewijs NA het renderen
            $params['html'] = '<div style="background:green; color:white; padding:5px; text-align:center;">Master 533 Geparsed via Smarty Hook</div>' . $params['html'];
        }
    } catch (Exception $e) {
        CRM_Core_Error::debug_log_message("Fout in Master 533 Hook: " . $e->getMessage());
    }
}
*/