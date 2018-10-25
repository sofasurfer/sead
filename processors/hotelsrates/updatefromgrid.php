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
$hotelRate = $modx->getObject('seadHotelRate',$_DATA['id']);
if ($hotelRate == null) return $modx->error->failure("Error no HotelRate found");


$hotelRate->fromArray($_DATA);

$d1 = explode('.',$_DATA['datestart']);
$hotelRate->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );

$d2 = explode('.',$_DATA['dateend']);
$hotelRate->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );

if ( $hotelRate->save() == false ) {
    return $modx->error->failure("Error while saving hotel rate information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Hotel Rate :" . $_DATA['id'] );


return $modx->error->success();
