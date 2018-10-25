<?php
/**
 * Add a Currency Item
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$currencyItem = $modx->newObject('seadCurrency'); 
$currencyItem->set('name','-- new --');
$currencyItem->set('code','');
$currencyItem->set('eur',1);
$currencyItem->set('usd',1);
$currencyItem->set('thb',1);




if ( $currencyItem->save() == false ) {
    return $modx->error->failure("Error while adding currency");
}

return $modx->error->success('',$currencyItem);
