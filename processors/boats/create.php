<?php
/**
 * Add a Tour Item
 *
 * @param integer $id The ID of the tour
 *
 * @package modx
 * @subpackage processors.tour
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$boatItem = $modx->newObject('seadBoat'); 
$boatItem->fromArray($_DATA);
$boatItem->set('name','-- new --');
$boatItem->set('lastupdate', time() );


if ( $boatItem->save() == false ) {
    return $modx->error->failure("Error while adding Boat");
}


$specs = $modx->getCollection('seadBoatSpec', array('id:>'=>0) );

foreach($specs as $spec){

	$boatSpecValue = $modx->newObject('seadBoatSpecValue'); 
	$boatSpecValue->set('specid', $spec->get('id') );
	$boatSpecValue->set('boatid', $boatItem->get('id') );	
	$boatSpecValue->save();
}


// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Boat :" . print_r($scriptProperties,true) );


return $modx->error->success('',$boatItem);
