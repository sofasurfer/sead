<?php
/**
 * Delete Rate Type
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rates
 */

$_DATA = $scriptProperties;

if (empty($_DATA['ratetypeid'])) return $modx->error->failure("Error no RateType ID");

$rate = $modx->getObject('seadRateType', $_DATA['ratetypeid'] );
if ($rate == null) return $modx->error->failure("Error RateType not found");

if ( $rate->remove() == false ) {
    return $modx->error->failure("Error while deleting RateType");
}


// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete RateType :" .print_r($scriptProperties,true) );


return $modx->error->success('',$rate);
