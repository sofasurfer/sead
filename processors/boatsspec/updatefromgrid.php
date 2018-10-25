<?php
/**
 * Update BoatSpecsValues
 *
 * @param integer $id The ID of the BoatSpecsValues
 *
 * @package modx
 * @subpackage processors.BoatSpecsValues
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no BoatSpecsValues ID");
$BoatSpecsValues = $modx->getObject('seadBoatSpecValue',$_DATA['id']);
if ($BoatSpecsValues == null) return $modx->error->failure("Error no BoatSpecsValues found");

$BoatSpecsValues->fromArray($_DATA);

$BoatSpecsValues->set('datestart', strtotime($_DATA['datestart']) );
$BoatSpecsValues->set('dateend', strtotime($_DATA['dateend']) );

if ( $BoatSpecsValues->save() == false ) {
    return $modx->error->failure("Error while saving BoatSpecsValues information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update BoatSpecsValues :" . $_DATA['id'] );

return $modx->error->success();
