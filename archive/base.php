<?php

require_once 'base.civix.php';

use CRM_Base_ExtensionUtil as E;

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

function wachthond (int $extdebug, int $severity, string $message, $arrayvalue = null) {

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

function find_fiscalyear() {

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    $cache_fiscalyear_start = Civi::cache()->get('cache_today_fiscalyear_start');
    wachthond($extdebug,3, 'cache_fiscalyear_start',    $cache_fiscalyear_start);  

    if (!$cache_fiscalyear_start) {

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### BEPAAL BASISWAARDEN DATA (OA. FISCAL YEAR)",                  "[START]");
        wachthond($extdebug,1, "########################################################################");   

        $today_datetime         = date("Y-m-d H:i:s");
//      $today_datetime_past    = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($today_datetime)) );
        $today_datetime_next    = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($today_datetime)) );

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### BEPAAL DE GRENZEN VAN HET HUIDIGE FISCALE JAAR",  "[$today_datetime]");
        wachthond($extdebug,1, "########################################################################");   

        $today_fiscalyear       = curriculum_civicrm_fiscalyear($today_datetime);
        $today_fiscalyear_start = $today_fiscalyear['fiscalyear_start'] ?? NULL;
        $today_fiscalyear_einde = $today_fiscalyear['fiscalyear_einde'] ?? NULL;
        $today_kampjaar         = date('Y', strtotime('+6 month', strtotime($today_fiscalyear_start)) ); // M61: obv halverwege het jaar

        wachthond($extdebug,2, 'today_kampjaar',            $today_kampjaar); 
        wachthond($extdebug,2, 'today_fiscalyear_start',    $today_fiscalyear_start);
        wachthond($extdebug,2, 'today_fiscalyear_einde',    $today_fiscalyear_einde);

        ##########################################################################################
        # WRITE FISCAL YEAR START VALUE TO CACHE
        ##########################################################################################
        Civi::cache()->set('cache_today_fiscalyear_start',  $today_fiscalyear_start);
        Civi::cache()->set('cache_today_fiscalyear_einde',  $today_fiscalyear_einde);
        Civi::cache()->set('cache_today_kampjaar',          $today_kampjaar);

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### BEPAAL DE GRENZEN VAN HET VOLGENDE FISCALE JAAR",  "[$today_datetime_next]");
        wachthond($extdebug,1, "########################################################################");   

        $nexty_fiscalyear       = curriculum_civicrm_fiscalyear($today_datetime_next);
        $nexty_fiscalyear_start = $nexty_fiscalyear['fiscalyear_start'] ?? NULL;
        $nexty_fiscalyear_einde = $nexty_fiscalyear['fiscalyear_einde'] ?? NULL;
        $nexty_kampjaar         = date('Y', strtotime('+6 month', strtotime($nexty_fiscalyear_start)) ); // M61: obv halverwege het jaar

        wachthond($extdebug,2, 'nexty_kampjaar',            $nexty_kampjaar); 
        wachthond($extdebug,2, 'nexty_fiscalyear_start',    $nexty_fiscalyear_start);
        wachthond($extdebug,2, 'nexty_fiscalyear_einde',    $nexty_fiscalyear_einde);

        ##########################################################################################
        # WRITE FISCAL YEAR START VALUE TO CACHE
        ##########################################################################################
        Civi::cache()->set('cache_nexty_fiscalyear_start',  $nexty_fiscalyear_start);
        Civi::cache()->set('cache_nexty_fiscalyear_einde',  $nexty_fiscalyear_einde);
        Civi::cache()->set('cache_nexty_kampjaar',          $nexty_kampjaar);

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### BEPAAL DE GRENZEN VAN VOG & REF NOG GOED",        "[$today_datetime]");
        wachthond($extdebug,1, "########################################################################");   

        // zet grens 'NOG GOED OP 1 NOVEMBER - reken te late REF's & VOG's nog bij dat jaar
        $grensvognoggoed1     = $today_fiscalyear_start;
        $grensvognoggoed3     = date("Y-11-01", strtotime("-3 year")); // VOG noggoed indien binnen previous 2 fiscal years
        $grensrefnoggoed3     = date("Y-11-01", strtotime("-3 year")); // REF noggoed indien binnen previous 2 fiscal years
        // M61: TODO hier staat hardcoded 1 november. Dit kan ook op een andere manier

        wachthond($extdebug,2, 'grensvognoggoed1',          "[NAAR CACHE: $grensvognoggoed1]");
        wachthond($extdebug,2, 'grensvognoggoed3',          "[NAAR CACHE: $grensvognoggoed3]");
        wachthond($extdebug,2, 'grensrefnoggoed3',          "[NAAR CACHE: $grensrefnoggoed3]");

        ##########################################################################################
        # WRITE FISCAL YEAR START VALUE TO CACHE
        ##########################################################################################
        Civi::cache()->set('cache_grensvognoggoed1',        $grensvognoggoed1);
        Civi::cache()->set('cache_grensvognoggoed3',        $grensvognoggoed3);
        Civi::cache()->set('cache_grensrefnoggoed3',        $grensrefnoggoed3);
        ##########################################################################################

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### BEPAAL BASISWAARDEN DATA (OA. FISCAL YEAR)",                  "[EINDE]");
        wachthond($extdebug,1, "########################################################################");   

    }

}

function find_lastnext($referencedate = NULL) {

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    $today_datetime         = date("Y-m-d H:i:s");
//  $today_datetime_past    = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($today_datetime)) );
//  $today_datetime_next    = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($today_datetime)) );

    if ($referencedate == NULL) {
        $referencedate = $today_datetime;
        wachthond($extdebug,2, 'referencedate [DEFAULT TO TODAY]',  "$referencedate");
    } else {
        $referencedate = $referencedate;
        wachthond($extdebug,2, 'referencedate [INPUTDATE GIVEN]',   "$referencedate");
    }
    $referencedate_past     = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($referencedate)) );
    $referencedate_next     = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($referencedate)) );

    $cache_next_event_start_date = Civi::cache()->get('cache_next_event_start_date');
    wachthond($extdebug,3, 'cache_next_event_start_date',    $cache_next_event_start_date);  

    if (!$cache_next_event_start_date) {

        ###########################################################################################
        ### LAST EVENT BEFORE TODAY
        ###########################################################################################
        # DIT STAAT NU EVEN RANDOM HIER OMDAT HET NA RETREIVE EVENT_ID MOET EN VOOR LEEFTIJD DEEL

        if ($ditevent_event_type_id) {
            $current_event_type = $ditevent_event_type_id;  // M61: choose current event_type if available
        } else {
            $current_event_type = 1;                        // M61: default to 1 (leiding) if not
        }

        wachthond($extdebug,4, 'today_datetime',        $today_datetime);
//      wachthond($extdebug,4, 'today_datetime_past',   $today_datetime_past);
//      wachthond($extdebug,4, 'today_datetime_next',   $today_datetime_next);

        if ($current_event_type) {

            $params_lastkampfromtoday = [
                'checkPermissions' => FALSE,
                'debug'     => $apidebug,
                'limit'     => 1,
                    'select'    => [
                        'title', 
                        'start_date',
                        'end_date',
                    ],
                'where' => [
                    ['start_date',      '>', $referencedate_past],
                    ['start_date',      '<', $referencedate],
                    ['event_type_id',   '=', $current_event_type],
                ],
            ];

//          wachthond($extdebug,7, 'params_lastkampfromtoday',          $params_lastkampfromtoday);
            $result_lastkampfromtoday = civicrm_api4('Event', 'get',    $params_lastkampfromtoday);
//          wachthond($extdebug,9, 'result_lastkampfromtoday',          $result_lastkampfromtoday);

//          if ($result_lastkampfromtoday->countMatched() > 0) {
                $last_event_start_date  = $result_lastkampfromtoday[0]['start_date']    ?? NULL;
                $last_event_einde_date  = $result_lastkampfromtoday[0]['end_date']      ?? NULL;
                $last_event_einde_year  = date('Y', strtotime($last_event_einde_date ?? ''));
//          }
            wachthond($extdebug,3, 'last_event_start_date',     $last_event_start_date);
            wachthond($extdebug,3, 'last_event_einde_date',     $last_event_einde_date);
            wachthond($extdebug,3, 'last_event_einde_year',     $last_event_einde_year);
        }

        ###########################################################################################
        ### NEXT EVENT FROM TODAY
        ###########################################################################################

        // ALS DEFAULT CALCULATE NEXT AUGUST 1ST ALS NIEUWE KAMPDATUM
        $d = new DateTime(date('Y').'-08-01 16:00:00');
        if ($d < new DateTime()) {
            $d = new DateTime((date('Y')+1).'-08-01 16:00:00');
        }
        $next_event_start_date  = $d->format('Y-m-d H:i:s');
        $next_event_start_year  = date('Y', strtotime($next_event_start_date));

        // DOE NU EEN QUERY OM EEN ECHTE VOLGENDE KAMPDATUM TE ACHTERHALEN

        if ($current_event_type) {

            $params_nextkampfromtoday = [
                'checkPermissions' => FALSE,
                'debug'     => $apidebug,
                'limit'     => 1,
                    'select'    => [
                        'row_count',
                        'title', 
                        'start_date',
                        'end_date',
                    ],
                    'where' => [
                        ['start_date',      '>', $referencedate],
                        ['start_date',      '<', $referencedate_next],
                        ['event_type_id',   '=', $current_event_type], // M61: zoek next event van zelfde event_type_id (kan evt missen)
                ],
            ];

//          wachthond($extdebug,7, 'params_nextkampfromtoday',          $params_nextkampfromtoday);
            $result_nextkampfromtoday = civicrm_api4('Event', 'get',    $params_nextkampfromtoday);
//          wachthond($extdebug,9, 'result_nextkampfromtoday',          $result_nextkampfromtoday);

//          if ($result_nextkampfromtoday->countMatched() > 0) {
                $next_event_start_date  = $result_nextkampfromtoday[0]['start_date'] ?? NULL;
                $next_event_start_year  = date('Y', strtotime($next_event_start_date ?? ''));
                $next_event_einde_date  = $result_nextkampfromtoday[0]['end_date'] ?? NULL;
//          }
            wachthond($extdebug,3, 'next_event_start_date',     $next_event_start_date);
            wachthond($extdebug,3, 'next_event_start_date',     $next_event_start_year);            
            wachthond($extdebug,3, 'next_event_einde_date',     $next_event_einde_date);
        }

        $refdate_lastnext_array = array(
            'reference_date'  => $referencedate,
            'last_start_date' => $last_event_start_date,
            'last_einde_date' => $last_event_einde_date,
            'last_einde_year' => $last_event_einde_year,
            'next_start_date' => $next_event_start_date,
            'next_einde_date' => $next_event_einde_date,
            'next_start_year' => $next_event_start_year,
        );

        return $refdate_lastnext_array;

        wachthond($extdebug,2, "########################################################################");
        wachthond($extdebug,2, 'last_event_einde_date',         "[NAAR CACHE: $last_event_einde_date]");
        wachthond($extdebug,2, 'last_event_einde_year',         "[NAAR CACHE: $last_event_einde_year]");
        wachthond($extdebug,2, 'next_event_start_date',         "[NAAR CACHE: $next_event_start_date]");
        wachthond($extdebug,2, 'next_event_start_year',         "[NAAR CACHE: $next_event_start_year]");
        wachthond($extdebug,3, "########################################################################");

        ##########################################################################################
        # WRITE LAST & NEXT EVENT DATES TO CACHE
        ##########################################################################################
        Civi::cache()->set('cache_last_event_einde_date',        $last_event_einde_date);
        Civi::cache()->set('cache_last_event_einde_year',        $last_event_einde_year);
        Civi::cache()->set('cache_next_event_start_date',        $next_event_start_date);
        Civi::cache()->set('cache_next_event_start_year',        $next_event_start_year);
        ##########################################################################################

    } else {
        wachthond($extdebug,2, "########################################################################");         
        wachthond($extdebug,1, '[FROM CACHE] check_next_event_start_date',   $check_next_event_start_date);
        wachthond($extdebug,2, "########################################################################");         
    }
}

function find_lastnext_part($contactid, $referencedate = NULL) {

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    $contact_id             = $contactid;

    $eventtypesdeel         = array(11,12,13,14,21,22,23,24,33);    //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelkkjk     = array(11,12,13,14,21,22,23,24);       //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)       
    $eventtypesdeeltop      = array(33);                            //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelandtop   = array_merge($eventtypesdeel, $eventtypesdeeltop);

    $eventtypesleid         = array(1);                             //  EVENT_TYPE_ID VAN HET LEIDING EVENT VAN DIT JAAR    (- TEST_LEID)
    $eventtypesmeet         = array(2);                             //  EVENT_TYPE_ID VAN HET KAMPSTAF EVENT VAN DIT JAAR   (- KAMPSTAF)

    $eventtypesdeeltest     = array(102);
    $eventtypesleidtest     = array(101);
    $eventtypesdeeltoptest  = array(103);

    $eventtypesprod         = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesleid);
    $eventtypestest         = array_merge($eventtypesdeeltest,  $eventtypesdeeltoptest, $eventtypesleidtest);
    $eventtypesall          = array_merge($eventtypesprod,      $eventtypestest);

    $eventtypesdeelall      = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesdeeltest,    $eventtypesdeeltoptest);
    $eventtypesleidall      = array_merge($eventtypesleid,      $eventtypesleidtest,    $eventtypesmeet);

    $today_datetime         = date("Y-m-d H:i:s");
//  $today_datetime_past    = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($today_datetime)) );
//  $today_datetime_next    = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($today_datetime)) );

    if ($referencedate == NULL) {
        $referencedate = $today_datetime;
        wachthond($extdebug,2, 'referencedate', "$referencedate [DEFAULT TO TODAY]");
    } else {
        $referencedate = $referencedate;
        wachthond($extdebug,2, 'referencedate', "$referencedate [INPUTDATE GIVEN]");
    }
    $referencedate_past     = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($referencedate)) );
    $referencedate_next     = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($referencedate)) );

    ###########################################################################################
    ### LAST EVENT BEFORE TODAY
    ###########################################################################################
    # DIT STAAT NU EVEN RANDOM HIER OMDAT HET NA RETREIVE EVENT_ID MOET EN VOOR LEEFTIJD DEEL

    if ($ditevent_event_type_id) {
        $current_event_type = $ditevent_event_type_id;  // M61: choose current event_type if available
    } else {
        $current_event_type = 1;                        // M61: default to 1 (leiding) if not
    }

    wachthond($extdebug,4, 'today_datetime',        $today_datetime);
//  wachthond($extdebug,4, 'today_datetime_past',   $today_datetime_past);
//  wachthond($extdebug,4, 'today_datetime_next',   $today_datetime_next);

    $params_lastkampfromtoday = [
        'select' => [
            'contact_id.display_name', 'event_id.end_date', 'PART.PART_kampeinde'
        ],
        'where' => [
            ['event_id.start_date',      '>', $referencedate_past],
            ['event_id.start_date',      '<', $referencedate],
            ['event_id.event_type_id',  'IN', $eventtypesall],
//          ['status_id',               'IN', $status_positive],        // deze waarde staat nog niet in deze functie
            ['is_test',                 'IN', [TRUE, FALSE]], 
            ['contact_id',              '=',  $contact_id],
        ],
        'checkPermissions' => FALSE,
        'debug' => $apidebug,
    ];
//  wachthond($extdebug,7, 'params_lastkampfromtoday',              $params_lastkampfromtoday);
    $result_lastkampfromtoday = civicrm_api4('Participant', 'get',  $params_lastkampfromtoday);
    wachthond($extdebug,3, 'result_lastkampfromtoday',              $result_lastkampfromtoday);

//  if ($result_lastkampfromtoday->countMatched() > 0) {
//      $last_event_einde_date  = $result_lastkampfromtoday[0]['event_id.end_date'] ?? NULL;
        $last_event_einde_date  = $result_lastkampfromtoday[0]['PART.PART_kampeinde'] ?? NULL;
        $last_event_einde_year  = date('Y', strtotime($last_event_einde_date));
//  }
    wachthond($extdebug,3, 'last_event_einde_date',     $last_event_einde_date);
    wachthond($extdebug,3, 'last_event_einde_year',     $last_event_einde_year);

    ###########################################################################################
    ### NEXT EVENT FROM TODAY
    ###########################################################################################

    // ALS DEFAULT CALCULATE NEXT AUGUST 1ST ALS NIEUWE KAMPDATUM
    $d = new DateTime(date('Y').'-08-01 16:00:00');
    if ($d < new DateTime()) {
        $d = new DateTime((date('Y')+1).'-08-01 16:00:00');
    }
    $next_event_start_date  = $d->format('Y-m-d H:i:s');
    $next_event_start_year  = date('Y', strtotime($next_event_start_date));

    // DOE NU EEN QUERY OM EEN ECHTE VOLGENDE KAMPDATUM TE ACHTERHALEN

    $params_nextkampfromtoday = [
        'select' => [
            'contact_id.display_name', 'event_id.start_date', 'PART.PART_kampstart'
        ],
        'where' => [
            ['event_id.start_date',      '>', $referencedate],
            ['event_id.start_date',      '<', $referencedate_next],
            ['event_id.event_type_id',  'IN', $eventtypesall],
//          ['status_id',               'IN', $status_positive],
            ['is_test',                 'IN', [TRUE, FALSE]], 
            ['contact_id',              '=',  $contact_id],
        ],
        'checkPermissions' => FALSE,
        'debug' => $apidebug,
    ];

//  wachthond($extdebug,7, 'params_nextkampfromtoday',              $params_nextkampfromtoday);
    $result_nextkampfromtoday = civicrm_api4('Participant', 'get',  $params_nextkampfromtoday);
    wachthond($extdebug,9, 'result_nextkampfromtoday',              $result_nextkampfromtoday);

//  if ($result_nextkampfromtoday->countMatched() > 0) {
//      $next_event_start_date  = $result_nextkampfromtoday[0]['event_id.start_date'] ?? NULL;
        $next_event_start_date  = $result_nextkampfromtoday[0]['PART.PART_kampstart'] ?? NULL;        
        $next_event_start_year  = date('Y', strtotime($next_event_start_date));
//  }
        wachthond($extdebug,3, 'next_event_start_date',     $next_event_start_date);
        wachthond($extdebug,3, 'next_event_start_date',     $next_event_start_year);            

    $refdate_lastnext_array = array(
        'reference_date'    => $referencedate,
        'last_einde_date'   => $last_event_einde_date,
        'last_einde_year'   => $last_event_einde_year,
        'next_start_date'   => $next_event_start_date,
        'next_start_year'   => $next_event_start_year,
    );

    return $refdate_lastnext_array;

    wachthond($extdebug,2, "########################################################################");
    wachthond($extdebug,2, 'last_event_einde_date',         "[NAAR CACHE: $last_event_einde_date]");
    wachthond($extdebug,2, 'last_event_einde_year',         "[NAAR CACHE: $last_event_einde_year]");
    wachthond($extdebug,2, 'next_event_start_date',         "[NAAR CACHE: $next_event_start_date]");
    wachthond($extdebug,2, 'next_event_start_year',         "[NAAR CACHE: $next_event_start_year]");
    wachthond($extdebug,3, "########################################################################");
}

function find_partstatus() {

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    $cache_status_positive = Civi::cache()->get('cache_status_positive');

    if (!$cache_status_positive) {

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,1, "### VIND GECONFIGUREERDE DEELNAME STATUSSEN (POS/NEG)","[$today_datetime]");
        wachthond($extdebug,3, "########################################################################");

        $partstatus_positive = civicrm_api4('ParticipantStatusType', 'get', [
            'checkPermissions' => FALSE,
            'select' => [
                'id', 'name', 'label',
            ],
            'where' => [
                ['class', '=', 'Positive'],
            ],
        ]);

        $partstatus_pending = civicrm_api4('ParticipantStatusType', 'get', [
            'checkPermissions' => FALSE,
            'select' => [
                'id',  'name', 'label',
            ],
            'where' => [
                ['class', '=', 'Pending'],
            ],
        ]);

        $partstatus_waiting = civicrm_api4('ParticipantStatusType', 'get', [
            'checkPermissions' => FALSE,
            'select' => [
                'id',  'name', 'label',
            ],
            'where' => [
                ['class', '=', 'Waiting'],
            ],
        ]);

        $partstatus_negative = civicrm_api4('ParticipantStatusType', 'get', [
            'checkPermissions' => FALSE,
            'select' => [
                'id',  'name', 'label',
            ],
            'where' => [
                ['class', '=', 'Negative'],
            ],
        ]);

        $status_positive        = $partstatus_positive->column('id');       // maakt een array met alleen de velden voor id
        $status_pending         = $partstatus_pending->column('id');        // maakt een array met alleen de velden voor id
        $status_waiting         = $partstatus_waiting->column('id');        // maakt een array met alleen de velden voor id
        $status_negative        = $partstatus_negative->column('id');       // maakt een array met alleen de velden voor id

        $status_name_positive   = $partstatus_positive->column('label');    // maakt een array met alleen de velden voor label
        $status_name_pending    = $partstatus_pending->column('label');     // maakt een array met alleen de velden voor label
        $status_name_waiting    = $partstatus_waiting->column('label');     // maakt een array met alleen de velden voor label
        $status_name_negative   = $partstatus_negative->column('label');    // maakt een array met alleen de velden voor label

        wachthond($extdebug,4, 'statusids_positive',    $status_positive);
        wachthond($extdebug,4, 'statusids_pending',     $status_pending);
        wachthond($extdebug,4, 'statusids_waiting',     $status_waiting);
        wachthond($extdebug,4, 'statusids_negative',    $status_negative);

        wachthond($extdebug,4, 'status_name_positive',  $status_name_positive);
        wachthond($extdebug,4, 'status_name_pending',   $status_name_pending);
        wachthond($extdebug,4, 'status_name_waiting',   $status_name_waiting);
        wachthond($extdebug,4, 'status_name_negative',  $status_name_negative);

        ##########################################################################################
        # WRITE FISCAL YEAR START VALUE TO CACHE
        ##########################################################################################
        Civi::cache()->set('cache_status_positive',     $status_positive);
        Civi::cache()->set('cache_status_pending',      $status_pending);
        Civi::cache()->set('cache_status_waiting',      $status_waiting);
        Civi::cache()->set('cache_status_negative',     $status_negative);
    }
}

