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

if (empty($_DATA['id'])) return $modx->error->failure("Error no Tour ID");
$tour = $modx->getObject('seadTour',$_DATA['id']);
if ($tour == null) return $modx->error->failure("Error no Tour found");

$tour->fromArray($_DATA);
$tour->set('lastupdate', time() );
$tour->set('editor', $modx->user->get('username') );

if ( $tour->save() == false ) {
    return $modx->error->failure("Error while saving tour information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Tour :" . $_DATA['id'] );

/* log manager action */
//$modx->logManagerAction('hotel_update','modUser',$user->get('id'));

return $modx->error->success();
