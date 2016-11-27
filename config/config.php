<?php

/**
 * konzerte-ostschweiz.ch Concert API
 *
 * Copyright (c) konzerte-ostschweiz.ch
 *
 * @package    Konzerteostschweiz
 * @link       https://www.konzerte-ostschweiz.ch
 * @author Marco Simbürger <develop@konzerte-ostschweiz.ch>
 *
 */

/**
 * Cron Jobs
 */
if ($GLOBALS['TL_CONFIG']['ko_concertapi_use_contao_cron'] == 1) {
  $GLOBALS['TL_CRON']['minutely'][] = array(
    'Konzerteostschweiz\Concert',
    'updateConcerts'
  );
}
