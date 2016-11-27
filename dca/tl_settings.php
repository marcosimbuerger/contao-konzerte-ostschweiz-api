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
 * Extend a tl_settings default palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('defaultChmod', 'defaultChmod;{ko_concertapi_legend},concert_calendar,ko_concertapi_rest_url,ko_concertapi_use_contao_cron;', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

/**
 * Add fields to tl_settings
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['concert_calendar'] = array
(
  'label'                 => &$GLOBALS['TL_LANG']['tl_settings']['concert_calendar'],
  'inputType'             => 'select',
  'foreignKey'            => 'tl_calendar.title',
  'eval'                  => array('mandatory'=>false, 'multiple'=>false, 'tl_class'=>'clr'),
  'relation'              => array('type'=>'hasOne', 'load'=>'lazy')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['ko_concertapi_rest_url'] = array
(
  'label'                 => &$GLOBALS['TL_LANG']['tl_settings']['ko_concertapi_rest_url'],
  'inputType'             => 'text',
  'eval'                  => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['ko_concertapi_use_contao_cron'] = array
(
  'label'                 => &$GLOBALS['TL_LANG']['tl_settings']['ko_concertapi_use_contao_cron'],
  'inputType'             => 'checkbox',
  'eval'                  => array('mandatory'=>false, 'tl_class'=>'clr'),
);