function find_eventids() {

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    $today_datetime         = date("Y-m-d H:i:s");

    $eventtypesdeel         = array(11,12,13,14,21,22,23,24,33);    //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelkkjk     = array(11,12,13,14,21,22,23,24);       //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)       
    $eventtypesdeeltop      = array(33);                            //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelandtop   = array_merge($eventtypesdeel, $eventtypesdeeltop);

    $eventtypesleid         = array(1);                             //  EVENT_TYPE_ID VAN HET LEIDING EVENT VAN DIT JAAR    (- TEST_LEID)
    $eventtypesmeet         = array(2);                             //  EVENT_TYPE_ID VAN HET KAMPSTAF EVENT VAN DIT JAAR   (- KAMPSTAF)
    $eventtypestoer         = array(3);                             //  EVENT_TYPE_ID VAN DE TRAININGSDAG VAN DIT JAAR      (- TRAININGSDAG)         

    $eventtypesdeeltest     = array(102);
    $eventtypesleidtest     = array(101);
    $eventtypesdeeltoptest  = array(103);

    $eventtypesprod         = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesleid);
    $eventtypestest         = array_merge($eventtypesdeeltest,  $eventtypesdeeltoptest, $eventtypesleidtest);
    $eventtypesall          = array_merge($eventtypesprod,      $eventtypestest);

    $eventtypesdeelall      = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesdeeltest,    $eventtypesdeeltoptest);
    $eventtypesleidall      = array_merge($eventtypesleid,      $eventtypesleidtest,    $eventtypesmeet,        $eventtypestoer);

    $cache_kampids_deel     = Civi::cache()->get('cache_kampids_deel');

    if (!$cache_kampids_deel) {

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,1, "### VIND ALLE EVENT LEIDING & DEELNEMERS VOOR DIT JAAR", "[$today_datetime]");
        wachthond($extdebug,3, "########################################################################");

        $params_event_deel = [
            'checkPermissions' => FALSE,
            'debug'     => $apidebug,
            'select'    => [
                'row_count', 'id', 'event_type_id', 'title',
            ],
            'where'     => [
                ['event_type_id', 'IN', $eventtypesdeel],
//              ['title', 'NOT LIKE',   '%TEST%'],
                ['start_date', '=',     'this.fiscal_year'],
            ],
        ];

        wachthond($extdebug,7, 'params_event_deel',         $params_event_deel);
        $result_event_deel = civicrm_api4('Event', 'get',   $params_event_deel);
        wachthond($extdebug,9, 'result_event_deel',         $result_event_deel);

        $kampids_deelcount  = $result_event_deel->count();
        $kampids_deelcount1 = $result_event_deel->countFetched();
        $kampids_deelcount2 = $result_event_deel->countMatched();

        wachthond($extdebug,4, 'kampids_deelcount 0',       $kampids_deelcount);
        wachthond($extdebug,4, 'kampids_deelcount 1',       $kampids_deelcount1);
        wachthond($extdebug,4, 'kampids_deelcount 2',       $kampids_deelcount2);

        $kampids_deel       = $result_event_deel->column('id');  // maakt een array met alleen de velden voor id
        ksort($kampids_deel);
        wachthond($extdebug,3, 'kampids_deel',                  $kampids_deel);

        $params_event_deeltop = [
            'checkPermissions' => FALSE,
            'debug'     => $apidebug,
            'select'    => [
                'id', 'event_type_id', 'title', 'row_count',
            ],
            'where'     => [
                ['event_type_id', 'IN', $eventtypesdeeltop],
//              ['title', 'NOT LIKE',   '%TEST%'],
                ['start_date', '=',     'this.fiscal_year'],
            ],
        ];

        wachthond($extdebug,7, 'params_event_deeltop',          $params_event_deeltop);
        $result_event_deeltop = civicrm_api4('Event', 'get',    $params_event_deeltop);
        wachthond($extdebug,9, 'result_event_deeltop',          $result_event_deeltop);
        $kampids_topcount   = $result_event_deeltop->countMatched();
        $kampids_top        = $result_event_deeltop->column('id');  // maakt een array met alleen de velden voor id
        ksort($kampids_top);
        wachthond($extdebug,3, 'kampids_top',                   $kampids_top);

        $params_event_leid = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'select' => [
                'id', 'event_type_id', 'title', 'row_count',
            ],
            'where' => [
                ['event_type_id', 'IN', $eventtypesleid],
                ['title', 'NOT LIKE',   '%TEST%'],
                ['start_date', '=',     'this.fiscal_year'],
            ],
        ];

        wachthond($extdebug,7, 'params_event_leid',             $params_event_leid);
        $result_event_leid = civicrm_api4('Event', 'get',       $params_event_leid);
        wachthond($extdebug,9, 'result_event_leid',             $result_event_leid);
        $kampids_leid       = $result_event_leid->column('id'); // maakt een array met alleen de velden voor id
        ksort($kampids_leid);
        wachthond($extdebug,3, 'kampids_leid',                  $kampids_leid);

        $params_event_meet = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'select' => [
                'id', 'event_type_id', 'title', 'row_count',
            ],
            'where' => [
                ['event_type_id', 'IN', $eventtypesmeet],
                ['title', 'NOT LIKE',   '%TEST%'],
                ['start_date', '=',     'this.fiscal_year'],
                ['start_date', '>=',    $today_datetime],
            ],
        ];

        wachthond($extdebug,3, 'params_event_meet',             $params_event_meet);
        $result_event_meet = civicrm_api4('Event', 'get',       $params_event_meet);
        wachthond($extdebug,9, 'result_event_meet',             $result_event_meet);
        $kampids_meet = $result_event_meet->column('id'); // maakt een array met alleen de velden voor id
        ksort($kampids_meet);
        wachthond($extdebug,3, 'kampids_meet',                  $kampids_meet);

        $params_event_toer = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'select' => [
                'id', 'event_type_id', 'title', 'row_count',
            ],
            'where' => [
                ['event_type_id', 'IN', $eventtypestoer],
                ['title', 'NOT LIKE',   '%TEST%'],
                ['start_date', '=',     'this.fiscal_year'],
                ['start_date', '>=',    $today_datetime],
            ],
        ];

        wachthond($extdebug,3, 'params_event_toer',             $params_event_toer);
        $result_event_toer = civicrm_api4('Event', 'get',       $params_event_toer);
        wachthond($extdebug,9, 'result_event_toer',             $result_event_toer);
        $kampids_toer = $result_event_toer->column('id'); // maakt een array met alleen de velden voor id
        ksort($kampids_toer);
        wachthond($extdebug,3, 'kampids_toer',                  $kampids_toer);

        $params_event_deeltest = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'select' => [
                'id', 'event_type_id', 'title', 'row_count',
            ],
            'where' => [
                ['event_type_id', 'IN', $eventtypesdeeltest],
                ['start_date', '>=',    $today_datetime],
                ['start_date', '<',     $today_fiscalyear_einde],
            ],
        ];

        wachthond($extdebug,7, 'params_event_deeltest',         $params_event_deeltest);
        $result_event_deeltest = civicrm_api4('Event', 'get',   $params_event_deeltest);
        wachthond($extdebug,9, 'result_event_deeltest',         $result_event_deeltest);
        $kampids_deeltest = $result_event_deeltest->column('id'); // maakt een array met alleen de velden voor id
        ksort($kampids_deeltest);

        $params_event_leidtest = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'select' => [
                'id', 'event_type_id', 'title', 'row_count',
            ],
            'where' => [
                ['event_type_id', 'IN', $eventtypesleidtest],
                ['start_date', '>=',    $today_datetime],
                ['start_date', '<',     $today_fiscalyear_einde],
            ],
        ];

        wachthond($extdebug,7, 'params_event_test',             $params_event_leidtest);
        $result_event_leidtest = civicrm_api4('Event', 'get',   $params_event_leidtest);
        wachthond($extdebug,9, 'result_event_test',             $result_event_leidtest);

        $kampids_leidtest   = $result_event_leidtest->column('id'); // maakt een array met alleen de velden voor id
        ksort($kampids_leidtest);
        wachthond($extdebug,3, 'kampids_leidtest',              $kampids_leidtest);

        $kampids_deel_top   = array_merge($kampids_deel, $kampids_top);
        $kampids_deel_all   = array_merge($kampids_deel, $kampids_top,          $kampids_deeltest);
        $kampids_leid_all   = array_merge($kampids_leid, $kampids_leidtest);
        $kampids_deel_leid  = array_merge($kampids_deel, $kampids_top,          $kampids_leid);

        $kampids_all        = array_merge($kampids_deel, $kampids_top,          $kampids_leid, $kampids_deeltest, $kampids_leidtest);

        $kampids_test_all   = array_merge($kampids_deeltest, $kampids_leidtest);

        ##########################################################################################
        # WRITE EVENT ID'S TO CACHE
        ##########################################################################################
        Civi::cache()->set('cache_kampids_deel',        $kampids_deel);
        Civi::cache()->set('cache_kampids_leid',        $kampids_leid);        

        Civi::cache()->set('cache_kampids_deel_top',    $kampids_deel_top);
        Civi::cache()->set('cache_kampids_deel_all',    $kampids_deel_all);
        Civi::cache()->set('cache_kampids_leid_all',    $kampids_leid_all);
        Civi::cache()->set('cache_kampids_deel_leid',   $kampids_deel_leid);
        Civi::cache()->set('cache_kampids_all',         $kampids_all);

        Civi::cache()->set('cache_kampids_meet',        $kampids_meet);
        Civi::cache()->set('cache_kampids_toer',        $kampids_toer);

        Civi::cache()->set('cache_kampids_test_deel',   $kampids_deeltest);
        Civi::cache()->set('cache_kampids_test_leid',   $kampids_leidtest);
        Civi::cache()->set('cache_kampids_test_all',    $kampids_leid_all);
    }
}


function find_contact(string $inputtype, $input)
{
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

    return $check_contact;

  } else {

    wachthond($extdebug,1, "FIND_CONTACT FOR $inputtype: $input",   "[NO CRM CONTACT FOUND]");    
    return;
  }
}

