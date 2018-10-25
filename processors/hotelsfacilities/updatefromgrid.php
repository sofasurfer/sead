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

if (empty($_DATA['id'])) return $modx->error->failure("Error no Facility ID");


$hotelFacility = $modx->getObject('seadHotelFacility',$_DATA['hotelfacilityid']);
if( !empty($hotelFacility) ){

	// Check if active
	if( $_DATA['checked'] == true ){

		$hotelFacility->set('remarks', $_DATA['remarks']);

		if ( $hotelFacility->save() == false ) {
		    return $modx->error->failure("Error while updating hotel facility");
		}

	}else{
		if ( $hotelFacility->remove() == false ) {
		    return $modx->error->failure("Error while removing hotel facility");
		}
	}

}else{

	$hotelFacility = $modx->newObject('seadHotelFacility');
	$hotelFacility->set('hotelid',$_DATA['hotelid']);
	$hotelFacility->set('facilityid',$_DATA['id']);
	$hotelFacility->set('remarks',$_DATA['remarks']);

	if ( $hotelFacility->save() == false ) {
	    return $modx->error->failure("Error while saving hotel facility");
	}


}


// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Hotel Facility :" . $_DATA['id'] );


return $modx->error->success();
