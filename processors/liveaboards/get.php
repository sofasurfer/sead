<?php

/**

 * Get Tour Detail Page

 *

 * @param integer $id The ID of the tour

 *

 * @package modx

 * @subpackage processors.tour

 */

// set log level





$_DATA = $scriptProperties;



//$_DATA['id'] = $scriptProperties['id'];



$_DATA['id'] = str_replace("liveaboards.html?bid=","",$scriptProperties['id']);



$lang = $modx->getOption('lang',$scriptProperties,'en');



if (empty($_DATA['id'])) return $modx->error->failure("Error no seadLiveAboard ID in post jSon data!" . print_r($scriptProperties,true) );

$tour = $modx->getObject('seadLiveAboard',$_DATA['id']);

if ($tour == null) return $modx->error->failure("Error no seadLiveAboard found:" . $_DATA['id'] );



/* Get Boat Data, BitSiren, 2015-04-09, AW */

if($tour->boatid) $oBoat = $modx->getObject('seadBoat',$tour->boatid);

/* Get Country Data, BitSiren, 2015-04-09, AW */

if($tour->countryid) $oCountry = $modx->getObject('seadCountry',$tour->countryid);





$tourArray = $tour->toArray();



$tourArray['datestart'] = date("d.m.Y",$tourArray['datestart']);

$tourArray['dateend'] = date("d.m.Y",$tourArray['dateend']);	





// Check for multilingual content

$nameArray = $modx->fromJSON($tour->get('name'));

if( empty($nameArray) ){

	$nameArray = array();

	$nameArray['de'] = $tourArray['name'];

	$nameArray['en'] = $tourArray['name'];

	$tour->set('name',$modx->toJson($nameArray) );

	$tour->save();

}

$tourArray['name'] = $nameArray[ $lang ];

	

$sDeskArray = $modx->fromJSON($tour->get('shortdescription'));

if( empty($sDeskArray) ){

	$sDeskArray = array();

	$sDeskArray['de'] = $tourArray['shortdescription'];

	$sDeskArray['en'] = $tourArray['shortdescription'];

	$tour->set('shortdescription',$modx->toJson($sDeskArray) );

	$tour->save();

}

$tourArray['shortdescription'] = $sDeskArray[ $lang ];



$deskArray = $modx->fromJSON($tour->get('description'));

if( empty($deskArray) ){

	$deskArray = array();

	$deskArray['de'] = $tourArray['description'];

	$deskArray['en'] = $tourArray['description'];

	$tour->set('description',$modx->toJson($deskArray) );

	$tour->save();

}

$tourArray['description'] = $deskArray[ $lang ];



$itineraryArray = $modx->fromJSON($tour->get('itinerary'));

if( empty($itineraryArray) ){

	$itineraryArray = array();

	$itineraryArray['de'] = $tourArray['itinerary'];

	$itineraryArray['en'] = $tourArray['itinerary'];

	$tour->set('itinerary',$modx->toJson($itineraryArray) );

	$tour->save();

}

$tourArray['itinerary'] = $itineraryArray[ $lang ];



// Get Boat

$boat = $tour->getOne('Boat');

if( !empty($boat) ){

$tourArray['boatid'] = $boat->get('id');

$boatNameArray = $modx->fromJSON($boat->get('name'));

$tourArray['boatname'] = $boatNameArray[ $lang ];

/* Incorrect URL

$tourArray['boaturl'] = "/data/boats.html?bid=".$tourArray['boatid']; */

/* New URL, BitSiren, 2015-04-09, AW */

$tourArray['boaturl'] = "/scuba-diving/" . $oCountry->name . "/boats/". $oBoat->nameurl .".html?bid=".$tourArray['boatid'];



}





if( isset($tourArray['productcode'])){

		//$tourArray['galleryimg'] .= $tourArray['id'];

		

		// Changed to using query object to sort, AW, 2015-04-21

		$c = $modx->newQuery('seadProductGalleryImage');

		$c->where(array('productcode' => $tourArray['productcode'],));

		$c->sortby('id','DESC');

		// $seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',array('productcode'=>$tourArray['productcode']) );				

		$seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',$c);

		

		//$tourDetailArray['galleryimg'] = $seadProductGalleryImage->get('filename');

               // $seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage' );                

                 

		foreach ($seadProductGalleryImage as $doc) {

			$tourArray['galleryimg']  .= '<img src="'.$doc->get('filename') . '" alt="'.$doc->get('alttext').'"><br/>';                       

		}

               

	}



//echo '<pre>'; print_r($tourArray); echo '</pre>';



$modx->log(modX::LOG_LEVEL_INFO, "Get Tour details:" . print_r($tourArray,true) );



return $modx->error->success('',$tourArray);