function find_ufmatch(string $inputtype, $input)
{
  $extdebug = 0;  // 1 = basic // 2 = verbose // 3 = params / 4 = results
  $apidebug = FALSE;

  if ($inputtype == NULL) {
    wachthond($extdebug,2, 'find_ufmatch', "[NO INPUTTYPE]");
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

function diag_ufmatch(string $inputtype, $inputcid, $input_ufmatch, $input_username, $input_jobtitle, $input_extid)
{
  $extdebug = 0;  // 1 = basic // 2 = verbose // 3 = params / 4 = results
  $apidebug = FALSE;

  if ($inputtype == NULL) {
    wachthond($extdebug,2, 'find_ufmatch', "[NO INPUTTYPE]");
    return;
  }

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

  $diag_input_ufmatch     = $input_ufmatch;

  wachthond($extdebug,2, "diag_input_ufmatch",        $diag_input_ufmatch);
  if ($diag_input_ufmatch->cid > 0) {
    $diag_cid   = 1;
    wachthond($extdebug,3, "diag_input_ufmatch->cid",     $diag_input_ufmatch->cid);
  }

  // DOEL   :   DIAGNOSE UFMATCH OBV INPUT (CID)
  // CHECK 1  :   KLOPT UFMATCH DRUPAL ID?
  //        a) IS UFMATCH UID  ZELFDE ALS CONNECTED UID (VIA CRM & CMS API)
  //        b) IS UFMATCH NAME ZELFDE ALS USER_NAME (HOEFT NIET, KAN REPAIR NODIG MAKEN)
  // CHECK 2  : IS ER GEEN UFMATCH VIA CID?
  //        a) IS ER WEL EEN DRUPAL ACCOUNT VIA LOAD-BY-NAME? ZO JA, BESTAAT DEZE IN ANDERE UF_MATCH?
  //        b) IS ER WEL EEN DRUPAL ACCOUNT VIA LOAD-BY-MAIL? ZO JA, BESTAAT DEZE IN ANDERE UF_MATCH?
  //        c) BESTAAT USER_NAME IN EEN ANDERE UF_MATCH?

  wachthond($extdebug,3, "########################################################################");
  wachthond($extdebug,2, "### 1. CHECK VIA CIVICRM API IF CID: $inputcid HAS A CONNECTED DRUPAL ACCOUNT");
  wachthond($extdebug,3, "########################################################################");

  if ($diag_input_ufmatch->cid > 0) {

    $params_drupaluser = [
        'return'    => ["id","name"],
        'contact_id'  => $inputcid,
        'sequential'  => 1,
    ];
    wachthond($extdebug,7, 'params_drupaluser',     $params_drupaluser);
    $result_drupaluser = civicrm_api3('User','get', $params_drupaluser);
    wachthond($extdebug,9, 'result_drupaluser',     $result_drupaluser);

    // MATCHLOGIC
    if ($result_drupaluser) {

      $crm_drupal_account_id    = $result_drupaluser['values'][0]['id']     ?? NULL;
      $crm_drupal_account_name  = $result_drupaluser['values'][0]['name']   ?? NULL;
      $crm_drupal_account_mail  = $result_drupaluser['values'][0]['email']  ?? NULL;
      wachthond($extdebug,3, "crm_drupal_account_id",   $crm_drupal_account_id);
      wachthond($extdebug,3, "crm_drupal_account_name", $crm_drupal_account_name);
      wachthond($extdebug,3, "crm_drupal_account_mail", $crm_drupal_account_mail);

      if ($diag_input_ufmatch->ufid == $crm_drupal_account_id) {
        $diag_crm_drupalaccount = 1;
      }
    }

    if ($result_drupaluser) {

      ### CONNECTED DRUPAL NAAM ###
      if ($crm_drupal_account_name) {
        wachthond($extdebug,3, "EEN CONNECTED DRUPAL NAAM GEVONDEN",  "[VIA CID $inputcid]");
        $crm_drupal_account_found  = 1;
      } else {
        wachthond($extdebug,3, "GEEN CONNECTED DRUPAL NAAM GEVONDEN",   "[VIA CID $inputcid]");
      }

      ### WARNING ###
      if ($input_extid    AND $crm_drupal_account_id    AND $crm_drupal_account_id  != $input_extid) {
        wachthond($extdebug,1, "DRUPALID ($crm_drupal_account_id) != CRMEXTID ($input_extid)", "WARNING [DRUPALID DANGER]");
        $crm_drupal_account_danger = 1;
      }

      if ($input_username   AND $crm_drupal_account_name  AND $crm_drupal_account_name != $input_username) {
        wachthond($extdebug,1, "DRUPALNAME != CRMNAME ($input_username)", "WARNING [DRUPALNAME DANGER]");
        $crm_drupal_account_danger = 1;
      }

      ### PRIMA ###
      if (empty($input_extid) AND $crm_drupal_account_id) {
        $crm_drupal_account_danger = 0;
      }

      if ($input_extid    AND $crm_drupal_account_id    AND $crm_drupal_account_id   == $input_extid) {
        wachthond($extdebug,3, "DRUPALID == CRMEXTID", "PRIMA ($crm_drupal_account_id)");
        $crm_drupal_account_danger = 0;
      }     
      if ($input_username   AND $crm_drupal_account_name  AND $crm_drupal_account_name == $input_username) {
        wachthond($extdebug,3, "DRUPALNAME == CRMNAME", "PRIMA ($input_username)");
        $crm_drupal_account_danger = 0;
      }

    } else {
      wachthond($extdebug,1, "VIA CIVICRM API IF CID GEEN CONNECTED DRUPAL ACCOUNT GEVONDEN");      
    }
  }

  wachthond($extdebug,3, "########################################################################");
  wachthond($extdebug,2, "### 2. CHECK VIA DRUPAL API IF UFMATCH_UFID ($diag_input_ufmatch->ufid) HAS A CONNECTED DRUPAL ACCOUNT");
  wachthond($extdebug,3, "########################################################################");

  global $cms_drupal_account;
  $cms_drupal_account    = user_load($diag_input_ufmatch->ufid);

  wachthond($extdebug,3, "cms_drupal_account->uid",     $cms_drupal_account->uid);
  wachthond($extdebug,3, "cms_drupal_account->name",    $cms_drupal_account->name);
  wachthond($extdebug,3, "cms_drupal_account->mail",    $cms_drupal_account->mail);
  wachthond($extdebug,4, "cms_drupal_account",          $cms_drupal_account);   

  // MATCHLOGIC
  if ($diag_input_ufmatch->ufid == $cms_drupal_account->uid) {
    $diag_ufid = 1;
    wachthond($extdebug,3, "diag_input_ufmatch->ufid",  $diag_input_ufmatch->ufid);   
    wachthond($extdebug,2, "DIAG_UFID",         $diag_ufid);
  } else {
    wachthond($extdebug,3, "diag_input_ufmatch->ufid",  $diag_input_ufmatch->ufid);
    wachthond($extdebug,2, "DIAG_UFID",         $diag_ufid);
  }

  ################################################################################################
  // HANDLE LOGIC
  ################################################################################################

  if ($cms_drupal_account->id > 0) {
    wachthond($extdebug,2, "VIA CMSEXTID ($input_extid) DRUPAL ACCOUNT ($cms_drupal_account->id) GEVONDEN", "PRIMA!");
    $cms_drupal_account_found   = 1;
  }

  if ($cms_drupal_account->id > 0 AND $cms_drupal_account->name != $input_username) {
    wachthond($extdebug,2, "VIA CMSEXTID ($input_extid) DRUPAL ACCOUNT ($cms_drupal_account->id) GEVONDEN", "CHECK [WANT ANDERE USER_NAME]");
    $cms_drupal_account_danger  = 1;
  }

  if ($cms_drupal_account->id > 0 AND $cms_drupal_account->mail != $user_mail) {
    wachthond($extdebug,2, "VIA CMSEXTID ($input_extid) DRUPAL ACCOUNT ($cms_drupal_account->id) GEVONDEN", "CHECK [WANT ANDERE USER_MAIL]");
    $cms_drupal_account_danger  = 1;
  }

  if (empty($input_extid)) {

    wachthond($extdebug,2, "CMSEXTID IS LEEG BIJ $displayname DUS KON NIET OP BASIS HIERVAN CHECKEN");

  } else {

    wachthond($extdebug,3, "input_username (contructed)", $input_username);
    wachthond($extdebug,3, "job_title (crm drupalnaam)",  $input_jobtitle);

    if ($cms_drupal_account->name == $input_username AND $cms_drupal_account->name == $input_jobtitle) {
      wachthond($extdebug,1, "DRUPALNAME == CONSTRUCTED USERNAME == civcrm_jobtitle", "PRIMA (VIA CRMEXTID $input_extid)");

      $cms_drupal_account_found     = 1;
      $cms_drupal_account_name_safe = 1;

      $diag_name                    = 1;

    } else {
      wachthond($extdebug,1, "DRUPALNAME != CONSTRUCTED USERNAME != civcrm_jobtitle", "ERROR (VIA CRMEXTID ($input_extid)");
      wachthond($extdebug,1, "NEED 2 UPDATE JOBTITLE", "[NAAR $input_username]");

      $need2update_jobtitle       = 1;
      $safe2update_jobtitle       = 1;
      // M61 TODO : HIER NOG CHECKEN OF DEZE USERNAME UNIEK IS OF AANGEPAST MOET WORDEN

    }
/*
    wachthond($extdebug,3, "user_mail (constructed)", $user_mail);
    wachthond($extdebug,2, "cms_drupal_account->mail",  $cms_drupal_account->mail);

    if ($cms_drupal_account->mail == $user_mail) {

      wachthond($extdebug,2, "DRUPALMAIL == CIVICRM EMAIL",               "PRIMA (VIA CRMEXTID $input_extid)");
      $cms_drupal_account_mail_safe = 1;
    }
*/
  }

  wachthond($extdebug,3, "########################################################################");
  wachthond($extdebug,2, "### 3. CHECKS WANNEER UF_MATCH LEEG IS");
  wachthond($extdebug,3, "########################################################################");

  wachthond($extdebug,3, "########################################################################");
  wachthond($extdebug,2, "### 4. CHECK IF DRUPAL UID ($diag_input_ufmatch->ufid) IS PROPERLY CONNECTED");
  wachthond($extdebug,3, "########################################################################");

  wachthond($extdebug,2, "diag_input_ufmatch->uid",     $diag_input_ufmatch->ufid);
  wachthond($extdebug,2, "input_extid",                 $input_extid);

  if ($diag_input_ufmatch->ufid > 0 AND $diag_input_ufmatch->ufid == $input_extid) {
    wachthond($extdebug,2, "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid)", "PRIMA! [MATCH MET EXTID]");
    $diag_id  = 1;
  }

  if ($diag_input_ufmatch->ufid > 0 AND $diag_input_ufmatch->ufid != $input_extid) {

    wachthond($extdebug,2, "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid)", "CHECK! [GEEN EXTID MATCH]"); // MOGELIJK WEL UFMATCH

    // LOGIC: INDIEN GEVONDEN DRUPAL ACCOUNT VIA EXTID GEKOPPELD IS: LEUK!
    // LOGIC: INDIEN NIET DAN IS HET ALSNOG MOGELIJK DAT ER EEN UF_MATCH IS

    // LOGIC: INDIEN EEN GEVONDEN UFMATCH WIJST NAAR HEEL ANDERE CONTACT DAN NIET SAFE
    // LOGIC: INDIEN OOK GEEN UFMATCH DAN IS DRUPAL ACCOUNT EEN ORPHAN (EN KAN MOGELIJK VIA NIEUWE UFMATCH GEKOPPELD WORDEN)

    $diag_input_ufmatch_findcontact   = find_contact('drupalid', $diag_input_ufmatch->ufid);
    if ($diag_input_ufmatch_findcontact->id > 0) {
      wachthond($extdebug,1,    "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid) HEEFT WEL CIVICRM CONTACT $diag_input_ufmatch_findcontact->naam",
                    "CHECK! [MOGELIJK WEL UFMATCH]");

      if ($diag_input_ufmatch_findcontact->id == $input_extid) {
        wachthond($extdebug,1,  "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid) HEEFT EXTID MATCH MET DIT CONTACT",
                    "PRIMA! [WEL UPDATE EXTID NODIG VOOR [$check_did_ufmatch_findcontact->naam]");
        $need2update_extid    = 1;
      } else {
        wachthond($extdebug,1,  "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid) HEEFT EXTID MATCH MET ANDER CID",
                    "ERROR! [CONFLICT MET $check_did_ufmatch_findcontact->naam]");
        $need2repair_ufmatch  = 1;          
      }
    }

    $check_did_ufmatch          = find_ufmatch('drupalid',  $diag_input_ufmatch->ufid) ?? NULL;
    $check_did_ufmatch_found    = (is_object($check_did_ufmatch) && isset($check_did_ufmatch->id)) ? 1 : 0;
    wachthond($extdebug,4, "check_did_ufmatch_result",      $check_did_ufmatch);
    wachthond($extdebug,3, "check_did_ufmatch_found",       $check_did_ufmatch_found);

    if ($check_did_ufmatch_found == 1) {
      $check_did_ufmatch_findcontact = find_contact('contactid', $check_did_ufmatch->cid);

      if ($check_did_ufmatch->cid != $inputcid) {
        wachthond($extdebug,1,  "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid) HEEFT UFMATCH MET ANDER CID",
                    "ERROR! [$check_did_ufmatch_findcontact->naam]");
        // M61: IN DIT GEVAL MOET ER EEN ALTERNATIEVE USER_NAME AANGEMAAKT / GEBRUIKT WORDEN (KAN ZIJN DAT JUISTE USERNAME HANGT AAN EMPTY PLACEHOLDER VAN ZELFDE CONTACT)
        $valid_username         = $user_name ."_". $contact_id;
        $need2unique_username   = 1;
        $need2update_jobtitle   = 1;
        $safe2update_jobtitle   = 1;
      }
      if ($check_did_ufmatch->cid == $inputcid) {
        wachthond($extdebug,1,  "INPUT UF MATCH DRUPAL UID ($diag_input_ufmatch->ufid) HEEFT UFMATCH ($check_did_ufmatch->id) MET DIT CONTACT",
                    "HOERA! [$check_did_ufmatch_findcontact->naam]");
        // M61: IN DIT GEVAL MOET DE CRM_EXTERNALID WORDEN GEUPDATE NAAR $check_did_ufmatch->ufid OF $diag_input_ufmatch->ufid;
        $valid_username     = $input_username;
        $valid_drupalid     = $diag_input_ufmatch->ufid;
        $valid_ufmatchid    = $check_did_ufmatch->id;
        $diag_name          = 1;
        $diag_ufid          = 1;
        $need2update_extid  = 1;
        $safe2update_extid  = 1;
      }

    } else {
      wachthond($extdebug,1,    "VIA USERNAAM ($input_username) GEVONDEN DRUPAL ACCOUNT KAN GEKOPPELD WORDEN!",
                    "ORPHAN [GEEN UFMATCH / EXTID MATCH VOOR UID: $diag_input_ufmatch->ufid]");         
      // M61: IN DIT GEVAL MOET DE UF_MATCH WORDEN GEUPDATE OF AANGEMAAKT 
      $need2repair_ufmatch = 1;

      if ($diag_input_ufmatch->id > 0) {
        $need2update_ufmatch = 1;
      } else {
        $need2create_ufmatch = 1;      
      }

    }
  }

  wachthond($extdebug,3, "########################################################################");
  wachthond($extdebug,2, "### 5. BUILD DIAG OBJECT");
  wachthond($extdebug,3, "########################################################################"); 

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

    return $diag_ufmatch;

  } else {
    wachthond($extdebug,1, "DIAG_UFMATCH FOR $input_username", "[NO UFMATCH FOUND]");   
    return;
  }
}

function drupal_role_change($cmsid, $cmsrol, $cmsaction)
{
  $extdebug = 0;  // 1 = basic // 2 = verbose // 3 = params / 4 = results
  $apidebug = FALSE;

  if ($cmsid == NULL) {
    wachthond($extdebug,2, 'cmsid',   "[EMPTY]");
    return;
  }
  if ($cmsrol == NULL) {
    wachthond($extdebug,2, 'cmsrol',  "[EMPTY]");
    return;
  }

  $valid_drupalid = $cmsid;
  $drupal_role  = $cmsrol;
  $rolaction    = $cmsaction;

  wachthond($extdebug,1, "DRUPAL ROLE CHANGE ", "$rolaction role: $drupal_role");
  $existinguser       = user_load($valid_drupalid);
  $existinguser_roles_org = $existinguser->roles;
  $existinguser_roles_new = $existinguser->roles;   // SET INITIAL VALUE
  wachthond($extdebug,3, "existinguser_roles_org",  $existinguser_roles_org);
  wachthond($extdebug,3, "existinguser_roles_new",  $existinguser_roles_new);
  wachthond($extdebug,3, "existinguser_uid",        $existinguser->uid);
  wachthond($extdebug,4, "existinguser",            $existinguser);

  $role_exist   = array_search($drupal_role, $existinguser->roles);
  if ($role_exist == FALSE AND $rolaction == 'ADD') {
    $role_to_add  = user_role_load_by_name($drupal_role);
    $existinguser_roles_new = $existinguser_roles_org + array($role_to_add->rid => $role_to_add->name);
    wachthond($extdebug,1,  "ADD ROLE '$drupal_role'",    "[WANT NODIG]");
  }
  if ($role_exist == TRUE AND $rolaction == 'REMOVE') {
    $existinguser_roles_new = array_diff($existinguser_roles_new, [$drupal_role]);
    wachthond($extdebug,1,  "REMOVE ROLE '$drupal_role'",   "[WANT OVERBODIG]");
  }

  wachthond($extdebug,3, "existinguser_roles_org",  $existinguser_roles_org);
  wachthond($extdebug,3, "existinguser_roles_new",  $existinguser_roles_new);

  if ($existinguser_roles_org != $existinguser_roles_new) {
    $existinguser->roles   = $existinguser_roles_new; // update user_roles met nieuwe waarde
    user_save($existinguser);
//    user_save((object) array('uid' => $existinguser->uid), (array) $existinguser);
    wachthond($extdebug,1,  "SUCCESS: VOOR UID $valid_drupalid CHANGED ROLES $ditevent_part_functie",
                "FROM $existinguser_roles_org TO $existinguser_roles_new");
  } else {
    wachthond($extdebug,1,  "SKIPPED UPDATE DRUPAL TO PROPER ROLES", "[WAS AL OK]");
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

function date_bigger($date_source, $date_target, string $date_source_name = NULL, string $date_target_name = NULL)
{
    // RETURN TRUE ALS TARGET DATE LEEG IS
    if ($date_source AND empty($date_target)) {
        return true;
    }
    // RETURN FALSE ALS SOURCE DATE LEEG IS
    if (empty($date_source) AND $date_target) {
        return false;
    }
    // RETURN IS BOTH ARE EMPTY
    if (empty($date_source) AND empty($date_target)) {
        return;
    }

    $extdebug                     = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $dateparse_date_source        = NULL;
    $dateparse_date_target        = NULL;
    $dateparse_date_source_error  = 0;
    $dateparse_date_target_error  = 0;
    $date_bigger_result           = "X";

    wachthond($extdebug,2, "************************************************************************");

    if (is_string($date_source) == true) {
        $date_source_date   = date('d-m-Y H:i:s', strtotime($date_source)); 
        $date_source_string = $date_source;
        wachthond($extdebug,2, 'inputdate source is a string',$date_source);    
    } else {
        $date_source_date   = $date_source;
        $date_source_string = $date_source->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate source is not a string',$date_source);    
    }
    $date_source_unix   = strtotime($date_source_string);

    if (is_string($date_target) == true) {
        $date_target_date   = date('d-m-Y H:i:s', strtotime($date_target));     
        $date_target_string = $date_target;
        wachthond($extdebug,2, 'inputdate target is a string',$date_target);
    } else {
        $date_target_date   = $date_target;
        $date_target_string = $date_target->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate target is not a string',$date_target);
    }
    $date_target_unix   = strtotime($date_target_string);

    if ($date_source)   {
        $dateparse_date_source        = date_parse($date_source_string);
        $dateparse_date_source_error  = $dateparse_date_source['error_count'] ?? NULL;
    }
    if ($date_target) {
        $dateparse_date_target        = date_parse($date_target_string);
        $dateparse_date_target_error  = $dateparse_date_target['error_count'] ?? NULL;
    }

    wachthond($extdebug,2, "************************************************************************");

    wachthond($extdebug,2, 'date_source_string',  $date_source_string); 
    wachthond($extdebug,2, 'date_source_date',    $date_source_date); 
    wachthond($extdebug,2, 'date_source_unix',    $date_source_unix); 

    wachthond($extdebug,2, 'date_target_string',  $date_target_string); 
    wachthond($extdebug,2, 'date_target_date',    $date_target_date); 
    wachthond($extdebug,2, 'date_target_unix',    $date_target_unix); 

    wachthond($extdebug,2, "************************************************************************");

    if ( ($dateparse_date_source_error + $dateparse_date_target_error) >= 1) {

        wachthond($extdebug,2, "date_bigger: dateparse_date_source_error: $dateparse_date_source_error", "NOTVALID [$dateparse_date_source - $date_source]");
        wachthond($extdebug,2, "date_bigger: dateparse_date_target_error: $dateparse_date_target_error", "NOTVALID [$dateparse_date_target - $date_target]");

        return false;
    }

    if ( ($dateparse_date_source_error + $dateparse_date_target_error) == 0) {

        if ($date_source_unix > $date_target_unix) {
            wachthond($extdebug,1, "$date_source_string > $date_target_string", "Bigger? YES $date_source_name");
            $date_bigger_result = 1;
        }
        if ($date_source_unix < $date_target_unix) {
            wachthond($extdebug,1, "$date_source_string < $date_target_string", "Bigger? NOT $date_source_name");
            $date_bigger_result = 0;
        } 
        if ($date_source_unix == $date_target_unix) {
            wachthond($extdebug,1, "$date_source_string = $date_target_string", "Bigger? EQL $date_source_name");
            $date_bigger_result = "E";
        }
        return $date_bigger_result;
    }
}

function date_biggerequal($date_source, $date_target, string $date_source_name = NULL, string $date_target_name = NULL)
{
    // RETURN TRUE ALS TARGET DATE LEEG IS
    if ($date_source AND empty($date_target)) {
        return true;
    }
    // RETURN FALSE ALS SOURCE DATE LEEG IS
    if (empty($date_source) AND $date_target) {
        return false;
    }
    // RETURN IS BOTH ARE EMPTY
    if (empty($date_source) AND empty($date_target)) {
        return;
    }

    $extdebug                     = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $dateparse_date_source        = NULL;
    $dateparse_date_target        = NULL;
    $dateparse_date_source_error  = 0;
    $dateparse_date_target_error  = 0;
    $date_biggerequal_result      = "X";

    wachthond($extdebug,2, "************************************************************************");

    if (is_string($date_source) == true) {
        $date_source_date   = date('d-m-Y H:i:s', strtotime($date_source)); 
        $date_source_string = $date_source;
        wachthond($extdebug,2, 'inputdate source is a string',$date_source);    
    } else {
        $date_source_date   = $date_source;
        $date_source_string = $date_source->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate source is not a string',$date_source);    
    }
    $date_source_unix   = strtotime($date_source_string);

    if (is_string($date_target) == true) {
        $date_target_date   = date('d-m-Y H:i:s', strtotime($date_target)); 
        $date_target_string = $date_target;
        wachthond($extdebug,2, 'inputdate target is a string',$date_target);
    } else {
        $date_target_date   = $date_target;
        $date_target_string = $date_target->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate target is not a string',$date_target);
    }
    $date_target_unix   = strtotime($date_target_string);

    if ($date_source)   {
        $dateparse_date_source        = date_parse($date_source_string);
        $dateparse_date_source_error  = $dateparse_date_source['error_count'] ?? NULL;
    }
    if ($date_target) {
        $dateparse_date_target        = date_parse($date_target_string);
        $dateparse_date_target_error  = $dateparse_date_target['error_count'] ?? NULL;
    }

    wachthond($extdebug,2, "************************************************************************");

    wachthond($extdebug,2, 'date_source_string',  $date_source_string); 
    wachthond($extdebug,2, 'date_source_date',    $date_source_date); 
    wachthond($extdebug,2, 'date_source_unix',    $date_source_unix); 

    wachthond($extdebug,2, 'date_target_string',  $date_target_string); 
    wachthond($extdebug,2, 'date_target_date',    $date_target_date); 
    wachthond($extdebug,2, 'date_target_unix',    $date_target_unix); 

    wachthond($extdebug,2, "************************************************************************");

    if ( ($dateparse_date_source_error + $dateparse_date_target_error) >= 1) {

        wachthond($extdebug,2, "date_biggerequal: dateparse_date_source_error: $dateparse_date_source_error", "NOTVALID [$dateparse_date_source - $date_source]");
        wachthond($extdebug,2, "date_biggerequal: dateparse_date_target_error: $dateparse_date_target_error", "NOTVALID [$dateparse_date_target - $date_target]");
        return false;
    }

    if ( ($dateparse_date_source_error + $dateparse_date_target_error) == 0) {

        if ($date_source_unix >= $date_target_unix) {
            wachthond($extdebug,1, "$date_source_string > $date_target_string", "Bigger? YES ($date_source_name >= $date_target_name)");
            $date_biggerequal_result = 1;
        }
        if ($date_source_unix < $date_target_unix) {
            wachthond($extdebug,1, "$date_source_string < $date_target_string", "Bigger? NOT ($date_source_name >= $date_target_name)");
            $date_biggerequal_result = 0;
        }
        if ($date_source_unix == $date_target_unix) {
            wachthond($extdebug,1, "$date_source_string = $date_target_string", "Bigger? EQL ($date_source_name >= $date_target_name)");
            $date_biggerequal_result = "E";
        }
        return $date_biggerequal_result;
    }
}

function date_between($date_check, $date_start, $date_einde, string $date_check_name = NULL, string $date_range_name = NULL)
{
    if (empty($date_check) OR empty($date_start) OR empty($date_einde)) {
        return false;
    }

    $extdebug                     = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $dateparse_date_check         = NULL;
    $dateparse_date_start         = NULL;
    $dateparse_date_einde         = NULL;
    $dateparse_date_check_error   = 0;
    $dateparse_date_start_error   = 0;
    $dateparse_date_einde_error   = 0;
    $date_between_result          = "X";

    wachthond($extdebug,2, "************************************************************************");

    if (is_string($date_check) == true) {
        $date_check_date  = date('d-m-Y H:i:s', strtotime($date_check)); 
        $date_check_string  = $date_check;
        wachthond($extdebug,2, 'inputdate check is a string',$date_check);        
    } else {
        $date_check_date    = $date_check;
        $date_check_string  = $date_check->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate check is not a string',$date_check);        
    }
    $date_check_unix      = strtotime($date_check_string);

    if (is_string($date_start) == true) {
        $date_start_date  = date('d-m-Y H:i:s', strtotime($date_start)); 
        $date_start_string  = $date_start;
        wachthond($extdebug,2, 'inputdate start is a string',$date_start);        
    } else {
        $date_start_date    = $date_start;
        $date_start_string  = $date_start->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate start is not a string',$date_start);        
    }
    $date_start_unix   = strtotime($date_start_string);

    if (is_string($date_einde) == true) {
        $date_einde_date  = date('d-m-Y H:i:s', strtotime($date_einde)); 
        $date_einde_string  = $date_einde;
        wachthond($extdebug,2, 'inputdate einde is a string',$date_einde);
    } else {
        $date_einde_date    = $date_einde;
        $date_einde_string  = $date_einde->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate einde is not a string',$date_einde);
    }
    $date_einde_unix   = strtotime($date_einde_string);

    if ($date_start)   {
        $dateparse_date_start          = date_parse($date_start_string);
        $dateparse_date_start_error    = $dateparse_date_start['error_count'] ?? NULL;
    }
    if ($date_einde)   {
        $dateparse_date_einde          = date_parse($date_einde_string);
        $dateparse_date_einde_error    = $dateparse_date_einde['error_count'] ?? NULL;
    }

    wachthond($extdebug,2, "************************************************************************");

    wachthond($extdebug,2, 'date_check_string',    $date_check_string);   
    wachthond($extdebug,2, 'date_check_date',      $date_check_date); 
    wachthond($extdebug,2, 'date_check_unix',      $date_check_unix); 

    wachthond($extdebug,2, 'date_start_string',    $date_start_string);   
    wachthond($extdebug,2, 'date_start_date',      $date_start_date); 
    wachthond($extdebug,2, 'date_start_unix',      $date_start_unix); 

    wachthond($extdebug,2, 'date_einde_string',    $date_einde_string);   
    wachthond($extdebug,2, 'date_einde_date',      $date_einde_date); 
    wachthond($extdebug,2, 'date_einde_unix',      $date_einde_unix); 

    wachthond($extdebug,2, "************************************************************************");

  if (($dateparse_date_check_error + $dateparse_date_start_error + $dateparse_date_einde_error) >= 1) {

    wachthond($extdebug,2, "dateparse_date_check_error: $dateparse_date_check_error", "NOTVALID [$dateparse_date_check - $date_check]");
    wachthond($extdebug,2, "dateparse_date_start_error: $dateparse_date_start_error", "NOTVALID [$dateparse_date_start - $date_start]");
    wachthond($extdebug,2, "dateparse_date_einde_error: $dateparse_date_einde_error", "NOTVALID [$dateparse_date_einde - $date_einde]");

    $date_between_result = NULL;
    return false;
  }

  if (($dateparse_date_check_error + $dateparse_date_start_error + $dateparse_date_einde_error) == 0) {

    if ($date_check_unix >= $date_start_unix && $date_check_unix <= $date_einde_unix) {
      wachthond($extdebug,1, "$date_check_name: $date_check_string IN $date_range_name [$date_start_string AND $date_einde_string]", "YES");
      $date_between_result = 1;
    } else {
      wachthond($extdebug,1, "$date_check_name: $date_check_string IN $date_range_name [$date_start_string AND $date_einde_string]", "NOT");
      $date_between_result = 0;
    }
      return $date_between_result;
  }
}

function infiscalyear($date_check, $date_compare, $date_check_name = NULL, $date_range_name = NULL)
{
  if (empty($date_check) OR empty($date_compare)) {
    return false;
  }

  $extdebug                     = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results
  $date_compare_fiscalyear      = NULL;
  $dateparse_date_check         = NULL;
  $dateparse_date_start         = NULL;
  $dateparse_date_einde         = NULL;
  $dateparse_date_check_error   = 0;
  $dateparse_date_start_error   = 0;
  $dateparse_date_einde_error   = 0;
  $date_between_result          = "X";

  $date_compare_fiscalyear      = curriculum_civicrm_fiscalyear($date_compare);
  $date_start                   = $date_compare_fiscalyear['fiscalyear_start'] ?? NULL;
  $date_einde                   = $date_compare_fiscalyear['fiscalyear_einde'] ?? NULL;

    wachthond($extdebug,2, "************************************************************************");

    if (is_string($date_check) == true) {
        $date_check_date  = date('d-m-Y H:i:s', strtotime($date_check)); 
        $date_check_string  = $date_check;
        wachthond($extdebug,2, 'inputdate check is a string',$date_check);        
    } else {
        $date_check_date    = $date_check;
        $date_check_string  = $date_check->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate check is not a string',$date_check);        
    }
    $date_check_unix      = strtotime($date_check_string);

    if (is_string($date_start) == true) {
        $date_start_date  = date('d-m-Y H:i:s', strtotime($date_start)); 
        $date_start_string  = $date_start;
        wachthond($extdebug,2, 'inputdate start is a string',$date_start);        
    } else {
        $date_start_date    = $date_start;
        $date_start_string  = $date_start->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate start is not a string',$date_start);        
    }
    $date_start_unix   = strtotime($date_start_string);

    if (is_string($date_einde) == true) {
        $date_einde_date  = date('d-m-Y H:i:s', strtotime($date_einde)); 
        $date_einde_string  = $date_einde;
        wachthond($extdebug,2, 'inputdate einde is a string',$date_einde);
    } else {
        $date_einde_date    = $date_einde;
        $date_einde_string  = $date_einde->format('d-m-Y H:i:s');
        wachthond($extdebug,2, 'inputdate einde is not a string',$date_einde);
    }
    $date_einde_unix   = strtotime($date_einde_string);

    if ($date_start)   {
        $dateparse_date_start          = date_parse($date_start_string);
        $dateparse_date_start_error    = $dateparse_date_start['error_count'] ?? NULL;
    }
    if ($date_einde)   {
        $dateparse_date_einde          = date_parse($date_einde_string);
        $dateparse_date_einde_error    = $dateparse_date_einde['error_count'] ?? NULL;
    }

    wachthond($extdebug,2, "************************************************************************");

    wachthond($extdebug,2, 'date_check_string',    $date_check_string);   
    wachthond($extdebug,2, 'date_check_date',      $date_check_date); 
    wachthond($extdebug,2, 'date_check_unix',      $date_check_unix); 

    wachthond($extdebug,2, 'date_start_string',    $date_start_string);   
    wachthond($extdebug,2, 'date_start_date',      $date_start_date); 
    wachthond($extdebug,2, 'date_start_unix',      $date_start_unix); 

    wachthond($extdebug,2, 'date_einde_string',    $date_einde_string);   
    wachthond($extdebug,2, 'date_einde_date',      $date_einde_date); 
    wachthond($extdebug,2, 'date_einde_unix',      $date_einde_unix); 

    wachthond($extdebug,2, "************************************************************************");

  if (($dateparse_date_check_error + $dateparse_date_start_error + $dateparse_date_einde_error) >= 1) {

    if ($dateparse_date_check_error == 1) {
      wachthond($extdebug,2, "infiscalyear dateparse_date_check_error: $dateparse_date_check_error", "NOTVALID [$dateparse_date_check - $date_check]");     
    }
    if ($dateparse_date_start_error == 1) {
      wachthond($extdebug,2, "infiscalyear dateparse_date_start_error: $dateparse_date_start_error", "NOTVALID [$dateparse_date_start - $date_start]");
    }
    if ($dateparse_date_einde_error == 1) { 
      wachthond($extdebug,2, "infiscalyear dateparse_date_einde_error: $dateparse_date_einde_error", "NOTVALID [$dateparse_date_einde - $date_einde]");
    }

    $date_infiscalyear_result = NULL;
    return false;
  }

  if (($dateparse_date_check_error + $dateparse_date_start_error + $dateparse_date_einde_error) == 0) {

    if ($date_check_unix >= $date_start_unix && $date_check_unix <= $date_einde_unix) {
      wachthond($extdebug,2, "infiscalyear $date_check_name: \t$date_check_string IN [$date_start_string AND $date_einde_string] $date_range_name", "YES");
      $date_infiscalyear_result = 1;
    } elseif ($date_check_unix <= $date_start_unix) {
      wachthond($extdebug,2, "infiscalyear $date_check_name: \t$date_check_string <= $date_start_string [$date_range_name]", "BEF");
      $date_infiscalyear_result = 'before';
    } elseif ($date_check_unix >= $date_einde_unix) {
      wachthond($extdebug,2, "infiscalyear $date_check_name: \t$date_check_string >= $date_einde_string [$date_range_name]", "AFT");
      $date_infiscalyear_result = 'after';
    } else {
      wachthond($extdebug,2, "infiscalyear $date_check_name: \t$date_check_string IN [$date_start_date AND $date_einde_date] $date_range_name", "NOT");
      $date_infiscalyear_result = 0;
    } 
    return $date_infiscalyear_result;
  }
}

function curriculum_civicrm_fiscalyear($inputdatum) {

    if (empty($inputdatum)) {
        return;
    }

    $extdebug     = 0;  // 1 = basic // 2 = verbose // 3 = params / 4 = results

    $fiscalyear_start           = NULL;
    $ditevent_fiscalyear_einde  = NULL;

    $config           = CRM_Core_Config::singleton( );
    $fiscalYearStart  = $config->fiscalYearStart;
    $today_datetime   = date("Y-m-d H:i:s");
    $inputyear        = date('Y',     strtotime($inputdatum ?? ''));
    $inputdatum       = date('Y-m-d', strtotime($inputdatum ?? ''));

    ################################################################################################
      if ($extdebug >= 1) { watchdog('php','<pre>### BEPAAL FISCALE JAAR VAN EEN GEGEVEN DATUM [inputdate: '.$inputdatum.'] ###</pre>',NULL,WATCHDOG_DEBUG);}
    ################################################################################################

    ### 1 BASIS WAARDEN
    wachthond($extdebug,2, 'fiscalyear_start',      $fiscalYearStart); 
    wachthond($extdebug,2, 'inputdate',             $inputdatum);
    wachthond($extdebug,2, 'todaydatetime',         $today_datetime); 

    $fiscalyear_start = date("$inputyear-$fiscalYearStart[M]-$fiscalYearStart[d]");
    $fiscalyear_einde = date("$inputyear-$fiscalYearStart[M]-$fiscalYearStart[d]");
    $fiscalyear_start = date('Y-m-d', strtotime($fiscalyear_start ?? ''));
    $fiscalyear_einde = date('Y-m-d', strtotime($fiscalyear_einde ?? ''));

    ### 2 START MOET EERDER ZIJN DAN INPUT DATUM (ANDERS STARTDATE -1)
    if (strtotime($fiscalyear_start) > strtotime($inputdatum ?? '')) {
      wachthond($extdebug,2, "### fiscalyear_start $fiscalyear_start moet -1 jaar want > inputdatum $inputdatum");
      $fiscalyear_start = date('Y-m-d',(strtotime ( '-1 year' , strtotime ( $fiscalyear_start ) ) ) );
      wachthond($extdebug,2, "fiscalyear_start -1 year", $fiscalyear_start);
    }

    ### 3 END MOET LATER ZIJN DAN INPUT DATUM (ANDERS ENDDATE +1)
    if (strtotime($fiscalyear_einde)   <= strtotime($inputdatum ?? '')) {
      wachthond($extdebug,2, "### fiscalyear_einde $fiscalyear_einde moet +1 jaar want < inputdatum $inputdatum");
      $fiscalyear_einde = date('Y-m-d',(strtotime ( '+1 year' , strtotime ( $fiscalyear_einde ) ) ) );    
      wachthond($extdebug,2, "fiscalyear_end +1 year", $fiscalyear_einde);
    }

    ### 4 CORRIGEER DATUMS OP DAG NAUWKEURIG
    wachthond($extdebug,2, "### fiscalyear_einde $fiscalyear_einde moet minus 1 dag om laatste dag fiscal year te markeren");
    $fiscalyear_start   = date('Y-m-d',(strtotime ( '-0 day' , strtotime ( $fiscalyear_start ) ) ) );
    $fiscalyear_einde   = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $fiscalyear_einde ) ) ) );
    wachthond($extdebug,2, "fiscalyear_einde -1 day", $fiscalyear_einde);

    $fiscalyear_array = array(
      'fiscalyear_input' => $inputdatum,
      'fiscalyear_start' => $fiscalyear_start,
      'fiscalyear_einde' => $fiscalyear_einde,
    );

    ### 5 EINDRESULTAAT
    wachthond($extdebug,1, "fiscalyear resultaat obv inputdatum:",  $inputdatum);
    wachthond($extdebug,1, "fiscalyear_start",                      $fiscalyear_start);
    wachthond($extdebug,1, "fiscalyear_einde",                      $fiscalyear_einde);
    wachthond($extdebug,1, "fiscalyear_array",                      $fiscalyear_array);

    # ATTEMPTY TO RETREIVE FISCAL YEAR START VALUE FROM CACHE

