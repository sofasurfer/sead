<?php
/**
 * Update Currency
 *
 * @param integer $id The ID of the currency
 *
 * @package modx
 * @subpackage processors.currency
 */
$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Currency ID");
$currency = $modx->getObject('seadCurrency',$_DATA['id']);
if ($currency == null) return $modx->error->failure("Error no Currency found");

$_DATA['code'] = trim($_DATA['code']);
$_DATA['name'] = trim($_DATA['name']);

$currency->fromArray($_DATA);

if ( $currency->save() == false ) {
    return $modx->error->failure("Error while saving currency information");
}

// delete currency cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Currency :" . $_DATA['id'] );

return $modx->error->success();
