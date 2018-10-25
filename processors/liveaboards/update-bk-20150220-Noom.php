<?php
/**
 * Update a Liveaboard
 *
 * @param integer $id The ID of the liveaboard
 *
 * @package modx
 * @subpackage processors.liveaboard
 */
 
 // set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);


$_DATA = $scriptProperties;
unset($_DATA['name']);
unset($_DATA['shortdescription']);
unset($_DATA['description']);
unset($_DATA['itinerary']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Liveaboard ID");
$liveaboard = $modx->getObject('seadLiveAboard',$_DATA['id']);
if ($liveaboard == null) return $modx->error->failure("Error no Liveaboard found");


// Check for languages
$nameArray = $modx->fromJSON($liveaboard->get('name'));
$nameArray[$_DATA['lang']] = $scriptProperties['name'];
$_DATA['name'] = $modx->toJson($nameArray);

$sDeskArray = $modx->fromJSON($liveaboard->get('shortdescription'));
$sDeskArray[$_DATA['lang']] = $scriptProperties['shortdescription'];
$_DATA['shortdescription'] = $modx->toJson($sDeskArray);

if( !empty($scriptProperties['description']) ){
	$deskArray = $modx->fromJSON($liveaboard->get('description'));
	$deskArray[$_DATA['lang']] = $scriptProperties['description'];
	$_DATA['description'] = $modx->toJson($deskArray);
}


if( !empty($scriptProperties['itinerary']) ){
	$itineraryArray = $modx->fromJSON($liveaboard->get('itinerary'));
	$itineraryArray[$_DATA['lang']] = $scriptProperties['itinerary'];
	$_DATA['itinerary'] = $modx->toJson($itineraryArray);
}

$_DATA['lastupdate'] = time();

// Update other infos 
$liveaboard->fromArray($_DATA);


if( !empty( $_DATA['active'] ) && $_DATA['active'] == 1 ){
	$liveaboard->set('active', 1 );
}else{
	$liveaboard->set('active', 0 );
}

// get countryid
$destination = $liveaboard->getOne('Destination');
$liveaboard->set('countryid', $destination->get('countryid') );


if ( $liveaboard->save() == false ) {
    return $modx->error->failure("Error while saving liveaboard information");
}

// delete destination cache file"
$modx->log(modX::LOG_LEVEL_ERROR, "Update Liveaboard :" . print_r($scriptProperties,true) );

return $modx->error->success();