//    Civi::cache()->set('cache_fiscalyear_start',  $today_fiscalyear_start);
//    Civi::cache()->set('cache_fiscalyear_einde',  $today_fiscalyear_einde);

    return $fiscalyear_array;
}

function base_cid2cont($contactid) {

    $contact_id             = $contactid;
    $result_cid2cont_count  = 0;

    if (empty($contact_id)) {
        return false;
    }

    $extdebug           = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results

    $profilecont        = array(225);
    $profilepartdeel    = array(139);
    $profilepartleid    = array(190);
    $profilepartref     = array(213);
    $profilepartvog     = array(140);

    $profilepart        = array_merge($profilepartdeel, $profilepartleid);
    $profilepartintake  = array_merge($profilepartref,  $profilepartvog);

    $profilecontmax     = array_merge($profilecont);
    $profilepartmax     = array_merge($profilepart,     $profilepartintake);
    $profilepartleidmax = array_merge($profilepartleid, $profilepartintake);
    $profilecv          = array_merge($profilecont,     $profilepart);
    $profilecvmax       = array_merge($profilecontmax,  $profilepartmax);

    wachthond($extdebug,1, "########################################################################");
    wachthond($extdebug,1, "### BASE - CHECK CONTACT INFO VAN DEZE PERSOON", "[EntityID: $entityID]");
    wachthond($extdebug,1, "########################################################################");

    $params_cid2cont = [
        'checkPermissions' => FALSE,
        'debug' => $apidebug,
        'limit' => 1,
        'select' => [
            'contact_type',
            'contact_sub_type',
            'id',
            'contact_id',
            'image_URL',            
            'birth_date', 
            'gender_id:label', 
            'email.email',
            'first_name',
            'middle_name',
            'last_name',
            'nick_name',
            'display_name',
            'job_title',
            'external_identifier',

            'PRIVACY.Contactvoorkeuren',
            'PRIVACY.Geheim_adres',
            'PRIVACY.Toestemming_beeldgebruik',

            'PRIVACY.notificatie_deel',
            'PRIVACY.notificatie_leid',
            'PRIVACY.notificatie_kamp',
            'PRIVACY.notificatie_staf',

            'Curriculum.CV_Deel',
            'Curriculum.CV_Leid',
            'Curriculum.Keren_Deel',
            'Curriculum.Keren_Leid',
            'Curriculum.Laatste_keer',

            'INTAKE.INT_status',
            'INTAKE.Intakegesprek_datum',
            'INTAKE.Intakegesprek_persoon',

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

            'MEDISCH.medisch_issues',
            'MEDISCH.medisch_toelichting',
            'MEDISCH.medisch_medicatie',
            'MEDISCH.medisch_luchtwegklachten',
            'MEDISCH.medisch_notities',
            'MEDISCH.medisch_doublecheck',

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
            ['id',                  '=', $contact_id],
            ['contact_type',        '=', 'Individual'],            
        ],
    ];

    wachthond($extdebug,7, 'params_cid2cont',                           $params_cid2cont);
    if ($contact_id) { $result_cid2cont = civicrm_api4('Contact','get', $params_cid2cont); }
    wachthond($extdebug,9, 'result_cid2cont',                           $result_cid2cont);

//      wachthond($extdebug,1, "########################################################################");
//      wachthond($extdebug,1, "### BASE - ASIGN RETREIVED VALUES TO VARIABLES", "[groupID: $groupID] [op: $op]");
//      wachthond($extdebug,1, "########################################################################");     

    if ($result_cid2cont[0]['contact_type'])    { $contact_type     = trim($result_cid2cont[0]['contact_type'])           ?? NULL;    }
    if ($result_cid2cont[0]['contact_sub_type']){ $contact_subtype  = $result_cid2cont[0]['contact_sub_type']             ?? NULL;    }

    if ($result_cid2cont[0]['image_URL'])       { $contact_foto     = trim($result_cid2cont[0]['image_URL'])              ?? NULL;    }
    if ($result_cid2cont[0]['birth_date'])      { $birth_date       = trim($result_cid2cont[0]['birth_date'])             ?? NULL;    }
    if ($result_cid2cont[0]['gender_id:label']) { $gender           = trim($result_cid2cont[0]['gender_id:label'])        ?? NULL;    }
    if ($result_cid2cont[0]['first_name'])      { $first_name       = ucfirst(trim($result_cid2cont[0]['first_name']))    ?? NULL;    }
    if ($result_cid2cont[0]['middle_name'])     { $middle_name      = strtolower(trim($result_cid2cont[0]['middle_name']))?? NULL;    }
    if ($result_cid2cont[0]['last_name'])       { $last_name        = ucfirst(trim($result_cid2cont[0]['last_name']))     ?? NULL;    }
    if ($result_cid2cont[0]['nick_name'])       { $nick_name        = ucfirst(trim($result_cid2cont[0]['nick_name']))     ?? NULL;    }

    if ($first_name AND $middle_name AND $last_name) {
        $displayname = $first_name." ".$middle_name." ".$last_name;
    } elseif ($first_name AND empty($middle_name) AND $last_name) {
        $displayname = $first_name." ".$last_name;
    } elseif ($first_name AND empty($middle_name) AND empty($last_name)) {
        $displayname = $first_name;
    } else {
        $displayname = NULL;
    }

    if ($result_cid2cont[0]['job_title'])            {   $crm_drupalnaam = trim($result_cid2cont[0]['job_title']);            }
    if ($result_cid2cont[0]['external_identifier'])  {   $crm_externalid = trim($result_cid2cont[0]['external_identifier']);  }

    wachthond($extdebug,2, 'entityID',                  $entityID);
    wachthond($extdebug,2, 'contact_id',                $contact_id);
    wachthond($extdebug,2, 'birth_date',                $birth_date);
    wachthond($extdebug,2, 'gender',                    $gender);
    wachthond($extdebug,2, 'first_name',                $first_name);
    wachthond($extdebug,2, 'middle_name',               $middle_name);
    wachthond($extdebug,2, 'last_name',                 $last_name);
    wachthond($extdebug,2, 'nick_name',                 $nick_name);
    wachthond($extdebug,2, 'displayname',               $displayname);
    wachthond($extdebug,2, 'crm_drupalnaam',            $crm_drupalnaam);
    wachthond($extdebug,2, 'crm_externalid',            $crm_externalid);        

    $datum_belangstelling       = $result_cid2cont[0]['WERVING.Datum_belangstelling']   ?? NULL;
//  $intakegesprekdatum         = $result_cid2cont[0]['INTAKE.Intakegesprek_datum']     ?? NULL;
//  $intakegesprekpersoon       = $result_cid2cont[0]['INTAKE.Intakegesprek_persoon']   ?? NULL;

    wachthond($extdebug,2, 'datum_belangstelling',      $datum_belangstelling);
//  wachthond($extdebug,2, 'intakegesprekdatum',        $intakegesprekdatum);
//  wachthond($extdebug,2, 'intakegesprekpersoon',      $intakegesprekpersoon);         

//  $ditjaar_nawgecheckt        = $result_cid2cont[0]['INTAKE.NAW_gecheckt']            ?? NULL;
//  $ditjaar_bioingevuld        = $result_cid2cont[0]['INTAKE.BIO_ingevuld']            ?? NULL;    
//  $ditjaar_biogecheckt        = $result_cid2cont[0]['INTAKE.BIO_gecheckt']            ?? NULL;    

    $org_ditjaar_nawgecheckt    = $ditjaar_nawgecheckt; // ZET DE NIEUWE PARAMETERS INITIEEL MET WAARDE VAN DE OUDE
    $new_ditjaar_nawgecheckt    = $ditjaar_nawgecheckt; // ZET DE NIEUWE PARAMETERS INITIEEL MET WAARDE VAN DE OUDE
    $org_ditjaar_biogecheckt    = $ditjaar_biogecheckt; // ZET DE NIEUWE PARAMETERS INITIEEL MET WAARDE VAN DE OUDE
    $new_ditjaar_biogecheckt    = $ditjaar_biogecheckt; // ZET DE NIEUWE PARAMETERS INITIEEL MET WAARDE VAN DE OUDE

//  wachthond($extdebug,2, 'ditjaar_nawgecheckt',       $ditjaar_nawgecheckt);
//  wachthond($extdebug,2, 'ditjaar_bioingevuld',       $ditjaar_bioingevuld);
//  wachthond($extdebug,2, 'ditjaar_biogecheckt',       $ditjaar_biogecheckt);
//  wachthond($extdebug,2, 'org_ditjaar_nawgecheckt',   $org_ditjaar_nawgecheckt);
//  wachthond($extdebug,2, 'new_ditjaar_nawgecheckt',   $new_ditjaar_nawgecheckt);

    $leeftijd_vantoday_deci     = $result_cid2cont[0]['WERVING.leeftijd_decimalen']     ?? NULL;
    $leeftijd_nextkamp_deci     = $result_cid2cont[0]['WERVING.nextkamp_decimalen']     ?? NULL;

    $werving_mee_komendkamp     = $result_cid2cont[0]['WERVING.mee_komendkampjaar']     ?? NULL;
    $werving_mee_verwachting    = $result_cid2cont[0]['WERVING.mee_verwachting']        ?? NULL;
    $werving_mee_toelichting    = $result_cid2cont[0]['WERVING.mee_toelichting']        ?? NULL;
    $werving_mee_update         = $result_cid2cont[0]['WERVING.mee_update']             ?? NULL;
    $werving_mee_update_year    = date('Y', strtotime($werving_mee_update ?? ''));
    $werving_mee_notities       = $result_cid2cont[0]['WERVING.mee_notities']           ?? NULL;

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
    $laatstekeer            = $result_cid2cont[0]['Curriculum.Laatste_keer']            ?? NULL;    // M61: tbv jaar 'mee komend jaar'       
    $curcv_deel_array       = $result_cid2cont[0]['Curriculum.CV_Deel']                 ?? NULL;    // welke jaren deel
    $curcv_leid_array       = $result_cid2cont[0]['Curriculum.CV_Leid']                 ?? NULL;    // welke jaren leid 
    $curcv_keer_deel        = $result_cid2cont[0]['Curriculum.Keren_Deel']              ?? NULL;    // keren deel
    $curcv_keer_leid        = $result_cid2cont[0]['Curriculum.Keren_Leid']              ?? NULL;    // keren leid

    wachthond($extdebug,2, 'laatstekeer',               $laatstekeer);
    wachthond($extdebug,2, 'curcv_deel_array',          $curcv_deel_array);
    wachthond($extdebug,2, 'curcv_leid_array',          $curcv_leid_array);
    wachthond($extdebug,2, 'curcv_keer_deel',           $curcv_keer_deel);
    wachthond($extdebug,2, 'curcv_keer_leid',           $curcv_keer_leid);

    $privacy_voorkeuren     = $result_cid2cont[0]['PRIVACY.Contactvoorkeuren']          ?? NULL;    // bv. Verwijder contactgegevens
    $privacy_geheimadres    = $result_cid2cont[0]['PRIVACY.Geheim_adres']               ?? NULL;    // bv. Ja / Nee
    $privacy_beeldgebruik   = $result_cid2cont[0]['PRIVACY.Toestemming_beeldgebruik']   ?? NULL;    // Ik geef toestemming voor kampfotos

    wachthond($extdebug,2, 'privacy_voorkeuren',        $privacy_voorkeuren);
    wachthond($extdebug,2, 'privacy_geheimadres',       $privacy_geheimadres);
    wachthond($extdebug,2, 'privacy_beeldgebruik',      $privacy_beeldgebruik);        

    $cont_notificatie_deel  = $result_cid2cont[0]['PRIVACY.notificatie_deel']            ?? NULL;
    $cont_notificatie_leid  = $result_cid2cont[0]['PRIVACY.notificatie_leid']            ?? NULL;
    $cont_notificatie_kamp  = $result_cid2cont[0]['PRIVACY.notificatie_kamp']            ?? NULL;
    $cont_notificatie_staf  = $result_cid2cont[0]['PRIVACY.notificatie_staf']            ?? NULL;

    wachthond($extdebug,2, 'cont_notificatie_deel',     $cont_notificatie_deel);
    wachthond($extdebug,2, 'cont_notificatie_leid',     $cont_notificatie_leid);
    wachthond($extdebug,2, 'cont_notificatie_kamp',     $cont_notificatie_kamp);
    wachthond($extdebug,2, 'cont_notificatie_staf',     $cont_notificatie_staf);        
   
    $cont_intnodig          = $result_cid2cont[0]['INTAKE.INT_nodig']                   ?? NULL;
    $cont_intstatus         = $result_cid2cont[0]['INTAKE.INT_status']                  ?? NULL;
    $cont_intake_datum      = $result_cid2cont[0]['INTAKE.Intakegesprek_datum']         ?? NULL;
    $cont_intake_persoon    = $result_cid2cont[0]['INTAKE.Intakegesprek_persoon']       ?? NULL;

    $cont_nawnodig          = $result_cid2cont[0]['INTAKE.NAW_nodig']                   ?? NULL;
    $cont_nawstatus         = $result_cid2cont[0]['INTAKE.NAW_status']                  ?? NULL;
    $cont_nawgecheckt       = $result_cid2cont[0]['INTAKE.NAW_gecheckt']                ?? NULL;

    $cont_bionodig          = $result_cid2cont[0]['INTAKE.BIO_nodig']                   ?? NULL;
    $cont_biostatus         = $result_cid2cont[0]['INTAKE.BIO_status']                  ?? NULL;
    $cont_bioingevuld       = $result_cid2cont[0]['INTAKE.BIO_ingevuld']                ?? NULL;
    $cont_biogecheckt       = $result_cid2cont[0]['INTAKE.BIO_gecheckt']                ?? NULL;

    $cont_refnodig          = $result_cid2cont[0]['INTAKE.REF_nodig']                   ?? NULL;
    $cont_refstatus         = $result_cid2cont[0]['INTAKE.REF_status']                  ?? NULL;
    $cont_refdatum          = $result_cid2cont[0]['INTAKE.REF_datum']                   ?? NULL;
    $cont_refnaam           = $result_cid2cont[0]['INTAKE.REF_naam']                    ?? NULL;
    $cont_refpersoon        = $result_cid2cont[0]['INTAKE.ref_persoon']                 ?? NULL;
    $cont_reffeedback       = $result_cid2cont[0]['INTAKE.ref_feedback']                ?? NULL;

    $cont_vognodig          = $result_cid2cont[0]['INTAKE.VOG_nodig']                   ?? NULL;
    $cont_vogstatus         = $result_cid2cont[0]['INTAKE.VOG_status']                  ?? NULL;
    $cont_voglaatste        = $result_cid2cont[0]['INTAKE.VOG_laatste']                 ?? NULL;

    $continfo_array = array(
        'contact_type'              => $contact_type,
        'contact_subtype'           => $contact_subtype,

        'contact_id'                => $contact_id,
        'contact_foto'              => $contact_foto,
        'birth_date'                => $birth_date,
        'gender'                    => $gender,
        'first_name'                => $first_name,
        'middle_name'               => $middle_name,
        'last_name'                 => $last_name,
        'nick_name'                 => $nick_name,
        'displayname'               => $displayname,
        'crm_drupalnaam'            => $crm_drupalnaam,
        'crm_externalid'            => $crm_externalid,

        'leeftijd_vantoday_deci'    => $leeftijd_vantoday_deci,
        'leeftijd_nextkamp_deci'    => $leeftijd_nextkamp_deci,

        'laatstekeer'               => $laatstekeer,
        'curcv_deel_array'          => $curcv_deel_array,
        'curcv_leid_array'          => $curcv_leid_array,
        'oldcv_deel_array'          => $oldcv_deel_array,
        'oldcv_leid_array'          => $oldcv_leid_array,
        'curcv_keer_deel'           => $curcv_keer_deel,
        'curcv_keer_leid'           => $curcv_keer_leid,

        'werving_mee_komendkamp'    => $werving_mee_komendkamp,
        'werving_mee_verwachting'   => $werving_mee_verwachting,
        'werving_mee_toelichting'   => $werving_mee_toelichting,
        'werving_mee_update'        => $werving_mee_update,
        'werving_mee_update_year'   => $werving_mee_update_year,
        'werving_mee_notities'      => $werving_mee_notities,
        'werving_vakantieregio'     => $werving_vakantieregio,            

        'privacy_voorkeuren'        => $privacy_voorkeuren,
        'privacy_geheimadres'       => $privacy_geheimadres,
        'privacy_beeldgebruik'      => $privacy_beeldgebruik,
        'cont_notificatie_deel'     => $cont_notificatie_deel,
        'cont_notificatie_leid'     => $cont_notificatie_leid,
        'cont_notificatie_kamp'     => $cont_notificatie_kamp,
        'cont_notificatie_staf'     => $cont_notificatie_staf,

        'datum_belangstelling'      => $datum_belangstelling,
//      'intakegesprek_datum'       => $intakegesprekdatum,
//      'intakegesprek_persoon'     => $intakegesprekpersoon,

        'cont_intnodig'             => $cont_intnodig,
        'cont_intstatus'            => $cont_intstatus,
        'cont_intake_datum'         => $cont_intake_datum,
        'cont_intake_persoon'       => $cont_intake_persoon,

        'cont_nawnodig'             => $cont_nawnodig,
        'cont_nawstatus'            => $cont_nawstatus,
        'cont_nawgecheckt'          => $cont_nawgecheckt,

        'cont_bionodig'             => $cont_bionodig,
        'cont_biostatus'            => $cont_biostatus,
        'cont_bioingevuld'          => $cont_bioingevuld,
        'cont_biogecheckt'          => $cont_biogecheckt,

        'cont_refnodig'             => $cont_refnodig,
        'cont_refstatus'            => $cont_refstatus,
        'cont_refdatum'             => $cont_refdatum,
        'cont_refnaam'              => $cont_refnaam,

        'cont_vognodig'             => $cont_vognodig,
        'cont_vogstatus'            => $cont_vogstatus,
        'cont_voglaatste'           => $cont_voglaatste,
    );

    wachthond($extdebug,1, "continfo_array", $continfo_array);

    return $continfo_array;

}

