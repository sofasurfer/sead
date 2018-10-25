<?php
/**
 * Update Boat Cabin
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Cabin ID");
$cabin = $modx->getObject('seadBoatCabin',$_DATA['id']);
if ($cabin == null) return $modx->error->failure("Error no Cabin found");

$cabin->fromArray($_DATA);

if ( $cabin->save() == false ) {
    return $modx->error->failure("Error while saving cabin information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update cabin :" . $_DATA['id'] );

return $modx->error->success();
