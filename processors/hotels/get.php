<?php
/**
 * Get Hotel Detail
 *
 * @param integer $id The ID of the hotel
 *
 * @package modx
 * @subpackage processors.hotel
 */
// set log level


$_DATA = $modx->fromJSON($scriptProperties['data']);

$_DATA['id'] = $scriptProperties['id'];

if (empty($_DATA['id'])) return $modx->error->failure("Error no hotel ID in post jSon data!" . print_r($scriptProperties,true) );
$hotel = $modx->getObject('seadHotel',$_DATA['id']);
if ($hotel == null) return $modx->error->failure("Error no hotel found:" . $_DATA['id'] );

$hotelArray = $hotel->toArray();



return $modx->error->success('',$hotelArray);

