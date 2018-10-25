<?php
/**
 * Update Boat
 *
 * @param integer $id The ID of the boat
 *
 * @package modx
 * @subpackage processors.boat
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

$_DATA['lang'] = 'en';

if (empty($_DATA['id'])) return $modx->error->failure("Error no Boat ID");
$boat = $modx->getObject('seadBoat',$_DATA['id']);
if ($boat == null) return $modx->error->failure("Error no Boat found");


$nameArray = $modx->fromJSON($boat->get('name'));
$nameArray[$_DATA['lang']] = $_DATA['name'];
$_DATA['name'] = $modx->toJson($nameArray);

$_DATA['lastupdate'] = time();

$boat->fromArray($_DATA);

if ( $boat->save() == false ) {
    return $modx->error->failure("Error while saving boat information");
}

// delete boat cache file
// $modx->log(modX::LOG_LEVEL_ERROR, "Update Boat :" . print_r($_DATA,true) );

return $modx->error->success();
