<?php
/** SEAD Packages
 * 
 * Handels all Package/Tours Data
 *
 *
 * @package SEAD data class
 */ 


// get include file
require_once ( $modx->getOption('core_path') .'config/sead.conf.php');



$languageCode = $modx->getOption('cultureKey');


	// get tour code from param
	if( !empty($_GET['code']) ){
		$tourcode = $_GET['code'];
	}else if ( !empty($scriptProperties['tourcode']) ){
		$tourcode = $scriptProperties['tourcode'];
	}
	
	// get tour detail
	$tourDetail = $modx->getObject('seadTour', array('code1'=>$tourcode) );
	
	if( empty($tourDetail) ){
		$tourDetail = $modx->getObject('seadTour', array('pageurl'=>$_SERVER['REQUEST_URI']) );
	}

	if( empty($tourDetail) ){
		return "Tour Header not found [$tourcode]..";
	}	
	
	$tourDetailArray = $tourDetail->toArray();
	$tourDetailArray['code'] = $tourDetailArray['code1'];

	// Check for multilingual content
	$nameArray = $modx->fromJSON($tourDetailArray['name']);
	if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

		$tourDetailArray['name'] = $nameArray[$languageCode];
	}


	$sDeskArray = $modx->fromJSON($tourDetailArray['shortdescription']);
	if( !empty($sDeskArray) && !empty($sDeskArray[$languageCode])  ){

		$tourDetailArray['shortdescription'] = $sDeskArray[$languageCode];
	}

	if( strlen($tourDetailArray['description']) == 23 ){

		$description = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['description'],'languagecode'=>$languageCode) ); 
		$tourDetailArray['description'] = $description->get('contenthtml');

	}

	if( strlen($tourDetailArray['itinerary']) == 23 ){

		$itinerary = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['itinerary'],'languagecode'=>$languageCode) ); 
		$tourDetailArray['itinerary'] = $itinerary->get('contenthtml');
	}
			
	if( !empty($duration) ){
		$tourDetailArray['durationname'] = $duration->get('name');
	}

	// get destination id from code
	$destination = $tourDetail->getOne('Destination');
			
	if( !empty($destination) ){
		$tourDetailArray['destinationname'] = $destination->get('name');
	}else{
		$tourDetailArray['destinationname'] = "Destination not found";
	}

	// get language name
	$tourDetailArray['languagename'] = $LANGUAGES[$tourDetail->get('language')];
			
	// get duration
	$duration = $tourDetail->getOne('Duration');
	
	if( !empty($duration) ){		
		$tourDetailArray['durationname'] = $duration->get('name');
	}else{
		$tourDetailArray['durationname'] = "";
	}

	// week days frequency
	$fArray = explode(',',$tourDetailArray['frequency']);
	$fArrayValues = array();
	foreach( $fArray as $dId ){
		$fArrayValues[] = $WEEKDAYS[$dId];
	}
	$tourDetailArray['frequencynames'] = implode(', ',$fArrayValues);

	// themes
	$tArray = explode(',',$tourDetailArray['theme']);
	$tArrayValues = array();
	foreach( $tArray as $tId ){
		$tArrayValues[] = $THEMES[$tId];
	}
	$tourDetailArray['themenames'] = implode(', ',$tArrayValues);

	// segmentation
	$sArray = explode(',',$tourDetailArray['segment']);
	$sArrayValues = array();
	foreach( $sArray as $sId ){
		$sArrayValues[] = $SEGMENTS[$sId];
	}
	$tourDetailArray['segmentnames'] = implode(', ',$sArrayValues);

	
	// get tour list chunk
	if( empty($scriptProperties['tmpl']) ){
		$scriptProperties['tmpl'] = "TourHeader";
	}
		
	$listChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tmpl']
	));

    // make all porperties public
    $modx->toPlaceholders($tourDetailArray);

		            
	// proccess and return booking chunk
	return $listChunk->process($tourDetailArray);