function base_pid2part($partid) {

    $extdebug               = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results

    wachthond($extdebug,1, "########################################################################");
    wachthond($extdebug,1, "### BASE - BEPAAL HET CONTACT ID OBV PART_ID:",           "[PID:$partid]");
    wachthond($extdebug,1, "########################################################################");

    $contact_id             = NULL;
    $result_pid2part_count  = 0;

    $part_id                = $partid;

    if (empty($partid)) {
        return false;
    }

    $eventtypesdeel         = array(11,12,13,14,21,22,23,24,33);    //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelkkjk     = array(11,12,13,14,21,22,23,24);       //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)       
    $eventtypesdeeltop      = array(33);                            //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelandtop   = array_merge($eventtypesdeel, $eventtypesdeeltop);

    $eventtypesleid         = array(1);                             //  EVENT_TYPE_ID VAN HET LEIDING EVENT VAN DIT JAAR    (- TEST_LEID)
    $eventtypesmeet         = array(2);                             //  EVENT_TYPE_ID VAN HET KAMPSTAF EVENT VAN DIT JAAR   (- KAMPSTAF)

    $eventtypesdeeltest     = array(102);
    $eventtypesleidtest     = array(101);
    $eventtypesdeeltoptest  = array(103);

    $eventtypesprod         = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesleid);
    $eventtypestest         = array_merge($eventtypesdeeltest,  $eventtypesdeeltoptest, $eventtypesleidtest);
    $eventtypesall          = array_merge($eventtypesprod,      $eventtypestest);

    $eventtypesdeelall      = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesdeeltest,    $eventtypesdeeltoptest);
    $eventtypesleidall      = array_merge($eventtypesleid,      $eventtypesleidtest,    $eventtypesmeet);

    $params_pid2part = [
        'checkPermissions'  => FALSE,
        'debug'             => $apidebug,
        'orderBy' => [
            'event_id' => 'ASC',
        ],
        'limit'     => 1,
        'select'    => [
            'row_count',
            'id', 
            'contact_id',
            'contact_id.first_name',
            'contact.display_name',
            'contact.Curriculum.Keren_Deel',
            'contact.Curriculum.Keren_Leid',

            'status_id',
            'status_id:name',
            'role_id', 
            'register_date',
            'event_id',                     // event_id stored in participant table

            'event.id',                     // event_id stored in joined event table
            'event.title',
            'event.event_type_id',          // integer                 bv. 21
            'event.event_type_id:label',    // zelfde als naam event:  bv. Kinderkamp week 2
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

            'PART_INTERN.brengen_van',
            'PART_INTERN.brengen_tot',
            'PART_INTERN.pres_van',
            'PART_INTERN.pres_tot',
            'PART_INTERN.halen_van',
            'PART_INTERN.halen_tot',

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

            'PART_LEID_VOG.Datum_verzoek',
            'PART_LEID_VOG.Datum_aanvraag',
            'PART_LEID_VOG.Datum_ontvangst',
            'PART_LEID_VOG.Datum_nieuwe_VOG',
            'PART_LEID_VOG.Kenmerk_VOG',

            'PART_LEID_HOOFD.notificatie_deel',
            'PART_LEID_HOOFD.notificatie_leid',
            'PART_LEID_HOOFD.notificatie_kamp',
            'PART_LEID_HOOFD.notificatie_staf',
            'PART_LEID_HOOFD.Jouw_prive_emailadres',

            'PART_EVALUATIE.terugblik_score:label',
            'PART_EVALUATIE.kampthema_score:label',
            'PART_EVALUATIE.inhoud_score:label',
            'PART_EVALUATIE.actief_score:label',
            'PART_EVALUATIE.slapen_score:label',
            'PART_EVALUATIE.etendrinken_score:label',
            'PART_EVALUATIE.brengenhalen_score:label',
            'PART_EVALUATIE.kampinfo_score:label',
            'PART_EVALUATIE.aanraden_score:label',
        ],
        'join' => [
            ['Event AS event',      'INNER',    ['event_id',    '=', 'event.id']],
            ['Contact AS contact',  'INNER',    ['contact_id',  '=', 'contact.id']],
        ],
        'where' => [
            ['id', '=',  $part_id],
        ],
    ];

    wachthond($extdebug,3, 'params_pid2part',            $params_pid2part);
    $result_pid2part = civicrm_api4('Participant','get', $params_pid2part);
    wachthond($extdebug,3, 'result_pid2part',            $result_pid2part);

    if ($result_pid2part) {
        $result_pid2part_count  = $result_pid2part->countMatched();
    }

//  M61: TODO: misschien hier wat logic naartoe verplaatset zoals:
//  1. bepalen waarde kampjaar obv event_start                          V
//  2. bapelen waarde kamprol  obv event_type                           V
//  3. bepalen waarde ditevent_fiscalejaar_start obv event_start        V

    $ditevent_event_start           = $result_pid2part[0]['event.start_date'];
    $ditevent_kampjaar              = date('Y', strtotime($ditevent_event_start))           ?? NULL;
    $ditevent_kampjaar_kort         = date('y', strtotime($ditevent_event_start))           ?? NULL;

    $ditevent_part_kampstart        = $result_pid2part[0]['PART.PART_kampstart'];
    $ditevent_part_kampeinde        = $result_pid2part[0]['PART.PART_kampeinde'];

    if (empty($ditevent_part_kampstart) OR empty($ditevent_part_kampeinde))    {
        // M61: TODO if empty retrieve via aparte query
    }

    $ditevent_fiscalyear            = curriculum_civicrm_fiscalyear($ditevent_event_start);
    $ditevent_fiscalyear_start      = $ditevent_fiscalyear['fiscalyear_start']              ?? NULL;
    $ditevent_fiscalyear_einde      = $ditevent_fiscalyear['fiscalyear_einde']              ?? NULL;

    $ditevent_part_kampweek         = $result_pid2part[0]['PART.PART_kampweek_nr']          ?? NULL;
    $ditevent_part_kampweek         = $result_pid2part[0]['PART.PART_kampweek_nr']          ?? NULL;

    $ditevent_event_type_id         = $result_pid2part[0]['event.event_type_id']            ?? NULL;
    $ditevent_leid_functie          = $result_pid2part[0]['PART_LEID.Functie']              ?? NULL;

    wachthond($extdebug,3, 'ditevent_event_type_id',    $ditevent_event_type_id);
    wachthond($extdebug,3, 'ditevent_leid_functie',     $ditevent_leid_functie);
    wachthond($extdebug,3, 'eventtypesdeelall',         $eventtypesdeelall);
    wachthond($extdebug,3, 'eventtypesleidall',         $eventtypesleidall);

    if (in_array($ditevent_event_type_id, $eventtypesdeelall)) {
        $ditevent_part_functie      = 'deelnemer';
        $ditevent_part_rol          = 'deelnemer';
    }
    if (in_array($ditevent_event_type_id, $eventtypesleidall) OR $ditevent_event_type_id == 1) {
        $ditevent_part_functie      = $ditevent_leid_functie;
        $ditevent_part_rol          = 'leiding';
    }
    // M61: ditevent_leid_functie zou bij aanmelding niet leeg moeten kunnen zijn
    if ($ditevent_event_type_id == 1 AND empty($ditevent_leid_functie)) {
        $ditevent_leid_functie      = 'kampleiding';
        $ditevent_part_functie      = 'kampleiding';
        $ditevent_part_rol          = "leiding";
    }

    wachthond($extdebug,3, 'ditevent_part_functie',         $ditevent_part_functie);
    wachthond($extdebug,3, 'ditevent_part_rol',             $ditevent_part_rol);    

    $ditevent_part_kampkort         = $result_pid2part[0]['PART.PART_kampkort']                ?? NULL;
    $ditevent_part_kampkort_low     = preg_replace('/[^ \w-]/','',strtolower(trim($ditevent_part_kampkort)));  // only letters/numbers/dashes
    $ditevent_part_kampkort_cap     = preg_replace('/[^ \w-]/','',strtoupper(trim($ditevent_part_kampkort)));  // only letters/numbers/dashes

    $ditevent_event_kampkort        = $result_pid2part[0]['event.Event_Kenmerken.kampkort']    ?? NULL;
    $ditevent_event_kampkort_low    = preg_replace('/[^ \w-]/','',strtolower(trim($ditevent_event_kampkort)));  // only letters/numbers/dashes
    $ditevent_event_kampkort_cap    = preg_replace('/[^ \w-]/','',strtoupper(trim($ditevent_event_kampkort)));  // only letters/numbers/dashes

    $partinfo_array = array(
        'id'                        => $result_pid2part[0]['id'],
        'contact_id'                => $result_pid2part[0]['contact_id'],
        'displayname'               => $result_pid2part[0]['contact.display_name'],        

        'curcv_keer_deel'           => $result_pid2part[0]['contact.Curriculum.Keren_Deel'],
        'curcv_keer_leid'           => $result_pid2part[0]['contact.Curriculum.Keren_Leid'],

        'status_id'                 => $result_pid2part[0]['status_id'],
        'status_name'               => $result_pid2part[0]['status_id:name'],
        'role_id'                   => $result_pid2part[0]['role_id'],

        'part_event_id'             => $result_pid2part[0]['event_id'],
        'event_id'                  => $result_pid2part[0]['event.id'],
        'event_title'               => $result_pid2part[0]['event.title'],
        'event_type_id'             => $result_pid2part[0]['event.event_type_id'],
        'event_type_label'          => $result_pid2part[0]['event.event_type_id:label'],

        'register_date'             => $result_pid2part[0]['register_date'],
        'event_start_date'          => $result_pid2part[0]['event.start_date'],
        'event_end_date'            => $result_pid2part[0]['event.end_date'],

        'event_fiscalyear'          => $ditevent_fiscalyear,
        'event_fiscalyear_start'    => $ditevent_fiscalyear_start,
        'event_fiscalyear_einde'    => $ditevent_fiscalyear_einde,

        'part_kampstart'            => $result_pid2part[0]['PART.PART_kampstart'],
        'part_kampeinde'            => $result_pid2part[0]['PART.PART_kampeinde'],

        'part_kampjaar'             => $ditevent_kampjaar,
        'part_kampjaar_kort'        => $ditevent_kampjaar_kort,
        'part_eventjaar'            => $result_pid2part[0]['event.start_date'],

        'part_kampnaam'             => $result_pid2part[0]['PART.PART_kamplang'],
        'part_kampkort'             => $result_pid2part[0]['PART.PART_kampkort'],
        'part_kampkort_low'         => $ditevent_part_kampkort_low,
        'part_kampkort_cap'         => $ditevent_part_kampkort_cap,

        'part_kamptype_naam'        => $result_pid2part[0]['PART.PART_kamptype_naam'],
        'part_kamptype_id'          => $result_pid2part[0]['PART.PART_kamptype_id'],
        'part_kampweek_nr'          => $result_pid2part[0]['PART.PART_kampweek_nr'],

        'part_functie'              => $ditevent_part_functie,
        'part_rol'                  => $ditevent_part_rol,

        'part_leid_kamp'            => $result_pid2part[0]['PART_LEID.Welk_kamp'],
        'part_leid_functie'         => $result_pid2part[0]['PART_LEID.Functie'],
        'part_vakantieregio'        => $result_pid2part[0]['PART.vakantieregio'],

        'kenmerken_kampnaam'        => $result_pid2part[0]['event.Event_Kenmerken.kampnaam'],
        'kenmerken_kampkort'        => $result_pid2part[0]['event.Event_Kenmerken.kampkort'],
        'kenmerken_kampkort_low'    => $ditevent_event_kampkort_low,
        'kenmerken_kampkort_cap'    => $ditevent_event_kampkort_cap,
 
        'kenmerken_kamptype_naam'   => $result_pid2part[0]['event.Event_Kenmerken.kamptype_naam'],
        'kenmerken_kamptype_label'  => $result_pid2part[0]['event.Event_Kenmerken.kamptype_naam:label'],
        'kenmerken_kamptype_id'     => $result_pid2part[0]['event.Event_Kenmerken.kamptype_id'],
        'kenmerken_kampsoort'       => $result_pid2part[0]['event.Event_Kenmerken.kampsoort'],

        'event_brengen_van'         => $result_pid2part[0]['event.Event_Kenmerken.brengen_van'],
        'event_brengen_tot'         => $result_pid2part[0]['event.Event_Kenmerken.brengen_tot'],
        'event_pres_van'            => $result_pid2part[0]['event.Event_Kenmerken.pres_van'],
        'event_pres_tot'            => $result_pid2part[0]['event.Event_Kenmerken.pres_tot'],
        'event_halen_tot'           => $result_pid2part[0]['event.Event_Kenmerken.halen_tot'],
        'event_halen_van'           => $result_pid2part[0]['event.Event_Kenmerken.halen_van'],

        'event_thema_naam'          => $result_pid2part[0]['event.Event_Linkjes.thema_naam'],
        'event_thema_info'          => $result_pid2part[0]['event.Event_Linkjes.thema_info'],
        'event_goeddoel_naam'       => $result_pid2part[0]['event.Event_Linkjes.goeddoel_naam'],
        'event_goeddoel_info'       => $result_pid2part[0]['event.Event_Linkjes.goeddoel_info'],
        'event_goeddoel_link'       => $result_pid2part[0]['event.Event_Linkjes.goeddoel_link'],

        'part_1stdeel'              => $result_pid2part[0]['PART.PART_1xkeer_deel'],
        'part_1stleid'              => $result_pid2part[0]['PART.PART_1xkeer_leid'],

        'part_nawgecheckt'          => $result_pid2part[0]['PART.NAW_gecheckt'],
        'part_biogecheckt'          => $result_pid2part[0]['PART.BIO_gecheckt'],

        'part_groepklas'            => $result_pid2part[0]['PART_DEEL.Groep_klas'],
        'part_voorkeur'             => $result_pid2part[0]['PART_DEEL.Voorkeur'],
        'part_groepsletter'         => $result_pid2part[0]['PART_INTERN.groep_letter'],
        'part_groepskleur'          => $result_pid2part[0]['PART_INTERN.groep_kleur'],
        'part_groepsnaam'           => $result_pid2part[0]['PART_INTERN.groep_naam'],
        'part_slaapzaal'            => $result_pid2part[0]['PART_INTERN.Slaapzaal'],

        'part_wachtlijst_erop'      => $result_pid2part[0]['PART_DEEL_INTERN.wachtlijst_erop'],
        'part_wachtlijst_eraf'      => $result_pid2part[0]['PART_DEEL_INTERN.wachtlijst_eraf'],
        'part_criteriacheck_start'  => $result_pid2part[0]['PART_DEEL_INTERN.criteriacheck_start'],
        'part_criteriacheck_einde'  => $result_pid2part[0]['PART_DEEL_INTERN.criteriacheck_einde'],

        'part_criteria_leeftijd'    => $result_pid2part[0]['PART_DEEL_INTERN.criteria_leeftijd'],
        'part_criteria_school'      => $result_pid2part[0]['PART_DEEL_INTERN.criteria_school'],
        'part_criteria_indicatie'   => $result_pid2part[0]['PART_DEEL_INTERN.criteria_indicatie'],
        'part_criteria_oordeel'     => $result_pid2part[0]['PART_DEEL_INTERN.criteria_oordeel'],

        'part_notificatie_deel'     => $result_pid2part[0]['PART_LEID_HOOFD.notificatie_deel'],
        'part_notificatie_leid'     => $result_pid2part[0]['PART_LEID_HOOFD.notificatie_leid'],
        'part_notificatie_kamp'     => $result_pid2part[0]['PART_LEID_HOOFD.notificatie_kamp'],
        'part_notificatie_staf'     => $result_pid2part[0]['PART_LEID_HOOFD.notificatie_staf'],
        'part_notificatie_priv'     => $result_pid2part[0]['PART_LEID_HOOFD.Jouw_prive_emailadres'],

        'part_kampgeld_contribid'   => $result_pid2part[0]['PART_KAMPGELD.contribid'],
        'part_kampgeld_regeling'    => $result_pid2part[0]['PART_KAMPGELD.regeling'],
        'part_kampgeld_fietshuur'   => $result_pid2part[0]['PART_KAMPGELD.fietshuur'],
        'event_fietsevent'          => $result_pid2part[0]['event.Event_Kenmerken.Fietshuur'],

        'part_eval_datum'           => $result_pid2part[0]['PART_EVALUATIE.DATUM_evaluatie'],
        'part_eval_terugblik'       => $result_pid2part[0]['PART_EVALUATIE.terugblik_score:label'],
        'part_eval_kampthema'       => $result_pid2part[0]['PART_EVALUATIE.kampthema_score:label'],
        'part_eval_inhoud'          => $result_pid2part[0]['PART_EVALUATIE.inhoud_score:label'],
        'part_eval_actief'          => $result_pid2part[0]['PART_EVALUATIE.actief_score:label'],
        'part_eval_slapen'          => $result_pid2part[0]['PART_EVALUATIE.slapen_score:label'],
        'part_eval_eten'            => $result_pid2part[0]['PART_EVALUATIE.etendrinken_score:label'],
        'part_eval_brengen'         => $result_pid2part[0]['PART_EVALUATIE.brengenhalen_score:label'],
        'part_eval_kampinfo'        => $result_pid2part[0]['PART_EVALUATIE.kampinfo_score:label'],
        'part_eval_aanrader'        => $result_pid2part[0]['PART_EVALUATIE.aanraden_score:label'],

        'part_intnodig'             => $result_pid2part[0]['PART_LEID_INTERN.INT_nodig'],
        'part_nawnodig'             => $result_pid2part[0]['PART_LEID_INTERN.NAW_nodig'],
        'part_bionodig'             => $result_pid2part[0]['PART_LEID_INTERN.BIO_nodig'],
        'part_refnodig'             => $result_pid2part[0]['PART_LEID_INTERN.REF_nodig'],
        'part_vognodig'             => $result_pid2part[0]['PART_LEID_INTERN.VOG_nodig'],

        'part_intstatus'            => $result_pid2part[0]['PART_LEID_INTERN.INT_status'],
        'part_nawstatus'            => $result_pid2part[0]['PART_LEID_INTERN.NAW_status'],
        'part_biostatus'            => $result_pid2part[0]['PART_LEID_INTERN.BIO_status'],
        'part_refstatus'            => $result_pid2part[0]['PART_LEID_INTERN.REF_status'],
        'part_vogstatus'            => $result_pid2part[0]['PART_LEID_INTERN.VOG_status'],

        'part_naw_gecheckt'         => $result_pid2part[0]['PART.NAW_gecheckt'],
        'part_bio_gecheckt'         => $result_pid2part[0]['PART.BIO_gecheckt'],

        'part_refpersoon'           => $result_pid2part[0]['PART_LEID_REF.REF_persoon'],
        'part_refgevraagd'          => $result_pid2part[0]['PART_LEID_REF.REF_gevraagd'],
        'part_reffeedback'          => $result_pid2part[0]['PART_LEID_REF.REF_feedback'],

        'part_vogverzoek'           => $result_pid2part[0]['PART_LEID_VOG.Datum_verzoek'],
        'part_vogaanvraag'          => $result_pid2part[0]['PART_LEID_VOG.Datum_aanvraag'],
        'part_vogdatum'             => $result_pid2part[0]['PART_LEID_VOG.Datum_nieuwe_VOG'],

    );

    return $partinfo_array;

}

