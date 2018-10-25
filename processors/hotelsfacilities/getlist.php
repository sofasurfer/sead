
<?php
/**
 * Gets HotelRates
 *
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 *
 * @package modx
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);


// load manager lexicon
$modx->lexicon->load('sead:manager');

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$onlyactive = $modx->getOption('onlyactive',$scriptProperties,false);
$returnarray = $modx->getOption('returnarray',$scriptProperties,false);  


// Get hotel facilities
$hotalFacilityList = array();
$hotelFacilities = $modx->getCollection('seadHotelFacility', array('hotelid' => $scriptProperties['hotelid']) );

foreach($hotelFacilities as $hotelFacility ){
	$hotalFacilityList[ $hotelFacility->get('facilityid') ] = $hotelFacility->toArray();
}

/* query for hotel facilities */
$c = $modx->newQuery('seadHotelFacilityType');
$c->leftJoin('seadHotelFacility','HotelFacility');


if (!empty($scriptProperties['query'])) {
    $c->where(array('seadHotelFacilityType.name:LIKE' => '%'.$scriptProperties['query'].'%'));      
}

// get total count
$count = $modx->getCount('seadHotelFacilityType',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadHotelFacilityType`.*,
    `HotelFacility`.hotelid,
    `HotelFacility`.facilityid,
    `HotelFacility`.id AS hotelfacilityid
');

// get collection
$facilityCollection = $modx->getCollection('seadHotelFacilityType',$c);

  
/* iterate through users */
$list = array();
foreach ($facilityCollection as $facilityItem) {

	$facilityArray = $facilityItem->toArray();

	// check if facility exist for this hotel
	if( array_key_exists ($facilityArray['id'], $hotalFacilityList) ){
		$facilityArray['checked'] = 1;
		$facilityArray['remarks'] = $hotalFacilityList[$facilityArray['id']]['remarks'];
		$facilityArray['hotelfacilityid'] = $hotalFacilityList[$facilityArray['id']]['id'];
	}else{
		$facilityArray['checked'] = 0;
		$facilityArray['remarks'] = '';
		$facilityArray['hotelfacilityid'] = 0;
	}
	$facilityArray['hotelid'] = $scriptProperties['hotelid'];


	$facilityArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	$list[] = $facilityArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor seadHotelFacilityType: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





