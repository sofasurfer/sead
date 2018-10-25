<?php
/**
 * Update Destination
 *
 * @param integer $id The ID of the destination
 *
 * @package modx
 * @subpackage processors.destination
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Destination ID");
$destination = $modx->getObject('seadDestination',$_DATA['id']);
if ($destination == null) return $modx->error->failure("Error no Destination found");


$_DATA['name'] = trim($_DATA['name']);

$destination->fromArray($_DATA);

if ( $destination->save() == false ) {
    return $modx->error->failure("Error while saving destination information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Destination :" . $_DATA['id'] );

return $modx->error->success();
