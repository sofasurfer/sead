<?php
/**
 * Update a Liveaboard
 *
 * @param integer $id The ID of the liveaboard
 *
 * @package modx
 * @subpackage processors.liveaboard
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Liveaboard ID");
$liveaboard = $modx->getObject('seadLiveAboard',$_DATA['id']);
if ($liveaboard == null) return $modx->error->failure("Error no Liveaboard found");

$nameArray = $modx->fromJSON($liveaboard->get('name'));
$nameArray['en'] = $_DATA['name'];
$liveaboard->set('name', $modx->toJson($nameArray) );

unset($_DATA['name']);
$liveaboard->fromArray($_DATA);

$liveaboard->set('datestart', strtotime($_DATA['datestart']) );
$liveaboard->set('dateend', strtotime($_DATA['dateend']) );

// get countryid
$destination = $liveaboard->getOne('Destination');
if( !empty($destination) ){
	$liveaboard->set('countryid', $destination->get('countryid') );
}

$liveaboard->set('lastupdate', time() );
$liveaboard->set('editor', $modx->user->get('username') );

if ( $liveaboard->save() == false ) {
    return $modx->error->failure("Error while saving liveaboard information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Liveaboard :" . $_DATA['id'] );


return $modx->error->success();
