<?php
/**
 * Update a Tour
 *
 * @param integer $id The ID of the tour
 *
 * @package modx
 * @subpackage processors.tour
 */
 
 // get tour data
$_DATA = $scriptProperties;

// check if tour exist
if (empty($_DATA['id'])) return $modx->error->failure("Error no Tour ID");
$tour = $modx->getObject('seadTour',$_DATA['id']);
if ($tour == null) return $modx->error->failure("Error no Tour found");

$lang = $modx->getOption('lang',$scriptProperties,'en');

unset($_DATA['name']);
unset($_DATA['shortdescription']);
$tour->fromArray($_DATA);


// Check for languages
$nameArray = $modx->fromJSON( $tour->get('name') );
$nameArray[$lang] = $scriptProperties['name'];
$tour->set('name',$modx->toJSON($nameArray) );

// check file pdf upload
        
        $image_path = $modx->getOption('path_images').basename( $_FILES['tour_PDFAttachment_upload']['name']);
        $pdfArray = $modx->fromJSON( $tour->get('pdfAttachment') );
        
    if($_FILES["tour_PDFAttachment_upload"]["type"] == "application/pdf"){       
        
        if(move_uploaded_file($_FILES['tour_PDFAttachment_upload']['tmp_name'], $image_path)){
            $pdfArray[$lang] = 'assets/components/pdfattachments/'.$_FILES['tour_PDFAttachment_upload']['name'];
            $tour->set('pdfAttachment',$modx->toJSON($pdfArray) );
        }else{
            $pdfArray[$lang] = 'Error PDF uploaded file.';
            $tour->set('pdfAttachment',$modx->toJSON($pdfArray) );
        }
    }else{
        $pdfArray[$lang] = 'Error PDF uploaded file type.';
        $tour->set('pdfAttachment',$modx->toJSON($pdfArray) );
    }    




$sDeskArray = $modx->fromJSON( $tour->get('shortdescription') );
$sDeskArray[$lang] = $scriptProperties['shortdescription'];
$tour->set('shortdescription',$modx->toJSON($sDeskArray) );

$tour->set('lastupdate', time() );


if( !empty($_DATA['description']) ){
	$description = $modx->getObject('seadContent',array('contentkey'=>$_DATA['description'],'languagecode'=>$lang ) ); 
	if( empty($description) ){
		return $modx->error->failure("Error content not found [" . $_DATA['description'] . "] " . $lang  );
	}
	$description->set('contenthtml',$_DATA['description_text']);
	$description->save();
}

if( !empty($_DATA['itinerary']) ){
	$itinerary = $modx->getObject('seadContent',array('contentkey'=>$_DATA['itinerary'],'languagecode'=>$lang ) ); 
	if( empty($itinerary) ){
		return $modx->error->failure("Error content not found [" . $_DATA['itinerary'] . "] " .$lang  );
	}
	$itinerary->set('contenthtml',$_DATA['itinerary_text']);
	$itinerary->save();
}


// check if active
if( !empty( $_DATA['active'] ) && $_DATA['active'] == 1 ){
	$tour->set('active', 1 );
}else{
	$tour->set('active', 0 );
}

// check for frequency
$frequency = array();
if( !empty($_DATA['frequency1']) ){
	$frequency[] = "1";
}
if( !empty($_DATA['frequency2']) ){
	$frequency[] = "2";
}
if( !empty($_DATA['frequency3']) ){
	$frequency[] = "3";
}
if( !empty($_DATA['frequency4']) ){
	$frequency[] = "4";
}
if( !empty($_DATA['frequency5']) ){
	$frequency[] = "5";
}
if( !empty($_DATA['frequency6']) ){
	$frequency[] = "6";
}
if( !empty($_DATA['frequency7']) ){
	$frequency[] = "7";
}
if( !empty($_DATA['frequency8']) ){
	$frequency[] = "8";
}
if( !empty($_DATA['frequency9']) ){
	$frequency[] = "9";
}

$tour->set('frequency', implode(',',$frequency) );

$theme = array();
if( !empty($_DATA['theme1']) ){
	$theme[] = "1";
}
if( !empty($_DATA['theme2']) ){
	$theme[] = "2";
}
if( !empty($_DATA['theme3']) ){
	$theme[] = "3";
}
if( !empty($_DATA['theme4']) ){
	$theme[] = "4";
}
if( !empty($_DATA['theme5']) ){
	$theme[] = "5";
}
if( !empty($_DATA['theme6']) ){
	$theme[] = "6";
}
if( !empty($_DATA['theme7']) ){
	$theme[] = "7";
}
if( !empty($_DATA['theme8']) ){
	$theme[] = "8";
}
if( !empty($_DATA['theme9']) ){
	$theme[] = "9";
}

$tour->set('theme', implode(',',$theme) );


$segment = array();
if( !empty($_DATA['segment1']) ){
	$segment[] = "1";
}
if( !empty($_DATA['segment2']) ){
	$segment[] = "2";
}
if( !empty($_DATA['segment3']) ){
	$segment[] = "3";
}
if( !empty($_DATA['segment4']) ){
	$segment[] = "4";
}

$tour->set('segment', implode(',',$segment) );

// get countryid
$destination = $tour->getOne('Destination');
if( !empty($destination) ){
	$tour->set('countryid', $destination->get('countryid') );
}else{
	$tour->set('countryid', 0 );
}	






$tour->set('lastupdate', time() );
$tour->set('editor', $modx->user->get('username') );

if ( $tour->save() == false ) {
    return $modx->error->failure("Error while saving tour information");
}


return $modx->error->success('',$tour);
