<?php
/**
 * Add a Rate Item
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

// Check if rateType is Hotels
if( !empty($_DATA['ratetype']) && $_DATA['ratetype'] == 5 ){

	$rArray = array('1'=>'Single','2'=>'Double / Twin','5'=>'Tripple','6'=>'Family Room');

}else if( !empty($_DATA['ratetype']) ){
	
	$rArray = array();
	$rateTypes = $modx->getCollection('seadRateType',array('type'=> $_DATA['ratetype']) );
	foreach( $rateTypes as $rateType ){
		$rArray[$rateType->get('id')] = $rateType->get('name');
	}

}else if( !empty($_DATA['ratetypeid']) ){

	$rArray = array($_DATA['ratetypeid'] => $_DATA['categoryid'] );

}else{

	$rArray = array(0 => 0 );

}



// loop all rates to add
foreach ($rArray as $key => $value) {

	$rateItem = $modx->newObject('seadRate');
	$rateItem->fromArray($_DATA);
	$rateItem->set('productcode', $_DATA['productcode'] );	
	$rateItem->set('currency', 'USD' );

	if( !empty($_DATA['ratetype']) && $_DATA['ratetype'] == 5 ){
		$rateItem->set('typeid', $scriptProperties['ratetypeid'] );
		$rateItem->set('categoryid', $key );
	}else{
		$rateItem->set('typeid', $key );
	}
	
	if( !empty($_DATA['datestart']) ){
		//$d1 = explode('.',$_DATA['datestart']);
		//$rate->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );
		$rateItem->set('datestart', strtotime($_DATA['datestart']));
	}
	
	if( !empty($_DATA['dateend']) ){	
		//$d2 = explode('.',$_DATA['dateend']);
		//$rate->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );
		$rateItem->set('dateend', strtotime($_DATA['dateend']));
	}
	
	if( !empty($_DATA['currency']) ){
		$rateItem->set('currency', $_DATA['currency'] );
	}	


	if ( $rateItem->save() == false ) {
	    return $modx->error->failure("Error while adding rate");
	}

}



// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Rate :" . print_r($scriptProperties,true) );


return $modx->error->success('',$rateItem);
