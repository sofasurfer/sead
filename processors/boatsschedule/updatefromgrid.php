<?php
/**
 * Update Schedule
 *
 * @param integer $id The ID of the Schedule
 *
 * @package modx
 * @subpackage processors.Schedule
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Schedule ID");
$Schedule = $modx->getObject('seadBoatSchedule',$_DATA['id']);
if ($Schedule == null) return $modx->error->failure("Error no Schedule found");

$Schedule->fromArray($_DATA);

$d1 = explode('.',$_DATA['datestart']);
$Schedule->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );

$d2 = explode('.',$_DATA['dateend']);
$Schedule->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );

if ( $Schedule->save() == false ) {
    return $modx->error->failure("Error while saving Schedule information");
}


// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Schedule :" . $_DATA['id'] );

return $modx->error->success();
