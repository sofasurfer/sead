<?php
/** SEAD Packages
 * 
 * Handels all Package/Tours Data
 *
 *
 * @package SEAD data class
 */
// get include file
require_once ( $modx->getOption('core_path') . 'config/sead.conf.php');
$lang = $modx->getOption('lang',$scriptProperties,'en');

//print_r($scriptProperties);

$urlName = str_replace('', '', $_SERVER["REQUEST_URI"]);
//$urlName = str_replace('', '', 'liveaboards/thailand_myanmar_burma_8_days_safari_mv_deep_andaman_queen.html');

$urlItems = explode("/", $urlName);


$boatList = $modx->getCollection('seadTour', array('active' => '1','typeid'=>'3'));

if (empty($scriptProperties['tmpl'])) {
    $scriptProperties['tmpl'] = 'TourLinkListItem';
}
$boatHtml = "";


foreach ($boatList as $tourItem) {
    $tourArray = $tourItem->toArray();
    
    $nameArray = $modx->fromJSON($tourItem->get('name'));
    $tourArray['name'] = $nameArray[$languageCode];
   
    $tourArray['nameurl'] =$tourItem->get('pageurl'); 
    
    $listChunk = $modx->getObject('modChunk', array(
        'name' => $scriptProperties['tmpl']
    ));
    // proccess and return booking chunk
    $boatHtml .= $listChunk->process($tourArray);
    
}


return $boatHtml;