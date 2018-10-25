<?php
/**
 * Add a Hotel Facility Type
 *
 * @param integer $id The ID of the hotel and the new facility
 *
 * @package modx
 * @subpackage processors.hotel
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$seadHotelFacilityType = $modx->newObject('seadHotelFacilityType'); 
$seadHotelFacilityType->fromArray($_DATA);
$seadHotelFacilityType->set('name',$_DATA['facilityname']);

// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Hotel Facility Type:" . print_r($scriptProperties,true) );

// save data
if ( $seadHotelFacilityType->save() == false ) {
    return $modx->error->failure("Error while adding hotel facility type");
}else{
	// Add Facility Type to Hotel 
	$seadHotelFacility = $modx->newObject('seadHotelFacility'); 
	$seadHotelFacility->set('hotelid',$_DATA['hotelid']);
	$seadHotelFacility->set('facilityid',$seadHotelFacilityType->get('id'));

	if ( $seadHotelFacility->save() == false ) {
	    return $modx->error->failure("Error while adding new facility type to hotel");
	}
}


return $modx->error->success('',$seadHotelFacilityType);
