<?php
/**
 * Update a Tour
 *
 * @param integer $id The ID of the tour
 *
 * @package modx
 * @subpackage processors.tour
 */


$_DATA = $modx->fromJSON($scriptProperties['data']);

$_DATA['lang'] = "en";

if (empty($_DATA['id'])) return $modx->error->failure("Error no Tour ID");
$tour = $modx->getObject('seadTour',$_DATA['id']);
if ($tour == null) return $modx->error->failure("Error no Tour found");

$nameArray = $modx->fromJSON($tour->get('name'));
$nameArray[$_DATA['lang']] = $_DATA['name'];
$_DATA['name'] = $modx->toJson($nameArray);

$_DATA['lastupdate'] = time();


$tour->fromArray($_DATA);


$tour->set('datestart', strtotime($_DATA['datestart']) );
$tour->set('dateend', strtotime($_DATA['dateend']) );

// get countryid
$destination = $tour->getOne('Destination');
if( !empty($destination)){
    $tour->set('countryid', $destination->get('countryid') );
}

$tour->set('lastupdate', time() );
$tour->set('editor', $modx->user->get('username') );

if ( $tour->save() == false ) {
    return $modx->error->failure("Error while saving tour information");
}

$modx->log(modX::LOG_LEVEL_ERROR, "Update Tour :" . $_DATA['id'] );

return $modx->error->success();
