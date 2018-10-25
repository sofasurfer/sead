<?php
/**
 * Delete Boat Cabin Information
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rates
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Cabin ID");

$cabin = $modx->getObject('seadBoatCabin', $_DATA['id'] );
if ($cabin == null) return $modx->error->failure("Error no Cabin found");

// delete sead rates first if this cabin type have only 1 record (more than 1 not delete)
$boatid = $cabin->get('boatid');
$typeid = $cabin->get('typeid');
$cabinBoatType = $modx->getCollection('seadBoatCabin', array(
   'seadBoatCabin.boatid' => $boatid,
   'seadBoatCabin.typeid' => $typeid
));
if(count($cabinBoatType) == 1) {
	$liveaboards = $modx->getCollection('seadLiveAboard', array('boatid' => $boatid));
	foreach( $liveaboards as $liveaboard ){
		$rateItem = $modx->getObject('seadRate', array(
			'seadRate.productcode' => $liveaboard->get('productcode'),
			'seadRate.typeid' => $typeid
		));
		if($rateItem) {
			if ( $rateItem->remove() == false ) {
			    return $modx->error->failure("Error while deleting rate");
			}
		}
	}
}

// then delete on boat cabin
if ( $cabin->remove() == false ) {
    return $modx->error->failure("Error while deleting cabin");
} else {

}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete Cabin :" .print_r($scriptProperties,true) );


return $modx->error->success('',$cabin);
