<?php
/**
 * Get Boat Detail Page
 *
 * @param integer $id The ID of the boat
 *
 * @package modx
 * @subpackage processors.boat
 */
// set log level


$_DATA = $scriptProperties;

$_DATA['id'] = $scriptProperties['id'];
$lang = $modx->getOption('lang',$scriptProperties,'en');

if (empty($_DATA['id'])) return $modx->error->failure("Error no seadBoat ID in post jSon data!" . print_r($scriptProperties,true) );
$boat = $modx->getObject('seadBoat',$_DATA['id']);
if ($boat == null) return $modx->error->failure("Error no seadBoat found:" . $_DATA['id'] );

$boatArray = $boat->toArray();

// Check for multilingual content
$nameArray = $modx->fromJSON($boatArray['name']);
if( empty($nameArray) ){
	$nameArray = array();
	$nameArray['de'] = $boatArray['name'];
	$nameArray['en'] = $boatArray['name'];
	$boat->set('name',$modx->toJson($nameArray) );
	$boat->save();
}
$boatArray['name'] = $nameArray[ $lang ];

$descArray = $modx->fromJSON($boatArray['description']);
if( empty($descArray) ){
	$descArray = array();
	$descArray['de'] = $boatArray['description'];
	$descArray['en'] = $boatArray['description'];
	$boat->set('description',$modx->toJson($descArray) );
	$boat->save();
}
$boatArray['description'] = $descArray[ $lang ];

$descShortArray = $modx->fromJSON($boatArray['shortdescription']);
if( empty($descArray) ){
	$descShortArray = array();
	$descShortArray['de'] = $boatArray['shortdescription'];
	$descShortArray['en'] = $boatArray['shortdescription'];
	$boat->set('shortdescription',$modx->toJson($descShortArray) );
	$boat->save();
}
$boatArray['shortdescription'] = $descShortArray[ $lang ];


$modx->log(modX::LOG_LEVEL_INFO, "Get Boat details:" . print_r($boatArray,true) );

return $modx->error->success('',$boatArray);

