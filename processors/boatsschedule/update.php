<?php
/**
 * Update Boat Schedule
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */
 // get tour data
$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Schedule ID");

$ids = explode(',',$_DATA['id']);

foreach($ids as $id){

	$Schedule = $modx->getObject('seadBoatSchedule',$id);
	if ($Schedule == null) return $modx->error->failure("Error no Schedule found");

	//$rate->fromArray($_DATA);

	if( !empty($_DATA['datestart']) ){
		$d1 = explode('.',$_DATA['datestart']);
		$Schedule->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );
	}
	
	if( !empty($_DATA['dateend']) ){	
		$d2 = explode('.',$_DATA['dateend']);
		$Schedule->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );
	}

	if( !empty($_DATA['liveaboardid']) && $_DATA['liveaboardid'] > 0 ){	
		$Schedule->set('liveaboardid', $_DATA['liveaboardid'] );
	}


	if ( $Schedule->save() == false ) {
	    return $modx->error->failure("Error while saving schedule information");
	}
}

return $modx->error->success();
