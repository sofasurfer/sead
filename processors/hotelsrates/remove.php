<?php
/**
 * Delete Hotel Rates
 *
 * @param integer $id The ID of the hotel rates
 *
 * @package modx
 * @subpackage processors.hotelsrates
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Hotel ID");
$hotel = $modx->getObject('seadHotelRate',$_DATA['id']);
if ($hotel == null) return $modx->error->failure("Error no Hotel Rate found");

if ( $hotel->remove() == false ) {
    return $modx->error->failure("Error while deleting hotel");
}

return $modx->error->success('',$hotel);
