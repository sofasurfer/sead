<?php
/**
 * Update a Hotel
 *
 * @param integer $id The ID of the Hotel
 *
 * @package modx
 * @subpackage processors.hotel
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Hotel ID");
$hotel = $modx->getObject('seadHotel',$_DATA['id']);
if ($hotel == null) return $modx->error->failure("Error no Hotel found");


$hotel->fromArray($_DATA);

// get countryid
$destination = $hotel->getOne('Destination');
if( !empty($destination) ){
	$hotel->set('countryid', $destination->get('countryid') );
}

$hotel->set('lastupdate', time() );
$hotel->set('editor', $modx->user->get('username') );

if ( $hotel->save() == false ) {
    return $modx->error->failure("Error while saving hotel information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Hotel :" . $_DATA['id'] );


return $modx->error->success();
