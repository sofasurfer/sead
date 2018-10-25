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


function addContentLanguage($modx,$contentCode,$languageCode,$htmlContent){

	$contentItem = $modx->newObject('seadContent'); 
	$contentItem->set('contentkey',$contentCode);
	$contentItem->set('languagecode',$languageCode);
	$contentItem->set('contenthtml',$htmlContent);
	$contentItem->save();

}


$_DATA = $scriptProperties;

$_DATA['id'] = $scriptProperties['id'];

$lang = $modx->getOption('lang',$scriptProperties,'en');

if (empty($_DATA['id'])) return $modx->error->failure("Error no Tour ID in post jSon data!" . print_r($scriptProperties,true) );
$tour = $modx->getObject('seadTour',$_DATA['id']);
if ($tour == null) return $modx->error->failure("Error no Tour found:" . $_DATA['id'] );

$tourArray = $tour->toArray();

$tourArray['datestart'] = date("d.m.Y",$tour->get('name'));
$tourArray['dateend'] = date("d.m.Y",$tourArray['dateend']);	

// Check for multilingual content
$nameArray = $modx->fromJSON($tourArray['name']);
if( empty($nameArray) ){
	$nameArray = array();
	$nameArray['de'] = $tourArray['name'];
	$nameArray['en'] = $tourArray['name'];
	$tour->set('name',$modx->toJson($nameArray) );
	$tour->save();
}
$tourArray['name'] = $nameArray[ $lang ];

//pdf
$nameArray = $modx->fromJSON($tourArray['pdfAttachment']);
$tourArray['tour_PDFAttachment_upload'] = $nameArray[ $lang ];


$sDeskArray = $modx->fromJSON($tour->get('shortdescription'));
if( empty($sDeskArray) ){

	$sDeskArray = array();
	$sDeskArray['de'] = $tourArray['shortdescription'];
	$sDeskArray['en'] = $tourArray['shortdescription'];
	$tour->set('shortdescription',$modx->toJson($sDeskArray) );
	$tour->save();
}
$tourArray['shortdescription'] = $sDeskArray[ $lang ];

$tourDescription =  $modx->getObject('seadContent',array('contentkey'=>$tourArray['description'],'languagecode'=>$lang) ); 
if( empty($tourDescription) || strlen($tourArray['description']) != 23 ){
	// Add new description
	$contentCode = uniqid('', true);
	addContentLanguage($modx,$contentCode,'de',$tourArray['description']);
	addContentLanguage($modx,$contentCode,'en',$tourArray['description']);
	$tourArray['description'] = $contentCode;
	$tour->set('description', $contentCode );
	$tour->save();
}
$tourDescription =  $modx->getObject('seadContent',array('contentkey'=>$tour->get('description'),'languagecode'=>$lang) ); 
if( !empty($tourDescription) ){
	$tourArray['description_text'] =  $tourDescription->get('contenthtml');
}else{
	return $modx->error->failure("Tour description not found " . $tour->get('description') . " " . $lang );	
}


$tourItinerary = $modx->getObject('seadContent',array('contentkey'=>$tourArray['itinerary'],'languagecode'=>$lang) ); 
if( empty($tourItinerary) || strlen($tourArray['itinerary']) != 23 ){

	// Add new description
	$contentCode = uniqid('', true);
	addContentLanguage($modx,$contentCode,'de',$tourArray['itinerary']);
	addContentLanguage($modx,$contentCode,'en',$tourArray['itinerary']);
	$tourArray['itinerary'] = $contentCode;
	$tour->set('itinerary', $contentCode );
	$tour->save();
}
$tourItinerary = $modx->getObject('seadContent',array('contentkey'=>$tour->get('itinerary'),'languagecode'=>$lang) ); 
if( !empty($tourItinerary) ){
	$tourArray['itinerary_text'] =  $tourItinerary->get('contenthtml');
}else{
	return $modx->error->failure("Tour itinerary not found " . $tour->get('itinerary') . " " . $lang );		
}




$modx->log(modX::LOG_LEVEL_ERROR, "Get Tour details:" . print_r($tourArray,true) );

return $modx->error->success('',$tourArray);

