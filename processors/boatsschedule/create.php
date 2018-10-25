<?php
/**
 * Add a Schedule Item
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

for( $i=0; $i < $_DATA['amount']; $i++){

	$scheduleItem = $modx->newObject('seadBoatSchedule');
	$scheduleItem->fromArray($_DATA);
	
	if( !empty($_DATA['datestart']) ){
		$d1 = explode('.',$_DATA['datestart']);
		$scheduleItem->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );
	}

	if( !empty($_DATA['dateend']) ){	
		$d2 = explode('.',$_DATA['dateend']);
		$scheduleItem->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );
	}


	if ( $scheduleItem->save() == false ) {
	    return $modx->error->failure("Error while adding schedule");
	}

}
// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Schedule :" . print_r($scriptProperties,true) );


return $modx->error->success('',$scheduleItem);
