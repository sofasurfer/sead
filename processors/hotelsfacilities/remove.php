<?php
/**
 * Delete Hotel Facility Type
 *
 * @param integer $id The ID of the hotel rates
 *
 * @package modx
 * @subpackage processors.hotelsrates
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Facility ID");
$hotelFacility = $modx->getObject('seadHotelFacilityType',$_DATA['id']);
if ($hotelFacility == null) return $modx->error->failure("Error no Hotel Facility Type found");

if ( $hotelFacility->remove() == false ) {
    return $modx->error->failure("Error while deleting hotel facility type");
}

return $modx->error->success('',$hotelFacility);
