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
if( !empty($nameArray) ){
    if( !empty($nameArray[$languageCode]) ) {
        $tourDetailArray['name'] = $nameArray[$languageCode];
    }else{
        $tourDetailArray['name'] = $nameArray['en'];
    }
}

$sDeskArray = $modx->fromJSON($tourDetailArray['shortdescription']);
if( !empty($sDeskArray) ){
    if( !empty($sDeskArray[$languageCode]) ) {
        $tourDetailArray['shortdescription'] = $sDeskArray[$languageCode];
    }else{
        $tourDetailArray['shortdescription'] = $sDeskArray['en'];
    }
}

if( strlen($tourDetailArray['description']) == 23 ){
    $description = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['description'],'languagecode'=>$languageCode) ); 
    $tourDetailArray['description'] = $description->get('contenthtml');
}

if( strlen($tourDetailArray['itinerary']) == 23 ){
    $itinerary = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['itinerary'],'languagecode'=>$languageCode) ); 
    $tourDetailArray['itinerary'] = $itinerary->get('contenthtml');
}

    
// noom
$pdfAttachment = $modx->fromJSON($tourDetailArray['pdfAttachment']);
if( !empty($pdfAttachment) ){
    if( !empty($pdfAttachment[$languageCode]) ) {
    	if(strpos($pdfAttachment[$languageCode], '.pdf')){
	    	$tourDetailArray['pdfAttachment'] ='Download the detailed program information in PDF format:';
	        $tourDetailArray['pdfAttachment'].= '<a class="link-pdf" href="'.$pdfAttachment[$languageCode].'" target="_blank">';
	        $tourDetailArray['pdfAttachment'].='<img src="assets/templates/sead-new/images/pdf-download.jpg" alt="pdf" /></a>';
    	}else{
    		$tourDetailArray['pdfAttachment'] = '&nbsp;';
    	}
    }else{
        $tourDetailArray['pdfAttachment'] = '&nbsp;';
    }
}

if( isset($tourDetailArray['productcode'])){
    // Changed to using query object to sort, AW, 2015-04-21
    $c = $modx->newQuery('seadProductGalleryImage');
    $c->where(array('productcode' => $tourDetailArray['productcode'],));
    $c->sortby('id','DESC');
    // $seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',array('productcode'=>$tourDetailArray['productcode']) );   
    $seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',$c);  
    //$tourDetailArray['galleryimg'] = $seadProductGalleryImage->get('filename');
    foreach ($seadProductGalleryImage as $doc) {
        $tourDetailArray['galleryimg']  .= '<img src="'.$doc->get('filename') . '" alt="'.$doc->get('alttext').'"><br/>';
    }
}   
    

// noom 
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