function base_eid2event($entityID, $partID = NULL) {

    ##########################################################################################
    # RETREIVE EVENT INFO OBV EID
    ##########################################################################################

    $extdebug               = 0; // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug               = FALSE;

    if (empty($entityID)) {
        return false;
    }

    $ditevent_part_eventid  = $entityID     ?? NULL;
    $ditevent_part_id       = $partID       ?? NULL;

    $eventtypesdeel         = array(11,12,13,14,21,22,23,24,33);    //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelkkjk     = array(11,12,13,14,21,22,23,24);       //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)       
    $eventtypesdeeltop      = array(33);                            //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeelandtop   = array_merge($eventtypesdeel, $eventtypesdeeltop);

    $eventtypesleid         = array(1);                             //  EVENT_TYPE_ID VAN HET LEIDING EVENT VAN DIT JAAR    (- TEST_LEID)
    $eventtypesmeet         = array(2);                             //  EVENT_TYPE_ID VAN HET KAMPSTAF EVENT VAN DIT JAAR   (- KAMPSTAF)

    $eventtypesdeeltest     = array(102);
    $eventtypesleidtest     = array(101);
    $eventtypesdeeltoptest  = array(103);

    $eventtypesprod         = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesleid);
    $eventtypestest         = array_merge($eventtypesdeeltest,  $eventtypesdeeltoptest, $eventtypesleidtest);
    $eventtypesall          = array_merge($eventtypesprod,      $eventtypestest);

    $eventtypesdeelall      = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesdeeltest,    $eventtypesdeeltoptest);
    $eventtypesleidall      = array_merge($eventtypesleid,      $eventtypesleidtest,    $eventtypesmeet);

    ##########################################################################################
    ### DITEVENT_EVENTKAMP
    ##########################################################################################

    $params_var_ditevent        = NULL;
    $params_var_ditevent_note   = NULL;

    if ($ditevent_part_eventid) {
        $params_eventtype = [
            'checkPermissions' => FALSE,
            'debug'  => $apidebug,
            'select' => [
                'id',
                'start_date',
                'end_date',
                'event_type_id',
                'event_type_id:label',
            ],
            'where' => [
                ['id', '=', $ditevent_part_eventid],
            ],
        ];

        wachthond($extdebug,7, 'params_eventtype',          $params_eventtype);
        $result_eventtype = civicrm_api4('Event', 'get',    $params_eventtype);
        wachthond($extdebug,9, 'result_eventtype',          $result_eventtype);

        $ditevent_event_type_id     = $result_eventtype[0]['event_type_id']     ?? NULL; // bv.21
        $ditevent_event_start       = $result_eventtype[0]['start_date']        ?? NULL;
        $ditevent_fiscalyear        = curriculum_civicrm_fiscalyear($ditevent_event_start);
        $ditevent_fiscalyear_start  = $ditevent_fiscalyear['fiscalyear_start']  ?? NULL;
        $ditevent_fiscalyear_einde  = $ditevent_fiscalyear['fiscalyear_einde']  ?? NULL;

        wachthond($extdebug,2,  'ditevent_event_type_id',       $ditevent_event_type_id);
        wachthond($extdebug,2,  'ditevent_fiscalyear_start',    $ditevent_fiscalyear_start);
        wachthond($extdebug,2,  'ditevent_fiscalyear_einde',    $ditevent_fiscalyear_einde);
    }

    ##########################################################################################
    ### DITEVENT_EVENTKAMP (DEEL)       BRON: EID
    ##########################################################################################

    if (in_array($ditevent_event_type_id, $eventtypesdeel)) {
        $params_var_ditevent = [
            ['event_type_id',               'IN',   $eventtypesdeel],
            ['start_date',                  '>=',   $ditevent_fiscalyear_start], // maakt editen eerdere events mogelijk
            ['start_date',                  '<=',   $ditevent_fiscalyear_einde],                        
            ['id',                          '=',    $ditevent_part_eventid],     // eventid of specific kamp
            ['Event_Kenmerken.testevent',   '!=',   1],
        ];
        $params_var_ditevent_note = "DITEVENT DEEL [BRON: EID]";
    }

    ##########################################################################################
    ### DITEVENT_EVENTKAMP (LEID)
    ##########################################################################################
    if (in_array($ditevent_event_type_id, $eventtypesleid)) {

        ##########################################################################################
        ### ZOEK HET KAMP OP WAAR DEZE LEIDING ZICH VOOR HEEFT AANGEMELD
        ##########################################################################################

        if ($ditevent_part_id) {
            $params_welkkamp = [
                'checkPermissions'  => FALSE,
                'debug'             => $apidebug,
                'limit'             => 1,
                'select'            => [
                    'row_count',
                    'id', 
                    'contact_id',
                    'PART_LEID.Welk_kamp',
                    'PART_LEID.Functie',
                ],
                'join' => [
                    ['Event AS event', 'INNER', ['event_id', '=', 'event.id']],
                ],
                'where' => [
                    ['id', '=',  $ditevent_part_id],
                ],
            ];
    //      wachthond($extdebug,7, 'params_welkkamp',            $params_welkkamp);
            $result_welkkamp = civicrm_api4('Participant','get', $params_welkkamp);
    //      wachthond($extdebug,9, 'result_welkkamp',            $result_welkkamp);

            $ditevent_leid_welkkamp = $result_welkkamp[0]['PART_LEID.Welk_kamp']    ?? NULL;
            $ditevent_leid_functie  = $result_welkkamp[0]['PART_LEID.Functie']      ?? NULL;
        }

        ##########################################################################################
        ### DITEVENT_EVENTKAMP (LEID)       BRON: LEID_WELKKAMP + FISCALYEAR
        ##########################################################################################

        if (!in_array($ditevent_leid_functie, array('bestuurslid'))) {
            $params_var_ditevent = [
                ['event_type_id',               'IN',   $eventtypesdeel],
                ['start_date',                  '>=',   $ditevent_fiscalyear_start],
                ['start_date',                  '<=',   $ditevent_fiscalyear_einde],
                ['Event_Kenmerken.kampkort',    '=',    strtolower(trim($ditevent_leid_welkkamp))], // search for kampkort lowercase
                ['Event_Kenmerken.testevent',   '!=',   1],
            ];
            $params_var_ditevent_note = "DITEVENT LEID [BRON: LEID_WELKKAMP + FISCALYEAR]";
        }

        ##########################################################################################
        ### DITEVENT_EVENTKAMP (BESTUUR)    BRON: EID VAN LEIDINGEVENT
        ##########################################################################################

        if (in_array($ditevent_leid_functie, array('bestuurslid'))) {
            $params_var_ditevent = [
                ['event_type_id',               'IN',   $eventtypesleid],
                ['start_date',                  '>=',   $ditevent_fiscalyear_start],    
                ['start_date',                  '<=',   $ditevent_fiscalyear_einde],                        
                ['id',                          '=',    $ditevent_part_eventid],        // eventid of specific kamp
                ['Event_Kenmerken.testevent',   '!=',   1],
            ];
            $params_var_note = "PARAMS_VAR: DITEVENT BESTUUR [BRON: EID VAN (DIT) LEIDINGEVENT]";
        }
    }

    ##########################################################################################
    ### DITEVENT_EVENTKAMP (TESTDEEL) BRON: EID
    ##########################################################################################

    if (in_array($ditevent_event_type_id, $eventtypesdeeltest)) {
        $params_var_ditevent = [
            ['event_type_id',               'IN',   $eventtypesdeeltest],
            ['start_date',                  '>=',   $ditevent_fiscalyear_start],    // niet alleen in dit fiscale jaar
            ['start_date',                  '<=',   $ditevent_fiscalyear_einde],                        
            ['id',                          '=',    $ditevent_part_eventid],        // eventid of specific kamp
#           ['Event_Kenmerken.testevent',   '=',    1],
        ];
        $params_var_note = "PARAMS_VAR: DITEVENT DEEL TEST [BRON: EID]";
    }
    ##########################################################################################
    ### DITEVENT_EVENTKAMP (TESTLEID) BRON: EID
    ##########################################################################################

    if (in_array($ditevent_event_type_id, $eventtypesleidtest)) {       // Eventtype = LEID (zoek kamp waar leiding zich voor opgaf)
        $params_var_ditevent = [
            ['event_type_id',               'IN',   $eventtypesdeeltest],
            ['start_date',                  '>=',   $ditevent_fiscalyear_start],
            ['start_date',                  '<=',   $ditevent_fiscalyear_einde],
            ['Event_Kenmerken.kampkort',    '=', strtolower(trim($ditevent_leid_welkkamp))], // search for kampkort lowercase
#           ['Event_Kenmerken.testevent',   '=', 1],
        ];
        $params_var_ditevent_note = "PARAMS_VAR: DITEVENT LEID TEST [BRON: EID]";
    }    

        $params_var = $params_var_ditevent;

        wachthond($extdebug,2, 'params_var',                $params_var);
        wachthond($extdebug,2, 'params_var_ditevent_note',  $params_var_ditevent_note);

        if ($params_var) {
            $params_eventkamp = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
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

                    'event.Event_Kenmerken.Fietshuur',

                    'Event_Kenmerken.brengen_van',
                    'Event_Kenmerken.brengen_tot',
                    'Event_Kenmerken.pres_van',
                    'Event_Kenmerken.pres_tot',
                    'Event_Kenmerken.halen_van',
                    'Event_Kenmerken.halen_tot',

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

                    'Event_Kenmerken.Fietshuur',

                    'Event_Kenmerken.brengen',
                    'Event_Kenmerken.halen',
                    'Event_Kenmerken.afsluiting',

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

                    'Taken_rollen.hoofd_keuken',
                    'Taken_rollen.hoofd_keuken_1',
                    'Taken_rollen.hoofd_keuken_2',
                    'Taken_rollen.hoofd_keuken_3',

                    'Taken_rollen.hoofd_boekje',
                    'Taken_rollen.boekje_team_1',
                    'Taken_rollen.boekje_team_2',
                ],
                'where' => $params_var,
            ];

//          wachthond($extdebug,7, 'params_eventkamp',          $params_eventkamp);
            $result_eventkamp = civicrm_api4('Event', 'get',    $params_eventkamp);
//          wachthond($extdebug,9, 'result_eventkamp',          $result_eventkamp);

            $eventkamp_event_id                 = $result_eventkamp[0]['id']                                    ?? NULL;
            $eventkamp_event_type_id            = $result_eventkamp[0]['event_type_id']                         ?? NULL;
            $eventkamp_event_type_id_label      = $result_eventkamp[0]['event_type_id:label']                   ?? NULL;

            $eventkamp_kamptype_naam            = $result_eventkamp[0]['Event_Kenmerken.kamptype_naam']         ?? NULL;
            $eventkamp_kamptype_label           = $result_eventkamp[0]['Event_Kenmerken.kamptype_naam:label']   ?? NULL;
            $eventkamp_kamptype_id              = $result_eventkamp[0]['Event_Kenmerken.kamptype_id']           ?? NULL;
            $eventkamp_kampsoort                = $result_eventkamp[0]['Event_Kenmerken.kampsoort']             ?? NULL;

            $eventkamp_kampnaam                 = $result_eventkamp[0]['Event_Kenmerken.kampnaam']              ?? NULL;
            $eventkamp_kampkort                 = $result_eventkamp[0]['Event_Kenmerken.kampkort']              ?? NULL;

            $eventkamp_event_start              = $result_eventkamp[0]['start_date']                            ?? NULL;
            $eventkamp_event_einde              = $result_eventkamp[0]['end_date']                              ?? NULL;
            $eventkamp_event_weeknr             = $result_eventkamp[0]['Event_Kenmerken.kampweek_nr']           ?? NULL;

            $eventkamp_fiscalyear               = curriculum_civicrm_fiscalyear($eventkamp_event_start);
            $eventkamp_fiscalyear_start         = $eventkamp_fiscalyear['fiscalyear_start']                     ?? NULL;
            $eventkamp_fiscalyear_einde         = $eventkamp_fiscalyear['fiscalyear_einde']                     ?? NULL;

            $eventkamp_kampjaar                 = $eventkamp_fiscalyear['eventkamp_kampjaar']                   ?? NULL;
            $eventkamp_kampjaar_kort            = $eventkamp_fiscalyear['eventkamp_kampjaarkort']               ?? NULL;

            $eventkamp_kampjaar                 = date('Y', strtotime($eventkamp_event_start    ?? ''));
            $eventkamp_kampjaarkort             = date('y', strtotime($eventkamp_event_start    ?? ''));

            $eventkamp_plek                     = $result_eventkamp[0]['Event_Kenmerken.kamplocatie']           ?? NULL;
            $eventkamp_stad                     = $result_eventkamp[0]['Event_Kenmerken.kampplaats']            ?? NULL;
            $eventkamp_pleklang                 = $result_eventkamp[0]['Event_Kenmerken.kamplocatie:label']     ?? NULL;
            $eventkamp_stadlang                 = $result_eventkamp[0]['Event_Kenmerken.kampplaats:label']      ?? NULL;

            $eventkamp_fietsevent               = $result_eventkamp[0]['Event_Kenmerken.Fietshuur']             ?? NULL;

            $eventkamp_brengen_van              = $result_eventkamp[0]['Event_Kenmerken.brengen_van']           ?? NULL;
            $eventkamp_brengen_tot              = $result_eventkamp[0]['Event_Kenmerken.brengen_tot']           ?? NULL;
            $eventkamp_pres_van                 = $result_eventkamp[0]['Event_Kenmerken.pres_van']              ?? NULL;
            $eventkamp_pres_tot                 = $result_eventkamp[0]['Event_Kenmerken.pres_tot']              ?? NULL;
            $eventkamp_halen_van                = $result_eventkamp[0]['Event_Kenmerken.halen_van']             ?? NULL;
            $eventkamp_halen_tot                = $result_eventkamp[0]['Event_Kenmerken.halen_tot']             ?? NULL;

            $eventkamp_thema_naam               = $result_eventkamp[0]['Event_Kenmerken_Linkjes.thema_naam']    ?? NULL;
            $eventkamp_thema_info               = $result_eventkamp[0]['Event_Kenmerken_Linkjes.thema_info']    ?? NULL;
            $eventkamp_goeddoel_naam            = $result_eventkamp[0]['Event_Kenmerken_Linkjes.goeddoel_naam'] ?? NULL;
            $eventkamp_goeddoel_info            = $result_eventkamp[0]['Event_Kenmerken_Linkjes.goeddoel_info'] ?? NULL;
            $eventkamp_goeddoel_link            = $result_eventkamp[0]['Event_Kenmerken_Linkjes.goeddoel_link'] ?? NULL;

            $eventkamp_welkomvideo              = $result_eventkamp[0]['Event_Kenmerken_Linkjes.welkomvideo']   ?? NULL;
            $eventkamp_slotvideo                = $result_eventkamp[0]['Event_Kenmerken_Linkjes.slotvideo']     ?? NULL;
            $eventkamp_extrabagage              = $result_eventkamp[0]['Event_Kenmerken_Linkjes.extrabagage']   ?? NULL;
            $eventkamp_playlist                 = $result_eventkamp[0]['Event_Kenmerken_Linkjes.playlist']      ?? NULL;
            $eventkamp_doc_link                 = $result_eventkamp[0]['Event_Kenmerken_Linkjes.doc_link']      ?? NULL;
            $eventkamp_doc_info                 = $result_eventkamp[0]['Event_Kenmerken_Linkjes.doc_info']      ?? NULL;
            $eventkamp_foto_vraag               = $result_eventkamp[0]['Event_Kenmerken_Linkjes.foto_vraag']    ?? NULL;
            $eventkamp_foto_album               = $result_eventkamp[0]['Event_Kenmerken_Linkjes.foto_album']    ?? NULL;

            $eventkamp_event_hldn1_id           = $result_eventkamp[0]['Taken_rollen.hoofdleiding_1']           ?? NULL;
            $eventkamp_event_hldn2_id           = $result_eventkamp[0]['Taken_rollen.hoofdleiding_2']           ?? NULL;
            $eventkamp_event_hldn3_id           = $result_eventkamp[0]['Taken_rollen.hoofdleiding_3']           ?? NULL;

            $eventkamp_event_kern1_id           = $result_eventkamp[0]['Taken_rollen.kernteam_1']               ?? NULL;
            $eventkamp_event_kern2_id           = $result_eventkamp[0]['Taken_rollen.kernteam_2']               ?? NULL;
            $eventkamp_event_kern3_id           = $result_eventkamp[0]['Taken_rollen.kernteam_3']               ?? NULL;

            $eventkamp_event_keuken1_id         = $result_eventkamp[0]['Taken_rollen.hoofd_keuken']             ?? NULL;
            $eventkamp_event_keuken2_id         = $result_eventkamp[0]['Taken_rollen.hoofd_keuken_1']           ?? NULL;
            $eventkamp_event_keuken3_id         = $result_eventkamp[0]['Taken_rollen.hoofd_keuken_2']           ?? NULL;

            $eventkamp_event_gedrag1_id         = $result_eventkamp[0]['Taken_rollen.hoofd_gedrag']             ?? NULL;
            $eventkamp_event_gedrag2_id         = $result_eventkamp[0]['Taken_rollen.gedrag_team_1']            ?? NULL;
            $eventkamp_event_gedrag3_id         = $result_eventkamp[0]['Taken_rollen.gedrag_team_2']            ?? NULL;

            $eventkamp_event_boekje1_id         = $result_eventkamp[0]['Taken_rollen.hoofd_boekje']             ?? NULL;
            $eventkamp_event_boekje2_id         = $result_eventkamp[0]['Taken_rollen.boekje_team_1']            ?? NULL;
            $eventkamp_event_boekje3_id         = $result_eventkamp[0]['Taken_rollen.boekje_team_2']            ?? NULL;

            wachthond($extdebug,3, 'eventkamp_id',                  $eventkamp_event_id);
            wachthond($extdebug,3, 'eventkamp_type_id',             $eventkamp_event_type_id);
            wachthond($extdebug,3, 'eventkamp_type_id_label',       $eventkamp_event_type_id_label);

            wachthond($extdebug,3, 'eventkamp_kamptype_naam',       $eventkamp_kamptype_naam);
            wachthond($extdebug,3, 'eventkamp_kamptype_label',      $eventkamp_kamptype_label);
            wachthond($extdebug,3, 'eventkamp_kamptype_id',         $eventkamp_kamptype_id);
            wachthond($extdebug,3, 'eventkamp_kampsoort',           $eventkamp_kampsoort);

            wachthond($extdebug,3, 'eventkamp_kampnaam',            $eventkamp_kampnaam);
            wachthond($extdebug,3, 'eventkamp_kampkort',            $eventkamp_kampkort);

            wachthond($extdebug,3, 'eventkamp_event_start',         $eventkamp_event_start);
            wachthond($extdebug,3, 'eventkamp_event_einde',         $eventkamp_event_einde); 
            wachthond($extdebug,3, 'eventkamp_fiscalyear_start',    $eventkamp_fiscalyear_start);
            wachthond($extdebug,3, 'eventkamp_fiscalyear_einde',    $eventkamp_fiscalyear_einde);

            wachthond($extdebug,3, 'eventkamp_kampjaar',            $eventkamp_kampjaar);
            wachthond($extdebug,3, 'eventkamp_kampjaarkort',        $eventkamp_kampjaarkort);

            wachthond($extdebug,3, 'eventkamp_pleklang',            $eventkamp_pleklang);
            wachthond($extdebug,3, 'eventkamp_stadlang',            $eventkamp_stadlang);

            wachthond($extdebug,3, 'eventkamp_fietsevent',          $eventkamp_fietsevent);

            wachthond($extdebug,3, 'eventkamp_brengen_van',         $eventkamp_brengen_van);   
            wachthond($extdebug,3, 'eventkamp_brengen_tot',         $eventkamp_brengen_tot);
            wachthond($extdebug,3, 'eventkamp_pres_van',            $eventkamp_pres_van);   
            wachthond($extdebug,3, 'eventkamp_pres_tot',            $eventkamp_pres_tot);
            wachthond($extdebug,3, 'eventkamp_halen_van',           $eventkamp_halen_van);   
            wachthond($extdebug,3, 'eventkamp_halen_tot',           $eventkamp_halen_tot);

            wachthond($extdebug,3, 'eventkamp_brengen',             $eventkamp_brengen);
            wachthond($extdebug,3, 'eventkamp_halen',               $eventkamp_halen);
            wachthond($extdebug,3, 'eventkamp_afsluiting',          $eventkamp_afsluiting);

            wachthond($extdebug,3, 'eventkamp_thema_naam',          $eventkamp_thema_naam);
            wachthond($extdebug,3, 'eventkamp_thema_info',          $eventkamp_thema_info);
            wachthond($extdebug,3, 'eventkamp_goeddoel_naam',       $eventkamp_goeddoel_naam);
            wachthond($extdebug,3, 'eventkamp_goeddoel_info',       $eventkamp_goeddoel_info);
            wachthond($extdebug,3, 'eventkamp_goeddoel_link',       $eventkamp_goeddoel_link);

            wachthond($extdebug,3, 'eventkamp_welkomvideo',         $eventkamp_welkomvideo);
            wachthond($extdebug,3, 'eventkamp_slotvideo',           $eventkamp_slotvideo);
            wachthond($extdebug,3, 'eventkamp_extrabagage',         $eventkamp_extrabagage);
            wachthond($extdebug,3, 'eventkamp_playlist',            $eventkamp_playlist);
            wachthond($extdebug,3, 'eventkamp_doc_link',            $eventkamp_doc_link);
            wachthond($extdebug,3, 'eventkamp_doc_info',            $eventkamp_doc_info);
            wachthond($extdebug,3, 'eventkamp_foto_vraag',          $eventkamp_foto_vraag);
            wachthond($extdebug,3, 'eventkamp_foto_album',          $eventkamp_foto_album);

            wachthond($extdebug,3, 'eventkamp_hoofdleid1_id',       $eventkamp_event_hldn1_id);
            wachthond($extdebug,3, 'eventkamp_hoofdleid2_id',       $eventkamp_event_hldn2_id);
            wachthond($extdebug,3, 'eventkamp_hoofdleid3_id',       $eventkamp_event_hldn3_id);

            wachthond($extdebug,3, 'eventkamp_kernteam1_id',        $eventkamp_event_kern1_id);
            wachthond($extdebug,3, 'eventkamp_kernteam2_id',        $eventkamp_event_kern2_id);
            wachthond($extdebug,3, 'eventkamp_kernteam3_id',        $eventkamp_event_kern3_id);

            wachthond($extdebug,3, 'eventkamp_hoofd_gedrag1_id',    $eventkamp_event_gedrag1_id);
            wachthond($extdebug,3, 'eventkamp_hoofd_gedrag2_id',    $eventkamp_event_gedrag2_id);
            wachthond($extdebug,3, 'eventkamp_hoofd_gedrag3_id',    $eventkamp_event_gedrag3_id);

            wachthond($extdebug,3, 'eventkamp_hoofd_keuken1_id',    $eventkamp_event_keuken1_id);
            wachthond($extdebug,3, 'eventkamp_hoofd_keuken2_id',    $eventkamp_event_keuken2_id);
            wachthond($extdebug,3, 'eventkamp_hoofd_keuken3_id',    $eventkamp_event_keuken3_id);

            wachthond($extdebug,3, 'eventkamp_hoofd_boekje1_id',    $eventkamp_event_boekje1_id);
            wachthond($extdebug,3, 'eventkamp_hoofd_boekje2_id',    $eventkamp_event_boekje2_id);
            wachthond($extdebug,3, 'eventkamp_hoofd_boekje3_id',    $eventkamp_event_boekje3_id);

            $eventinfo_array = array(

                'eventkamp_event_id'                => $eventkamp_event_id,
                'eventkamp_event_type_id'           => $eventkamp_event_type_id,
                'eventkamp_event_type_id_label'     => $eventkamp_event_type_id_label,

                'eventkamp_kamptype_naam'           => $eventkamp_kamptype_naam,
                'eventkamp_kamptype_label'          => $eventkamp_kamptype_label,
                'eventkamp_kamptype_id'             => $eventkamp_kamptype_id,
                'eventkamp_kampsoort'               => $eventkamp_kampsoort,

                'eventkamp_kampnaam'                => $eventkamp_kampnaam,
                'eventkamp_kampkort'                => $eventkamp_kampkort,

                'eventkamp_event_start'             => $eventkamp_event_start,
                'eventkamp_event_einde'             => $eventkamp_event_einde,
                'eventkamp_event_weeknr'            => $eventkamp_event_weeknr,
                'eventkamp_fiscalyear_start'        => $eventkamp_fiscalyear_start,
                'eventkamp_fiscalyear_einde'        => $eventkamp_fiscalyear_einde,

                'eventkamp_kampjaar'                => $eventkamp_kampjaar,
                'eventkamp_kampjaarkort'            => $eventkamp_kampjaarkort,

                'eventkamp_plek'                    => $eventkamp_plek,
                'eventkamp_stad'                    => $eventkamp_stad,
                'eventkamp_pleklang'                => $eventkamp_pleklang,
                'eventkamp_stadlang'                => $eventkamp_stadlang,

                'eventkamp_fietsevent'              => $eventkamp_fietsevent,

                'eventkamp_brengen_van'             => $eventkamp_brengen_van,
                'eventkamp_brengen_tot'             => $eventkamp_brengen_tot,
                'eventkamp_pres_van'                => $eventkamp_pres_van,
                'eventkamp_pres_tot'                => $eventkamp_pres_tot,
                'eventkamp_halen_van'               => $eventkamp_halen_van,
                'eventkamp_halen_tot'               => $eventkamp_halen_tot,

                'eventkamp_thema_naam'              => $eventkamp_thema_naam,
                'eventkamp_thema_info'              => $eventkamp_thema_info,
                'eventkamp_goeddoel_naam'           => $eventkamp_goeddoel_naam,
                'eventkamp_goeddoel_info'           => $eventkamp_goeddoel_info,
                'eventkamp_goeddoel_link'           => $eventkamp_goeddoel_link,

                'eventkamp_welkomvideo'             => $eventkamp_welkomvideo,
                'eventkamp_slotvideo'               => $eventkamp_slotvideo,
                'eventkamp_extrabagage'             => $eventkamp_extrabagage,
                'eventkamp_playlist'                => $eventkamp_playlist,
                'eventkamp_doc_link'                => $eventkamp_doc_link,
                'eventkamp_doc_info'                => $eventkamp_doc_info,
                'eventkamp_foto_vraag'              => $eventkamp_foto_vraag,
                'eventkamp_foto_album'              => $eventkamp_foto_album,

                'eventkamp_event_hldn1_id'          => $eventkamp_event_hldn1_id,
                'eventkamp_event_hldn2_id'          => $eventkamp_event_hldn2_id,
                'eventkamp_event_hldn3_id'          => $eventkamp_event_hldn3_id,

                'eventkamp_event_kern1_id'          => $eventkamp_event_kern1_id,
                'eventkamp_event_kern2_id'          => $eventkamp_event_kern2_id,
                'eventkamp_event_kern3_id'          => $eventkamp_event_kern3_id,

                'eventkamp_event_gedrag1_id'        => $eventkamp_event_gedrag1_id,
                'eventkamp_event_gedrag2_id'        => $eventkamp_event_gedrag2_id,
                'eventkamp_event_gedrag3_id'        => $eventkamp_event_gedrag3_id,

                'eventkamp_event_keuken1_id'        => $eventkamp_event_keuken1_id,
                'eventkamp_event_keuken2_id'        => $eventkamp_event_keuken2_id,
                'eventkamp_event_keuken3_id'        => $eventkamp_event_keuken3_id,

                'eventkamp_event_boekje1_id'        => $eventkamp_event_boekje1_id,
                'eventkamp_event_boekje2_id'        => $eventkamp_event_boekje2_id,
                'eventkamp_event_boekje3_id'        => $eventkamp_event_boekje3_id,
            );
            return $eventinfo_array;   
        }

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

