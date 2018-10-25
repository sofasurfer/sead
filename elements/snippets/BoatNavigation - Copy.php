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




//print_r($scriptProperties);

$urlName = str_replace('', '', $_SERVER["REQUEST_URI"]);
$urlItems = explode("/", $urlName);

// get boats
$c = $modx->newQuery('seadBoat');
$c->innerJoin('seadLiveAboard', 'LiveAboard');

if (!empty($countryid) && $countryid > 0) {
    $c->where(array('seadBoat.active' => 1, 'LiveAboard.countryid' => $countryid));
} else {
    $c->where(array('seadBoat.active' => 1));
}

// select fields
$c->select('
	    `seadBoat`.*,
            `LiveAboard`.countryid,
            `LiveAboard`.boatid
	');
$c->sortby('seadBoat.name', 'ASC');

// get collection
$boatList = $modx->getCollection('seadBoat', $c);

//echo $c->toSql();

if (empty($scriptProperties['tmpl'])) {
    $scriptProperties['tmpl'] = 'BoatListItem';
}

// check if liveaboard is set
if (!empty($_GET['lid'])) {

    $liveaboard = $modx->getObject('seadLiveAboard', $_GET['lid']);
    //$scriptProperties['boatid'] = $liveaboard->get('boatid');
}
$boatHtml = "";
//echo $boatactive . " / " . $countryid;
foreach ($boatList as $boatItem) {

    $boatArray = $boatItem->toArray();

    $nameArray = $modx->fromJSON($boatItem->get('name'));
    $boatArray['name'] = $nameArray[$languageCode];
    $boatArray['boatactive'] = $boatactive;

    $liveAboards = $boatItem->getMany('LiveAboard');

    $boatArray['SubMenuLiveaboards'] = "<ul>";
    foreach ($liveAboards as $liveAboard) {

        $nameArray = $modx->fromJSON($liveAboard->get('name'));
        if (!empty($nameArray)) {
            $liveaboardName = $nameArray[$languageCode];
        } else {
            $liveaboardName = $liveAboard->get('name');
        }
        if ($liveAboard->get('active') == 1) {
            if (!empty($liveaboard) && $liveaboard->get('id') == $liveAboard->get('id')) {
                $className = "active";
            } else {
                $className = "";
            }
            $urlPath = $urlItems[1] . "/" . $urlItems[2] . "/liveaboards/" . $liveAboard->get('nameurl') . ".html";
            $boatArray['SubMenuLiveaboards'] .= '<li class="' . $className . '"><a href="' . $urlPath . '">' . $liveaboardName . '</a></li>';
        }
    }

    $boatArray['SubMenuLiveaboards'] .= '</ul>';


    $listChunk = $modx->getObject('modChunk', array(
        'name' => $scriptProperties['tmpl']
    ));

    // proccess and return booking chunk
    $boatHtml .= $listChunk->process($boatArray);
}
return $boatHtml;