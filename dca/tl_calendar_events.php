<?php

/**
 * konzerte-ostschweiz.ch Concert API
 *
 * Copyright (c) konzerte-ostschweiz.ch
 *
 * @package    Konzerteostschweiz
 * @link       https://www.konzerte-ostschweiz.ch
 * @author     Marco Simbürger <develop@konzerte-ostschweiz.ch>
 *
 */

/**
 * Add fields to tl_calender_events
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['koid'] = array
(
  'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['ko_tstamp'] = array
(
  'sql' => "int(10) unsigned NOT NULL default '0'"
);