function base_find_allpart($contactid, $refdate) {

    if (empty($contactid)) {
        return false;
    } else {
        $contact_id = $contactid;
    }

    $extdebug = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug = FALSE;

    if (empty($refdate)) {
        $refdate    = date("Y-m-d H:i:s");
    }

    $refyear                    = date('Y', strtotime($refdate));

    wachthond($extdebug,3, 'refdate',       $refdate);
    wachthond($extdebug,3, 'refyear',       $refyear);

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "### BASE - VIND ALLE REGISTRATIES VOOR $refyear",   "[base_find_allpart]");
    wachthond($extdebug,3, "########################################################################");

    $ditevent_event_start       = $refdate;

    $ditevent_fiscalyear        = curriculum_civicrm_fiscalyear($ditevent_event_start);
    $ditevent_fiscalyear_start  = $ditevent_fiscalyear['fiscalyear_start']  ?? NULL;
    $ditevent_fiscalyear_einde  = $ditevent_fiscalyear['fiscalyear_einde']  ?? NULL;    

    $eventtypesdeel         = array(11,12,13,14,21,22,23,24,33);    //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesdeeltop      = array(33);                            //  EVENT_TYPE_ID'S VAN DE KAMPEN VAN DIT JAAR          (- TEST_DEEL)
    $eventtypesleid         = array(1);                             //  EVENT_TYPE_ID VAN HET LEIDING EVENT VAN DIT JAAR    (- TEST_LEID)
    $eventtypesmeet         = array(2);                             //  EVENT_TYPE_ID VAN HET KAMPSTAF EVENT VAN DIT JAAR   (- KAMPSTAF)

    $eventtypesdeeltest     = array(102);
    $eventtypesleidtest     = array(101);
    $eventtypesdeeltoptest  = array(103);

    $eventtypesprod         = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesleid);
    $eventtypestest         = array_merge($eventtypesdeeltest,  $eventtypesdeeltoptest, $eventtypesleidtest);
    $eventtypesall          = array_merge($eventtypesprod,      $eventtypestest);

    $eventtypesdeelall      = array_merge($eventtypesdeel,      $eventtypesdeeltop,     $eventtypesdeeltest,    $eventtypesdeeltoptest);
    $eventtypesleidall      = array_merge($eventtypesleid,      $eventtypesleidtest,    $eventtypesmeet);

        $params_allpart = [
            'checkPermissions'  => FALSE,
            'debug'             => $apidebug,
            'orderBy' => [
                'event_id' => 'ASC',
            ],
            'select'    => [
                'row_count',
                'id', 
                'contact_id',
                'contact_id.first_name',
                'contact_id.display_name',
                'status_id',
                'status_id:name',
                'role_id', 
                'register_date',
                'event_id',                     // event_id stored in participant table

                'event.id',                     // event_id stored in joined event table
                'event.title',
                'event.event_type_id',          // integer                 bv. 21
                'event.event_type_id:label',    // zelfde als naam event:  bv. Kinderkamp week 2
                'event.start_date',
                'event.end_date',

                'PART.PART_kamplang',
                'PART.PART_kampkort',
                'PART.PART_kamptype_naam',
                'PART.PART_kamptype_id',
                'PART.PART_kampfunctie',
                'PART.PART_kamprol',
                'PART_LEID.Welk_kamp',
                'PART_LEID.Functie',
            ],
            'join' => [
                ['Event AS event', 'INNER', ['event_id', '=', 'event.id']],
            ],
            'where' => [
                ['contact_id',              '=',    $contact_id],
//              ['event.event_type_id',     'IN',   $eventtypesprod],
                ['event.event_type_id',     'IN',   $eventtypesall],
                ['event.start_date',        '>=',   $ditevent_fiscalyear_start],
                ['event.start_date',        '<',    $ditevent_fiscalyear_einde],
            ], 
        ];

//      wachthond($extdebug,7, 'params_allpart',            $params_allpart);
        $result_allpart = civicrm_api4('Participant','get', $params_allpart);
//      wachthond($extdebug,7, 'result_allpart',            $result_allpart);

        $displayname    = $result_allpart[0]['contact_id.display_name'] ?? NULL;

        if (!Civi::cache()->get('cache_status_positive')) {
            find_partstatus();
        }

        $status_positive        = Civi::cache()->get('cache_status_positive');
        $status_waiting         = Civi::cache()->get('cache_status_waiting');
        $status_pending         = Civi::cache()->get('cache_status_pending');
        $status_negative        = Civi::cache()->get('cache_status_negative');

        wachthond($extdebug,4, 'statusids_positive',    $status_positive);
        wachthond($extdebug,4, 'statusids_waiting',     $status_waiting);
        wachthond($extdebug,4, 'statusids_pending',     $status_pending);
        wachthond($extdebug,4, 'statusids_negative',    $status_negative);        

        if ($result_allpart) {
            $result_allpart_count  = $result_allpart->countMatched();
        } else {
            wachthond($extdebug,2, "DITEVENTJAAR_ALL : GEEN PARTICIPANT RECORDS GEVONDEN", "NOT! [cid: $contact_id]");
        }
        if ($result_allpart_count  > 1) {
            wachthond($extdebug,2, "DITEVENTJAAR_ALL : $result_allpart_count PARTICIPANT RECORDS GEVONDEN", "[cid: $contact_id]");
        }
        if ($result_allpart_count == 1) {
            wachthond($extdebug,2, "DITEVENTJAAR_ALL : EXACT 1 PARTICIPANT RECORDS GEVONDEN", "[cid: $contact_id]");
        }        

        $indexed        = [];

        $pending_all    = [];
        $waiting_all    = [];
        $negativ_all    = [];        

        $positiv_all    = [];
        $positiv_deel   = [];
        $positiv_leid   = [];

        $allstat_all    = [];
        $allstat_deel   = [];
        $allstat_leid   = [];

        foreach($result_allpart as $i=>$item) {
/*
            // M61: DIT STAAT HIER OMDAT DE STATUS BIJ REG VAAK 0 IS OP DIT MOMENT
            if ($item['status_id'] == 0) {
                $item['status_id']          = 1;
                $item['status_id:name']     = 'Registered';
            }
*/
            if ( !isset($indexed[$i][$item['id']]) ) {
                $indexed[$i]['key']             = $i;
                $indexed[$i]['contact_id']      = $item['contact_id'];
                $indexed[$i]['id']              = $item['id'];
                $indexed[$i]['event.id']        = $item['event.id'];
                $indexed[$i]['event_id']        = $item['event_id'];
                $indexed[$i]['event_type_id']   = $item['event.event_type_id'];
                $indexed[$i]['status_id']       = $item['status_id'];
                $indexed[$i]['status_name']     = $item['status_id:name'];
                $indexed[$i]['event_title']     = $item['event.title'];
                $indexed[$i]['kampnaam']        = $item['PART.PART_kamplang'];
                $indexed[$i]['kampkort']        = $item['PART.PART_kampkort'];
                $indexed[$i]['kamprol']         = $item['PART.PART_kamprol'];
                $indexed[$i]['kampfunctie']     = $item['PART.PART_kampfunctie'];
            }
            if (!isset($pending_all[$i])  AND in_array($item['status_id'],$status_pending))   {
                $pending_all[]  = $i;
            }
            if (!isset($waiting_all[$i])  AND in_array($item['status_id'],$status_waiting))   {
                $waiting_all[]  = $i;
            }
            if (!isset($negativ_all[$i])  AND in_array($item['status_id'],$status_negative))   {
                $negativ_all[]  = $i;
            }

            if (!isset($positiv_all[$i])  AND in_array($item['status_id'],$status_positive) AND in_array($item['event.event_type_id'],$eventtypesprod)){
                $positiv_all[]  = $i;
            }
            if (!isset($positiv_deel[$i]) AND in_array($item['status_id'],$status_positive) AND in_array($item['event.event_type_id'],$eventtypesdeel)){
                $positiv_deel[] = $i;
            }
            if (!isset($positiv_leid[$i]) AND in_array($item['status_id'],$status_positive) AND in_array($item['event.event_type_id'],$eventtypesleid)){
                $positiv_leid[] = $i;
            }

            if (!isset($allstat_all[$i])  AND in_array($item['event.event_type_id'],   $eventtypesprod))    {
                $allstat_all[]  = $i;
            }
            if (!isset($allstat_deel[$i]) AND in_array($item['event.event_type_id'],   $eventtypesdeel))    {
                $allstat_deel[] = $i;
            }
            if (!isset($allstat_top[$i])  AND in_array($item['event.event_type_id'],   $eventtypesdeeltop)) {
                $allstat_top[]  = $i;
            }
            if (!isset($allstat_leid[$i]) AND in_array($item['event.event_type_id'],   $eventtypesleid))    {
                $allstat_leid[] = $i;
            }
        }

        wachthond($extdebug,2, 'result_indexed',        $indexed);

        wachthond($extdebug,3, 'result_pending_all',    $pending_all);
        wachthond($extdebug,3, 'result_waiting_all',    $waiting_all);
        wachthond($extdebug,3, 'result_negativ_all',    $negativ_all);

        wachthond($extdebug,3, 'result_positiv_all',    $positiv_all);
        wachthond($extdebug,3, 'result_positiv_deel',   $positiv_deel);
        wachthond($extdebug,3, 'result_positiv_leid',   $positiv_leid);

        wachthond($extdebug,3, 'result_allstat_all',    $allstat_all);
        wachthond($extdebug,3, 'result_allstat_deel',   $allstat_deel);
        wachthond($extdebug,3, 'result_allstat_top',    $allstat_top);
        wachthond($extdebug,3, 'result_allstat_leid',   $allstat_leid);

        wachthond($extdebug,2, "########################################################################");
        $penkey_all                                                 = $pending_all[0]   ?? NULL;
        $result_allpart_pen_count                                   = count($pending_all);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_pen_count REGISTRATION WITH STATUS: PENDING [ALL] ","[KEY: $penkey_all]");
        wachthond($extdebug,3, "########################################################################");
        $waitkey_all                                                 = $waiting_all[0]  ?? NULL;
        $result_allpart_wait_count                                   = count($waiting_all);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_wait_count REGISTRATION WITH STATUS: WAITING [ALL] ","[KEY: $waitkey_all]");
        wachthond($extdebug,3, "########################################################################");
        $negkey_all                                                 = $negativ_all[0]   ?? NULL;
        $result_allpart_neg_count                                   = count($negativ_all);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_neg_count REGISTRATION WITH STATUS: NEGATIV [ALL] ","[KEY: $negkey_all]");
        wachthond($extdebug,3, "########################################################################");
        $poskey_all                                                 = $positiv_all[0]   ?? NULL;
        $result_allpart_pos_count                                   = count($positiv_all);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_pos_count REGISTRATION WITH STATUS: POSITIV [ALL] ","[KEY: $poskey_all]");
        wachthond($extdebug,3, "########################################################################");
        $poskey_deel                                                = $positiv_deel[0]  ?? NULL;
        $result_allpart_pos_deel_count                              = count($positiv_deel);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_pos_deel_count REGISTRATION WITH STATUS: POSITIV [DEEL]","[KEY: $poskey_deel]");
        wachthond($extdebug,3, "########################################################################");
        $poskey_leid                                                = $positiv_leid[0]  ?? NULL;
        $result_allpart_pos_leid_count                              = count($positiv_leid);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_pos_leid_count REGISTRATION WITH STATUS: POSITIV [LEID]","[KEY: $poskey_leid]");
        wachthond($extdebug,3, "########################################################################");
        $onekey_all                                                 = $allstat_all[0]   ?? NULL;
        $result_allpart_all_count                                   = count($allstat_all);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_all_count REGISTRATION WITH STATUS: [ONE]","[KEY: $onekey_all]");
        wachthond($extdebug,3, "########################################################################");
        $onekey_deel                                                = $allstat_deel[0]  ?? NULL;
        $result_allpart_all_deel_count                              = count($allstat_deel);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_all_deel_count REGISTRATION WITH STATUS: [ONE DEEL]","[KEY: $onekey_deel]");
        wachthond($extdebug,3, "########################################################################");
        $onekey_leid                                                = $allstat_leid[0]  ?? NULL;
        $result_allpart_all_leid_count                              = count($allstat_leid);
        wachthond($extdebug,3, "DIT EVENTJAAR $result_allpart_all_leid_count REGISTRATION WITH STATUS: [ONE LEID]","[KEY: $onekey_leid]");
        wachthond($extdebug,3, "########################################################################");

        wachthond($extdebug,3, 'result_allpart_pen_count',          $result_allpart_pen_count);
        wachthond($extdebug,3, 'result_allpart_wait_count',         $result_allpart_wait_count);
        wachthond($extdebug,3, 'result_allpart_neg_count',          $result_allpart_neg_count);

        wachthond($extdebug,3, 'result_allpart_pos_count',          $result_allpart_pos_count);
        wachthond($extdebug,3, 'result_allpart_pos_deel_count',     $result_allpart_pos_deel_count);
        wachthond($extdebug,3, 'result_allpart_pos_leid_count',     $result_allpart_pos_leid_count);

        wachthond($extdebug,3, 'result_allpart_all_count',          $result_allpart_all_count);
        wachthond($extdebug,3, 'result_allpart_all_deel_count',     $result_allpart_all_deel_count);
        wachthond($extdebug,3, 'result_allpart_all_leid_count',     $result_allpart_all_leid_count);                

//      if ($result_allpart_wait_count       == 1) { ### 1 WAIT ALL
            $diteventjaar_wait_part_id               = $result_allpart[$waitkey_all]['id']                      ?? NULL;
            $diteventjaar_wait_event_id              = $result_allpart[$waitkey_all]['event.id']                ?? NULL;
            $diteventjaar_wait_event_type_id         = $result_allpart[$waitkey_all]['event.event_type_id']     ?? NULL;
            $diteventjaar_wait_status_id             = $result_allpart[$waitkey_all]['status_id']               ?? NULL;
            $diteventjaar_wait_kampfunctie           = $result_allpart[$waitkey_all]['PART.PART_kampfunctie']   ?? NULL;
            $diteventjaar_wait_kampkort              = $result_allpart[$waitkey_all]['PART.PART_kampkort']      ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: UIT $result_allpart_all_count PARTICIPANTS $result_allpart_wait_count DEELNAME WACHTLIJST GEVONDEN [D/L]","$diteventjaar_pen_part_id ($diteventjaar_pen_kampkort $refyear)");
//      }

//      if ($result_allpart_pen_count       == 1) { ### 1 PEN ALL
            $diteventjaar_pen_part_id               = $result_allpart[$penkey_all]['id']                        ?? NULL;
            $diteventjaar_pen_event_id              = $result_allpart[$penkey_all]['event.id']                  ?? NULL;
            $diteventjaar_pen_event_type_id         = $result_allpart[$penkey_all]['event.event_type_id']       ?? NULL;
            $diteventjaar_pen_status_id             = $result_allpart[$penkey_all]['status_id']                 ?? NULL;
            $diteventjaar_pen_kampfunctie           = $result_allpart[$penkey_all]['PART.PART_kampfunctie']     ?? NULL;
            $diteventjaar_pen_kampkort              = $result_allpart[$penkey_all]['PART.PART_kampkort']        ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: UIT $result_allpart_all_count PARTICIPANTS $result_allpart_pen_count DEELNAME PENDING GEVONDEN [D/L]","$diteventjaar_pen_part_id ($diteventjaar_pen_kampkort $refyear)");
