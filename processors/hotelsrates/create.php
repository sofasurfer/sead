<?php
/**
 * Add a Hotel Rate
 *
 * @param integer $id The ID of the hotel
 *
 * @package modx
 * @subpackage processors.hotel
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

// check if hotel id
if( empty($_DATA['hotelid']) ){

	return $modx->error->failure("Invalid hotel id");

// check what rates to add
}else if( $scriptProperties['roomtypes'] == 'all' ){

	$rArray = array('1'=>'Single','2'=>'Double / Twin','5'=>'Tripple','6'=>'Famili Room');

}else{
	$rArray = array('1'=>'empty');
}

// loop all rates to add
foreach ($rArray as $key => $value) {

	$hotelRate = $modx->newObject('seadHotelRate'); 
	$hotelRate->fromArray($_DATA);

	$hotelRate->set('hotelid', $_DATA['hotelid'] );	

	$hotelRate->set('roomid', $key );

	if( !empty($_DATA['typeid']) && $_DATA['typeid'] > 0 ){
		$hotelRate->set('typeid', $_DATA['typeid'] );
	}else{
		$hotelRate->set('typeid', 0 );
	}

	if( !empty($_DATA['currency']) ){
		$hotelRate->set('currency', $_DATA['currency'] );
	}else{
		$hotelRate->set('currency', 'USD' );
	}
	
	if( !empty($_DATA['datestart']) ){
		$d1 = explode('.',$_DATA['datestart']);
		$hotelRate->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );
	}else{
		$hotelRate->set('datestart', time() );
	}
	
	if( !empty($_DATA['dateend']) ){	
		$d2 = explode('.',$_DATA['dateend']);
		$hotelRate->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );
	}else{
		$hotelRate->set('dateend', time() );
	}
	
	if( !empty($_DATA['currency']) ){
		$hotelRate->set('currency', $_DATA['currency'] );
	}else{
		$hotelRate->set('currency', 'USD' );
	}	

	// save data
	if ( $hotelRate->save() == false ) {
	    return $modx->error->failure("Error while adding hotel rate");
	}

	// delete destination cache file
	$modx->log(modX::LOG_LEVEL_INFO, "Add Hotel Rate :" . print_r($scriptProperties,true) );

}

return $modx->error->success('',$hotelRate);
