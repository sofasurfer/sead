<?php
/**
 * Add a Boat Cabin Item
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$cabinItem = $modx->newObject('seadBoatCabin'); 
$cabinItem->fromArray($_DATA);
$cabinItem->set('typeid', 0 );

if ( $cabinItem->save() == false ) {
    return $modx->error->failure("Error while adding cabin");
}



// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Cabin :" . print_r($scriptProperties,true) );


return $modx->error->success('',$cabinItem);
