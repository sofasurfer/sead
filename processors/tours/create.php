<?php
/**
 * Add a Tour Item
 *
 * @param integer $id The ID of the tour
 *
 * @package modx
 * @subpackage processors.tour
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

$tourItem = $modx->newObject('seadTour'); 
$tourItem->fromArray($_DATA);
$tourItem->set('productcode', uniqid('', true) );
$tourItem->set('duration','01:00:00');
$tourItem->set('active',1);
$tourItem->set('lastupdate', time() );
$tourItem->set('editor', $modx->user->get('username') );

// Set name
$nameArray = array();
$nameArray['en'] = '-- new --';
$nameArray['de'] = '-- neu --';
$tourItem->set('name',$modx->toJson( $nameArray ) );

// Set shortdescription
$descArray = array();
$descArray['en'] = '';
$descArray['de'] = '';
$tourItem->set('shortdescription',$modx->toJson( $descArray ) );


if ( $tourItem->save() == false ) {
    return $modx->error->failure("Error while adding tour");
}

// delete destination cache file
$modx->log(modX::LOG_LEVEL_INFO, "Add Tour Option :" . print_r($scriptProperties,true) );


return $modx->error->success('',$tourItem);
