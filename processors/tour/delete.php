<?php
/**
 * Delete Tour Information
 *
 * @param integer $id The ID of the tour
 *
 * @package modx
 * @subpackage processors.tour
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Tour ID");
$tour = $modx->getObject('seadTour',$_DATA['id']);
if ($tour == null) return $modx->error->failure("Error no Tour found");

if ( $tour->remove() == false ) {
    return $modx->error->failure("Error while deleting tour");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete Tour :" .print_r($scriptProperties,true) );


return $modx->error->success('',$tour);
