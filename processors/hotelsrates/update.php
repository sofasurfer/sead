<?php
/**
 * Update Hotel Rate
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */
 // get tour data
$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Rate ID");

$ids = explode(',',$_DATA['id']);

foreach($ids as $id){

	$rate = $modx->getObject('seadHotelRate',$id);
	if ($rate == null) return $modx->error->failure("Error no Rate found");

	if( !empty($_DATA['typeid']) && is_numeric($_DATA['typeid']) ){

		$rate->set('typeid', $_DATA['typeid'] );
	}

	if( !empty($_DATA['datestart']) ){
		$d1 = explode('.',$_DATA['datestart']);
		$rate->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );
	}
	
	if( !empty($_DATA['dateend']) ){	
		$d2 = explode('.',$_DATA['dateend']);
		$rate->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );
	}

	if( !empty($_DATA['currency']) ){	
		$rate->set('currency', $_DATA['currency'] );
	}


	if ( $rate->save() == false ) {
	    return $modx->error->failure("Error while saving rate information");
	}
}


// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Rate :" . $_DATA['id'] );

return $modx->error->success();
