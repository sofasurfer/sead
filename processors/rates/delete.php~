<?php
/**
 * Delete Rate Information
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rates
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Rate ID");

$idList = explode(',',$_DATA['id']);

foreach( $idList as $id ){
	$rate = $modx->getObject('seadRate', $id );
	if ($rate == null) return $modx->error->failure("Error no Rate found");

	if ( $rate->remove() == false ) {
	    return $modx->error->failure("Error while deleting rate");
	}
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete Rate :" .print_r($scriptProperties,true) );


return $modx->error->success('',$rate);
