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
 * Add fields to tl_calender_events.
 */

// Update palettes.
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace
(
  '{publish_legend}',
  '{ko_concertapi_legend},association_name;{publish_legend}',
  $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']
);

// Add fields.
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['koid'] = array
(
  'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['ko_tstamp'] = array
(
  'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['association_name'] = array
(
  'label'         => &$GLOBALS['TL_LANG']['tl_calendar_events']['association_name'],
  'exclude'       => true,
  'search'        => true,
  'inputType'     => 'text',
  'eval'          => array('maxlength'=>255, 'tl_class'=>'w50'),
  'sql'           => "varchar(255) NOT NULL default ''"
);
