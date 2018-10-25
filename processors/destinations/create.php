<?php
/**
 * Add a Destination Item
 *
 * @param integer $id The ID of the destination
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$destinationItem = $modx->newObject('seadDestination'); 
$destinationItem->set('name','-- new --');
$destinationItem->set('active',1);

$destinationItem->fromArray($_DATA);




if ( $destinationItem->save() == false ) {
    return $modx->error->failure("Error while adding destination");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Destination :" . print_r($scriptProperties,true) );


return $modx->error->success('',$destinationItem);
