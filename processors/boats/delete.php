<?php
/**
 * Delete Boat Information
 *
 * @param integer $id The ID of the tour
 *
 * @package modx
 * @subpackage processors.tour
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Boat ID");
$Boat = $modx->getObject('seadBoat',$_DATA['id']);
if ($Boat == null) return $modx->error->failure("Error no Boat found");

if ( $Boat->remove() == false ) {
    return $modx->error->failure("Error while deleting Boat");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete Boat :" .print_r($scriptProperties,true) );


return $modx->error->success('',$Boat);
