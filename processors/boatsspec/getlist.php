
<?php
/**
 * Gets Tours
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


$spectypes = array(
	'1'	=> 'Boat type',
	'2'	=> 'Electricity',
	'3'	=> 'Diving',
	'4'	=> 'Cabin and Crew ',
	'5'	=> 'Navigation & Communication',
	'6'	=> 'Safety',
	'7'	=> 'Other Special / Facility '		
);

// load manager lexicon
$modx->lexicon->load('sead:manager');

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,50);
$sort = $modx->getOption('sort',$scriptProperties,'typeid,specid');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$onlyactive = $modx->getOption('onlyactive',$scriptProperties,false);
$returnarray = $modx->getOption('returnarray',$scriptProperties,false);  


/* query for bookings */
$c = $modx->newQuery('seadBoatSpecValue');
$c->innerJoin('seadBoatSpec','BoatSpec');
$c->where(array('seadBoatSpecValue.boatid' => $scriptProperties['boatid'] ) );


// get total count
$count = $modx->getCount('seadBoatSpecValue',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadBoatSpecValue`.*,
    `BoatSpec`.name,   
    `BoatSpec`.unit,  
    `BoatSpec`.typeid    
');

// get collection
$tourCollection = $modx->getCollection('seadBoatSpecValue',$c);

  
/* iterate through users */
$list = array();
foreach ($tourCollection as $tourItem) {

	$rateArray = $tourItem->toArray();

	$rateArray['typename'] = $spectypes[$rateArray['typeid']];
	
	if( $rateArray['typename'] != $rateArray['name'] ){

		$rateArray['typename'] = $rateArray['typename'] . " - " . $rateArray['name'];

	}
	
	$list[] = $rateArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor seadBoatSpecValue: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





