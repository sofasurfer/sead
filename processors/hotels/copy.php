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

$oldItem = $modx->getObject('seadLiveAboard',$_DATA['id']);

if( $_DATA['id'] < 1 || empty($oldItem) ) {
    return $modx->error->failure("Error while loading liveAboard: " . $_DATA['id'] );
}

$tourItem = $modx->newObject('seadLiveAboard'); 
$tourItem->fromArray($oldItem->toArray());
$tourItem->set('name','copy of - ' . $oldItem->get('name'));
$tourItem->set('productcode', uniqid('', true) );



// save data
if ( $tourItem->save() == false ) {
    return $modx->error->failure("Error while adding liveAboard");
}

// check if boatid is set
if( $tourItem->get('boatid') > 0 ){

	// get all cabins
	$cabins = $modx->getCollection('seadBoatCabin', array('boatid' => $tourItem->get('boatid') ) );

	foreach( $cabins as $cabin ){

		// add rate
		$rateItem = $modx->newObject('seadRate');
		$rateItem->set('productcode', $tourItem->get('productcode') );
		$rateItem->set('typeid', $cabin->get('typeid') );
		$rateItem->set('price', 0 );
		$rateItem->set('currency', 'THB' );
		$rateItem->save();
	}
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add LiveAboard :" . print_r($scriptProperties,true) );


return $modx->error->success('',$tourItem);
