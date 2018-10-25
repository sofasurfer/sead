<?php
/**
 * Update Rate
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

	$rate = $modx->getObject('seadRate',$id);
	if ($rate == null) return $modx->error->failure("Error no Rate found");

	if( !empty($_DATA['ratetypeid']) && is_numeric($_DATA['ratetypeid']) ){

		$rate->set('typeid', $_DATA['ratetypeid'] );

	}

	if( !empty($_DATA['categoryid']) && is_numeric($_DATA['categoryid']) ){

		$rate->set('categoryid', $_DATA['categoryid'] );

	}

	if( !empty($_DATA['duration']) && $_DATA['duration'] > 0 ){
		$rate->set('duration', $_DATA['duration'] );
	}

	if( !empty($_DATA['datestart']) ){
		//$d1 = explode('.',$_DATA['datestart']);
		//$rate->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );
		$rate->set('datestart', strtotime($_DATA['datestart']));
	}
	
	if( !empty($_DATA['dateend']) ){	
		//$d2 = explode('.',$_DATA['dateend']);
		//$rate->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );
		$rate->set('dateend', strtotime($_DATA['dateend']));
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
