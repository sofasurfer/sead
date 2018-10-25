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

if (empty($_DATA['id'])) return $modx->error->failure("Error no Liveaboard ID");
$liveAboard = $modx->getObject('seadLiveAboard',$_DATA['id']);
if ($liveAboard == null) return $modx->error->failure("Error no Liveaboard found");

if ( $liveAboard->remove() == false ) {
    return $modx->error->failure("Error while deleting liveAboard");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete liveAboard :" .print_r($scriptProperties,true) );


return $modx->error->success('',$liveAboard);
