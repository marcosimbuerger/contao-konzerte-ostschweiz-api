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
* Register the classes
*/

ClassLoader::addClasses(array(
    'Konzerteostschweiz\Concert' => 'system/modules/konzerte-ostschweiz-api/classes/Concert.php'
));