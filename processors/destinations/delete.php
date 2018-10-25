<?php
/**
 * Delete Destination Information
 *
 * @param integer $id The ID of the destination
 *
 * @package modx
 * @subpackage processors.destination
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Destination ID");
$destination = $modx->getObject('seadDestination',$_DATA['id']);
if ($destination == null) return $modx->error->failure("Error no Tour found");

if ( $destination->remove() == false ) {
    return $modx->error->failure("Error while deleting tour");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete Destination :" .print_r($scriptProperties,true) );


return $modx->error->success('',$destination);
