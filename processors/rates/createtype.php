<?php
/**
 * Add a Rate Type
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

// check if new rate type
if( !empty($_DATA['ratetypename']) && !empty($_DATA['ratetype']) ){

	// add new rate type	
	$rateType = $modx->newObject('seadRateType');
	$rateType->set('name',$_DATA['ratetypename']);
	$rateType->set('type',$_DATA['ratetype']);

	if ( $rateType->save() == false ) {
	    return $modx->error->failure("Error while adding new rate type!");
	}

}else{
	return $modx->error->failure("Invalid name!");
}

$modx->log(modX::LOG_LEVEL_INFO, "Add Rate Type :" . print_r($scriptProperties,true) . "\n" . print_r($rateType->toArray(),true));

return $modx->error->success('',$rateType);