//      }

        if ($result_allpart_pos_count       == 1) { ### 1 POS ALL
            $diteventjaar_pos_part_id               = $result_allpart[$poskey_all]['id']                        ?? NULL;
            $diteventjaar_pos_event_id              = $result_allpart[$poskey_all]['event.id']                  ?? NULL;
            $diteventjaar_pos_event_type_id         = $result_allpart[$poskey_all]['event.event_type_id']       ?? NULL;
            $diteventjaar_pos_status_id             = $result_allpart[$poskey_all]['status_id']                 ?? NULL;
            $diteventjaar_pos_kamprol               = $result_allpart[$poskey_all]['PART.PART_kamprol']         ?? NULL;
            $diteventjaar_pos_kampfunctie           = $result_allpart[$poskey_all]['PART.PART_kampfunctie']     ?? NULL;
            $diteventjaar_pos_kampkort              = $result_allpart[$poskey_all]['PART.PART_kampkort']        ?? NULL;
            $diteventjaar_pos_event_start           = $result_allpart[$poskey_all]['event.start_date']          ?? NULL;
            $diteventjaar_pos_event_einde           = $result_allpart[$poskey_all]['event.end_date']            ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: UIT $result_allpart_all_count PARTICIPANTS $result_allpart_pos_count POSITIEVE DEELNAME GEVONDEN [D/L]","$diteventjaar_pos_part_id ($diteventjaar_pos_kampkort $refyear)");
        }
        if ($result_allpart_pos_deel_count  == 1) { ### 1 POS DEEL
            $diteventjaar_pos_deel_part_id          = $result_allpart[$poskey_deel]['id']                       ?? NULL;
            $diteventjaar_pos_deel_event_id         = $result_allpart[$poskey_deel]['event.id']                 ?? NULL;
            $diteventjaar_pos_deel_event_type_id    = $result_allpart[$poskey_deel]['event.event_type_id']      ?? NULL;
            $diteventjaar_pos_deel_status_id        = $result_allpart[$poskey_deel]['status_id']                ?? NULL;
            $diteventjaar_pos_deel_kampfunctie      = $result_allpart[$poskey_deel]['PART.PART_kampfunctie']    ?? NULL;
            $diteventjaar_pos_deel_kampkort         = $result_allpart[$poskey_deel]['PART.PART_kampkort']       ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: UIT $result_allpart_all_count PARTICIPANTS $result_allpart_pos_deel_count POSITIEVE DEELNAME GEVONDEN [DEEL]","$diteventjaar_pos_deel_part_id ($diteventjaar_pos_deel_kampkort $refyear)");
        }
        if ($result_allpart_pos_leid_count  == 1) { ### 1 POS LEID
            $diteventjaar_pos_leid_part_id          = $result_allpart[$poskey_leid]['id']                       ?? NULL;
            $diteventjaar_pos_leid_event_id         = $result_allpart[$poskey_leid]['event.id']                 ?? NULL;
            $diteventjaar_pos_leid_event_type_id    = $result_allpart[$poskey_leid]['event.event_type_id']      ?? NULL;
            $diteventjaar_pos_leid_status_id        = $result_allpart[$poskey_leid]['status_id']                ?? NULL;
            $diteventjaar_pos_leid_kampfunctie      = $result_allpart[$poskey_leid]['PART.PART_kampfunctie']    ?? NULL;
            $diteventjaar_pos_leid_kampkort         = $result_allpart[$poskey_leid]['PART.PART_kampkort']       ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: UIT $result_allpart_all_count PARTICIPANTS $result_allpart_pos_leid_count POSITIEVE DEELNAME [LEID] RECORD GEVONDEN","$diteventjaar_pos_leid_part_id ($diteventjaar_pos_leid_kampkort $refyear)");
        }

        if ($result_allpart_all_count       == 1) { ### 1 ONE ALL
            $diteventjaar_one_part_id               = $result_allpart[$onekey_all]['id']                        ?? NULL;
            $diteventjaar_one_event_id              = $result_allpart[$onekey_all]['event.id']                  ?? NULL;
            $diteventjaar_one_event_type_id         = $result_allpart[$onekey_all]['event.event_type_id']       ?? NULL;
            $diteventjaar_one_status_id             = $result_allpart[$onekey_all]['status_id']                 ?? NULL;
            $diteventjaar_one_kamprol               = $result_allpart[$onekey_all]['PART.PART_kamprol']         ?? NULL;
            $diteventjaar_one_kampfunctie           = $result_allpart[$onekey_all]['PART.PART_kampfunctie']     ?? NULL;
            $diteventjaar_one_kampkort              = $result_allpart[$onekey_all]['PART.PART_kampkort']        ?? NULL;
            $diteventjaar_one_event_start           = $result_allpart[$onekey_all]['event.start_date']          ?? NULL;
            $diteventjaar_one_event_einde           = $result_allpart[$onekey_all]['event.end_date']            ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: ER IS PRECIES ONE DEELNAME RECORD GEVONDEN [D/L]",
                                    "$diteventjaar_one_part_id ($diteventjaar_one_kampkort $refyear)");
        }
        if ($result_allpart_all_deel_count  == 1) { ### 1 ONE DEEL
            $diteventjaar_one_deel_part_id          = $result_allpart[$onekey_deel]['id']                       ?? NULL;
            $diteventjaar_one_deel_event_id         = $result_allpart[$onekey_deel]['event.id']                 ?? NULL;
            $diteventjaar_one_deel_event_type_id    = $result_allpart[$onekey_deel]['event.event_type_id']      ?? NULL;
            $diteventjaar_one_deel_status_id        = $result_allpart[$onekey_deel]['status_id']                ?? NULL;
            $diteventjaar_one_deel_kampfunctie      = $result_allpart[$onekey_deel]['PART.PART_kampfunctie']    ?? NULL;
            $diteventjaar_one_deel_kampkort         = $result_allpart[$onekey_deel]['PART.PART_kampkort']       ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: ER IS PRECIES ONE DEELNAME RECORD GEVONDEN [DEEL]",
                                    "$diteventjaar_one_deel_part_id ($diteventjaar_one_deel_kampkort $refyear)");
        }
        if ($result_allpart_all_leid_count  == 1) { ### 1 ONE LEID
            $diteventjaar_one_leid_part_id          = $result_allpart[$onekey_leid]['id']                       ?? NULL;
            $diteventjaar_one_leid_event_id         = $result_allpart[$onekey_leid]['event.id']                 ?? NULL;
            $diteventjaar_one_leid_event_type_id    = $result_allpart[$onekey_leid]['event.event_type_id']      ?? NULL;
            $diteventjaar_one_leid_status_id        = $result_allpart[$onekey_leid]['status_id']                ?? NULL;
            $diteventjaar_one_leid_kampfunctie      = $result_allpart[$onekey_leid]['PART.PART_kampfunctie']    ?? NULL;
            $diteventjaar_one_leid_kampkort         = $result_allpart[$onekey_leid]['PART.PART_kampkort']       ?? NULL;
            wachthond($extdebug,2,  "DIT EVENTJAAR: ER IS PRECIES ONE DEELNAME RECORD GEVONDEN [LEID]",
                                    "$diteventjaar_one_leid_part_id ($diteventjaar_one_leid_kampkort $refyear)");
        }

        $eventjaar_array = array(
            'contact_id'                                => $contact_id,
            'displayname'                               => $displayname,

            'refdate'                                   => $refdate,
            'refyear'                                   => $refyear,            

            'result_allpart_pen_count'                  => $result_allpart_pen_count,
            'result_allpart_wait_count'                 => $result_allpart_wait_count,
            'result_allpart_neg_count'                  => $result_allpart_neg_count,

            'result_allpart_pos_count'                  => $result_allpart_pos_count,
            'result_allpart_pos_deel_count'             => $result_allpart_pos_deel_count,
            'result_allpart_pos_leid_count'             => $result_allpart_pos_leid_count,

            'result_allpart_all_count'                  => $result_allpart_all_count,
            'result_allpart_all_deel_count'             => $result_allpart_all_deel_count,
            'result_allpart_all_leid_count'             => $result_allpart_all_leid_count,

            'result_allpart_one_part_id'                => $diteventjaar_one_part_id,
            'result_allpart_one_deel_part_id'           => $diteventjaar_one_deel_part_id,
            'result_allpart_one_leid_part_id'           => $diteventjaar_one_leid_part_id,

            'result_allpart_one_event_id'               => $diteventjaar_one_event_id,
            'result_allpart_one_deel_event_id'          => $diteventjaar_one_deel_event_id,
            'result_allpart_one_leid_event_id'          => $diteventjaar_one_leid_event_id,

            'result_allpart_one_event_type_id'          => $diteventjaar_one_event_type_id,
            'result_allpart_one_deel_event_type_id'     => $diteventjaar_one_deel_event_type_id,
            'result_allpart_one_leid_event_type_id'     => $diteventjaar_one_leid_event_type_id,

            'result_allpart_one_status_id'              => $diteventjaar_one_status_id,
            'result_allpart_one_deel_status_id'         => $diteventjaar_one_deel_status_id,
            'result_allpart_one_leid_status_id'         => $diteventjaar_one_leid_status_id,

            'result_allpart_one_kamprol'                => $diteventjaar_one_kamprol,
            'result_allpart_one_event_start'            => $diteventjaar_one_event_start,
            'result_allpart_one_event_einde'            => $diteventjaar_one_event_einde,

            'result_allpart_one_kampfunctie'            => $diteventjaar_one_kampfunctie,
            'result_allpart_one_deel_kampfunctie'       => $diteventjaar_one_deel_kampfunctie,
            'result_allpart_one_leid_kampfunctie'       => $diteventjaar_one_leid_kampfunctie,

            'result_allpart_one_kampkort'               => $diteventjaar_one_kampkort,
            'result_allpart_one_deel_kampkort'          => $diteventjaar_one_deel_kampkort,
            'result_allpart_one_leid_kampkort'          => $diteventjaar_one_leid_kampkort,

            'result_allpart_pos_part_id'                => $diteventjaar_pos_part_id,
            'result_allpart_pos_deel_part_id'           => $diteventjaar_pos_deel_part_id,
            'result_allpart_pos_leid_part_id'           => $diteventjaar_pos_leid_part_id,

            'result_allpart_pos_event_id'               => $diteventjaar_pos_event_id,
            'result_allpart_pos_deel_event_id'          => $diteventjaar_pos_deel_event_id,
            'result_allpart_pos_leid_event_id'          => $diteventjaar_pos_leid_event_id,

            'result_allpart_pos_event_type_id'          => $diteventjaar_pos_event_type_id,
            'result_allpart_pos_deel_event_type_id'     => $diteventjaar_pos_deel_event_type_id,
            'result_allpart_pos_leid_event_type_id'     => $diteventjaar_pos_leid_event_type_id,

            'result_allpart_pos_status_id'              => $diteventjaar_pos_status_id,
            'result_allpart_pos_deel_status_id'         => $diteventjaar_pos_deel_status_id,
            'result_allpart_pos_leid_status_id'         => $diteventjaar_pos_leid_status_id,

            'result_allpart_pos_kamprol'                => $diteventjaar_pos_kamprol,
            'result_allpart_pos_event_start'            => $diteventjaar_pos_event_start,
            'result_allpart_pos_event_einde'            => $diteventjaar_pos_event_einde,

            'result_allpart_pos_kampfunctie'            => $diteventjaar_pos_kampfunctie,
            'result_allpart_pos_deel_kampfunctie'       => $diteventjaar_pos_deel_kampfunctie,
            'result_allpart_pos_leid_kampfunctie'       => $diteventjaar_pos_leid_kampfunctie,

            'result_allpart_pos_kampkort'               => $diteventjaar_pos_kampkort,
            'result_allpart_pos_deel_kampkort'          => $diteventjaar_pos_deel_kampkort,
            'result_allpart_pos_leid_kampkort'          => $diteventjaar_pos_leid_kampkort,

            'result_allpart_wait_part_id'               => $diteventjaar_wait_part_id,
            'result_allpart_wait_deel_part_id'          => $diteventjaar_wait_deel_part_id,
            'result_allpart_wait_leid_part_id'          => $diteventjaar_wait_leid_part_id,

            'result_allpart_wait_event_id'              => $diteventjaar_wait_event_id,
            'result_allpart_wait_deel_event_id'         => $diteventjaar_wait_deel_event_id,
            'result_allpart_wait_leid_event_id'         => $diteventjaar_wait_leid_event_id,

            'result_allpart_wait_event_type_id'         => $diteventjaar_wait_event_type_id,
            'result_allpart_wait_deel_event_type_id'    => $diteventjaar_wait_deel_event_type_id,
            'result_allpart_wait_leid_event_type_id'    => $diteventjaar_wait_leid_event_type_id,

            'result_allpart_wait_status_id'             => $diteventjaar_wait_status_id,
            'result_allpart_wait_deel_status_id'        => $diteventjaar_wait_deel_status_id,
            'result_allpart_wait_leid_status_id'        => $diteventjaar_wait_leid_status_id,

            'result_allpart_wait_kampkort'              => $diteventjaar_wait_kampkort,
            'result_allpart_wait_deel_kampkort'         => $diteventjaar_wait_deel_kampkort,
            'result_allpart_wait_leid_kampkort'         => $diteventjaar_wait_leid_kampkort,

            'result_allpart_pen_part_id'                => $diteventjaar_pen_part_id,
            'result_allpart_pen_deel_part_id'           => $diteventjaar_pen_deel_part_id,
            'result_allpart_pen_leid_part_id'           => $diteventjaar_pen_leid_part_id,

            'result_allpart_pen_event_id'               => $diteventjaar_pen_event_id,
            'result_allpart_pen_deel_event_id'          => $diteventjaar_pen_deel_event_id,
            'result_allpart_pen_leid_event_id'          => $diteventjaar_pen_leid_event_id,

            'result_allpart_pen_event_type_id'          => $diteventjaar_pen_event_type_id,
            'result_allpart_pen_deel_event_type_id'     => $diteventjaar_pen_deel_event_type_id,
            'result_allpart_pen_leid_event_type_id'     => $diteventjaar_pen_leid_event_type_id,

            'result_allpart_pen_status_id'              => $diteventjaar_pen_status_id,
            'result_allpart_pen_deel_status_id'         => $diteventjaar_pen_deel_status_id,
            'result_allpart_pen_leid_status_id'         => $diteventjaar_pen_leid_status_id,

            'result_allpart_pen_kampkort'               => $diteventjaar_pen_kampkort,
            'result_allpart_pen_deel_kampkort'          => $diteventjaar_pen_deel_kampkort,
            'result_allpart_pen_leid_kampkort'          => $diteventjaar_pen_leid_kampkort,                        
 
        );
        return $eventjaar_array;

        if ($result_allpart_all_count == 1) {

            wachthond($extdebug,2, "########################################################################");
            wachthond($extdebug,1, "DITEVENTJAAR_ALL PRIMA : 1 PARTICIPANT RECORDS GEVONDEN", "[cid: $contact_id]");
            wachthond($extdebug,2, "########################################################################");

            $diteventjaar_one_part_contact_id       = $result_allpart[$onekey_all]['contact_id']              ?? NULL;
            $diteventjaar_one_part_id               = $result_allpart[$onekey_all]['id']                      ?? NULL;
            $diteventjaar_one_part_eventid          = $result_allpart[$onekey_all]['event_id']                ?? NULL;
            $diteventjaar_one_status_id             = $result_allpart[$onekey_all]['status_id']               ?? NULL;
            $diteventjaar_one_status_name           = $result_allpart[$onekey_all]['status_id:name']          ?? NULL;

            $diteventjaar_one_event_title           = $result_allpart[$onekey_all]['event.title']             ?? NULL;
            $diteventjaar_one_event_type_id         = $result_allpart[$onekey_all]['event.event_type_id']     ?? NULL;
            $diteventjaar_one_event_start           = $result_allpart[$onekey_all]['event.start_date']        ?? NULL;
            $diteventjaar_one_event_einde           = $result_allpart[$onekey_all]['event.end_date']          ?? NULL;

            $diteventjaar_one_part_kampnaam         = $result_allpart[$onekey_all]['PART.PART_kamplang']      ?? NULL;
            $diteventjaar_one_part_kampkort         = $result_allpart[$onekey_all]['PART.PART_kampkort']      ?? NULL;
            $diteventjaar_one_part_functie          = $result_allpart[$onekey_all]['PART.PART_kampfunctie']   ?? NULL;
            $diteventjaar_one_part_rol              = $result_allpart[$onekey_all]['PART.PART_kamprol']       ?? NULL;
            $diteventjaar_one_part_role_id          = $result_allpart[$onekey_all]['role_id'][0]              ?? NULL;

            wachthond($extdebug,3, 'diteventjaar_one_contact_id',           $diteventjaar_one_part_contact_id);
            wachthond($extdebug,3, 'diteventjaar_one_part_id',              $diteventjaar_one_part_id);
            wachthond($extdebug,3, 'diteventjaar_one_part_eventid',         $diteventjaar_one_part_eventid);
            wachthond($extdebug,3, 'diteventjaar_one_status_id',            $diteventjaar_one_status_id);
            wachthond($extdebug,3, 'diteventjaar_one_status_name',          $diteventjaar_one_status_name);

            wachthond($extdebug,3, 'diteventjaar_one_event_title',          $diteventjaar_one_event_title);
            wachthond($extdebug,3, 'diteventjaar_one_event_type_id',        $diteventjaar_one_event_type_id);
            wachthond($extdebug,3, 'diteventjaar_one_event_start',          $diteventjaar_one_event_start);
            wachthond($extdebug,3, 'diteventjaar_one_event_einde',          $diteventjaar_one_event_einde);

            wachthond($extdebug,3, 'diteventjaar_one_part_kampnaam',        $diteventjaar_one_part_kampnaam);
            wachthond($extdebug,3, 'diteventjaar_one_part_kampkort',        $diteventjaar_one_part_kampkort);
            wachthond($extdebug,3, 'diteventjaar_one_part_functie',         $diteventjaar_one_part_functie);
            wachthond($extdebug,3, 'diteventjaar_one_part_rol',             $diteventjaar_one_part_rol);
            wachthond($extdebug,3, 'diteventjaar_one_part_role_id',         $diteventjaar_one_part_role_id);
        }

        if ($result_allpart_pos_count == 1) {

            $diteventjaar_pos_part_contact_id       = $result_allpart[$poskey_all]['contact_id']              ?? NULL;
            $diteventjaar_pos_part_id               = $result_allpart[$poskey_all]['id']                      ?? NULL;
            $diteventjaar_pos_part_eventid          = $result_allpart[$poskey_all]['event_id']                ?? NULL;
            $diteventjaar_pos_status_id             = $result_allpart[$poskey_all]['status_id']               ?? NULL;
            $diteventjaar_pos_status_name           = $result_allpart[$poskey_all]['status_id:name']          ?? NULL;

            $diteventjaar_pos_event_title           = $result_allpart[$poskey_all]['event.title']             ?? NULL;
            $diteventjaar_pos_event_type_id         = $result_allpart[$poskey_all]['event.event_type_id']     ?? NULL;
            $diteventjaar_pos_event_start           = $result_allpart[$poskey_all]['event.start_date']        ?? NULL;
            $diteventjaar_pos_event_einde           = $result_allpart[$poskey_all]['event.end_date']          ?? NULL;

            $diteventjaar_pos_part_kampnaam         = $result_allpart[$poskey_all]['PART.PART_kamplang']      ?? NULL;
            $diteventjaar_pos_part_kampkort         = $result_allpart[$poskey_all]['PART.PART_kampkort']      ?? NULL;
            $diteventjaar_pos_part_functie          = $result_allpart[$poskey_all]['PART.PART_kampfunctie']   ?? NULL;
            $diteventjaar_pos_part_rol              = $result_allpart[$poskey_all]['PART.PART_kamprol']       ?? NULL;
            $diteventjaar_pos_part_role_id          = $result_allpart[$poskey_all]['role_id'][0]              ?? NULL;

            wachthond($extdebug,3, 'diteventjaar_pos_contact_id',           $diteventjaar_pos_part_contact_id);
            wachthond($extdebug,3, 'diteventjaar_pos_part_id',              $diteventjaar_pos_part_id);
            wachthond($extdebug,3, 'diteventjaar_pos_part_eventid',         $diteventjaar_pos_part_eventid);
            wachthond($extdebug,3, 'diteventjaar_pos_status_id',            $diteventjaar_pos_status_id);
            wachthond($extdebug,3, 'diteventjaar_pos_status_name',          $diteventjaar_pos_status_name);

            wachthond($extdebug,3, 'diteventjaar_pos_event_title',          $diteventjaar_pos_event_title);
            wachthond($extdebug,3, 'diteventjaar_pos_event_type_id',        $diteventjaar_pos_event_type_id);
            wachthond($extdebug,3, 'diteventjaar_pos_event_start',          $diteventjaar_pos_event_start);
            wachthond($extdebug,3, 'diteventjaar_pos_event_einde',          $diteventjaar_pos_event_einde);

            wachthond($extdebug,3, 'diteventjaar_pos_part_kampnaam',        $diteventjaar_pos_part_kampnaam);
            wachthond($extdebug,3, 'diteventjaar_pos_part_kampkort',        $diteventjaar_pos_part_kampkort);
            wachthond($extdebug,3, 'diteventjaar_pos_part_functie',         $diteventjaar_pos_part_functie);
            wachthond($extdebug,3, 'diteventjaar_pos_part_rol',             $diteventjaar_pos_part_rol);
            wachthond($extdebug,3, 'diteventjaar_pos_part_role_id',         $diteventjaar_pos_part_role_id);
        }

        return $result_allpart;

        $allpart_array = array(
            'id'                        => $result_allpart[0]['id'],
            'contact_id'                => $result_allpart[0]['contact_id'],
            'status_id'                 => $result_allpart[0]['status_id'],
            'status_name'               => $result_allpart[0]['status_id:name'],
            'role_id'                   => $result_allpart[0]['role_id'],

            'part_event_id'             => $result_allpart[0]['event_id'],
            'event_id'                  => $result_allpart[0]['event.id'],
            'event_title'               => $result_allpart[0]['event.title'],
            'event_type_id'             => $result_allpart[0]['event.event_type_id'],
            'event_type_label'          => $result_allpart[0]['event.event_type_id:label'],

            'register_date'             => $result_allpart[0]['register_date'],
            'event_start_date'          => $result_allpart[0]['event.start_date'],
            'event_end_date'            => $result_allpart[0]['event.end_date'],

            'part_kampnaam'             => $result_allpart[0]['PART.PART_kamplang'],
            'part_kampkort'             => $result_allpart[0]['PART.PART_kampkort'],
            'part_kamptype_naam'        => $result_allpart[0]['PART.PART_kamptype_naam'],
            'part_kamptype_id'          => $result_allpart[0]['PART.PART_kamptype_id'],
            'part_functie'              => $result_allpart[0]['PART.PART_kampfunctie'],
            'part_rol'                  => $result_allpart[0]['PART.PART_kamprol'],
            'part_leid_kamp'            => $result_allpart[0]['PART_LEID.Welk_kamp'],
            'part_leid_functie'         => $result_allpart[0]['PART_LEID.Functie'],

        );
        // return $partinfo_array;
}

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
