<?php
/**
 * Update a Boat
 *
 * @param integer $id The ID of the boat
 *
 * @package modx
 * @subpackage processors.boat
 */
 
 // set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);


$_DATA = $scriptProperties;
unset($_DATA['name']);
unset($_DATA['shortdescription']);
unset($_DATA['description']);


if (empty($_DATA['id'])) return $modx->error->failure("Error no Boat ID");
$boat = $modx->getObject('seadBoat',$_DATA['id']);
if ($boat == null) return $modx->error->failure("Error no Boat found");

// Check for languages
$nameArray = $modx->fromJSON($boat->get('name'));
$nameArray[$_DATA['lang']] = $scriptProperties['name'];
$_DATA['name'] = $modx->toJson($nameArray);

$deskArray = $modx->fromJSON($boat->get('description'));
$deskArray[$_DATA['lang']] = $scriptProperties['description'];
$_DATA['description'] = $modx->toJson($deskArray);

$deskShortArray = $modx->fromJSON($boat->get('shortdescription'));
$deskShortArray[$_DATA['lang']] = $scriptProperties['shortdescription'];
$_DATA['shortdescription'] = $modx->toJson($deskShortArray);


$_DATA['lastupdate'] = time();

$boat->fromArray($_DATA);

if( !empty( $_DATA['active'] ) && $_DATA['active'] == 1 ){
	$boat->set('active', 1 );
}else{
	$boat->set('active', 0 );
}

if ( $boat->save() == false ) {
    return $modx->error->failure("Error while saving boat information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Boat :" . $_DATA['id'] . print_r($_DATA,true) );

return $modx->error->success();
