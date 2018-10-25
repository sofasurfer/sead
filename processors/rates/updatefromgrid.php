<?php
/**
 * Update Rate
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) return $modx->error->failure("Error no Rate ID");
$rate = $modx->getObject('seadRate',$_DATA['id']);
if ($rate == null) return $modx->error->failure("Error no Rate found");

$rate->fromArray($_DATA);

$modx->log(modX::LOG_LEVEL_INFO, "Update Rates:" . print_r($_DATA,true) );

if( !empty($_DATA['ratetypeid']) ){
	$rate->set('datestart', $_DATA['ratetypeid'] );
}

$d1 = explode('.',$_DATA['datestart']);
$rate->set('datestart', mktime(0,0,0,$d1[1],$d1[0],$d1[2]) );

$d2 = explode('.',$_DATA['dateend']);
$rate->set('dateend', mktime(0,0,0,$d2[1],$d2[0],$d2[2]) );

if ( $rate->save() == false ) {
    return $modx->error->failure("Error while saving rate information");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_ERROR, "Update Rate :" . print_r($_DATA,true) );

return $modx->error->success();
