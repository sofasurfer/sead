<?php
/**
 * Update a Hotel
 *
 * @param integer $id The ID of the hotel
 *
 * @package modx
 * @subpackage processors.hotel
 */
 
 // set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);


$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no hotel ID");
$hotel = $modx->getObject('seadHotel',$_DATA['id']);
if ($hotel == null) return $modx->error->failure("Error no Hotel found");


// check if hotelid is changed

// update 
$hotel->fromArray($_DATA);

if( !empty( $_DATA['active'] ) && $_DATA['active'] == 1 ){
	$hotel->set('active', 1 );
}else{
	$hotel->set('active', 0 );
}

// get countryid
$destination = $hotel->getOne('Destination');
$hotel->set('countryid', $destination->get('countryid') );


if ( $hotel->save() == false ) {
    return $modx->error->failure("Error while saving hotel information");
}

return $modx->error->success();
