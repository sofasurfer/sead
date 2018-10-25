<?php
/**
 * Add a Hotel
 *
 * @param integer $id The ID of the hotel
 *
 * @package modx
 * @subpackage processors.hotel
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$hotel = $modx->newObject('seadHotel'); 
$hotel->fromArray($_DATA);
$hotel->set('name','-- new --');
$hotel->set('productcode', uniqid('', true) );
$hotel->set('active',1);
$hotel->set('lastupdate', time() );
$hotel->set('editor', $modx->user->get('username') );

// save data
if ( $hotel->save() == false ) {
    return $modx->error->failure("Error while adding hotel");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Hotel :" . print_r($scriptProperties,true) );


return $modx->error->success('',$hotel);
