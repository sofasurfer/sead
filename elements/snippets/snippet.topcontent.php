<?php
/** SEAD Top Content
 * 
 * Handels homepage banners
 *
 *
 * @package SEAD data class
 */ 

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('Asia/Singapore');

/**
 * Add SEAD Data information package
 */
$modx->addPackage('SEAD',MODX_CORE_PATH.'components/sead/model/','sead_');

/* Load Lexicon */
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load('sead:default');

if( $scriptProperties['type'] == 'package' ){

	// Get a random tour.
	$criteria = $modx->newQuery('seadTour');
	//$criteria->innerJoin('seadDuration','Duration');
	$criteria->select('seadTour.*');
	$criteria->where(array(
		'seadTour.active' => 1
	));
	$criteria->sortby('RAND()');
	$criteria->limit(1); //Limit the results to 1
	$tour = $modx->getObject('seadTour',$criteria);

	if( !empty($tour) ){

		$properties['title'] = $tour->get('name');
		$properties['image'] = $tour->get('imgthumb');
		$properties['url'] = $tour->get('pageurl');
		$properties['text'] = $tour->get('shortdescription');


	}else{

		$properties['title'] = "Error";
		$properties['text'] = "No active tour found!";

	}

}else if( $scriptProperties['type'] == 'boat' ){

	// Get a random boat.
	$criteria = $modx->newQuery('seadBoat');
	$criteria->select('seadBoat.*');
	$criteria->where(array(
		'seadBoat.active' => 1
	));
	$criteria->sortby('RAND()');
	$criteria->limit(1); //Limit the results to 1
	$tour = $modx->getObject('seadBoat',$criteria);

	if( !empty($tour) ){

		$properties['title'] = $tour->get('name');
		$properties['image'] = $tour->get('imgthumb');
		$properties['url'] = $tour->get('pageurl');
		$properties['text'] = $tour->get('description');


	}else{

		$properties['title'] = "Error";
		$properties['text'] = "No active tour found!";

	}
}

$contentChunk = $modx->getObject('modChunk',array(
'name' => $tmpl
));

return $contentChunk->process($properties);
