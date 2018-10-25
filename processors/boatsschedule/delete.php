<?php
/**
 * Delete Schedule Information
 *
 * @param integer $id The ID of the Schedule
 *
 * @package modx
 * @subpackage processors.Schedules
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Schedule ID");

$idList = explode(',',$_DATA['id']);

foreach( $idList as $id ){
	$Schedule = $modx->getObject('seadBoatSchedule', $id );
	if ($Schedule == null) return $modx->error->failure("Error no Schedule found");

	if ( $Schedule->remove() == false ) {
	    return $modx->error->failure("Error while deleting Schedule");
	}
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Delete Schedule :" .print_r($scriptProperties,true) );


return $modx->error->success('',$Schedule);
