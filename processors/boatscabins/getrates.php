
<?php
/**
 * Gets BoatsCabins
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
$sort = $modx->getOption('sort',$scriptProperties,'typeid');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$onlyactive = $modx->getOption('onlyactive',$scriptProperties,false);
$returnarray = $modx->getOption('returnarray',$scriptProperties,false);  


/* query for bookings */
$c = $modx->newQuery('seadBoatCabin');

if (!empty($scriptProperties['boatid']) )  {
	$c->where(array('seadBoatCabin.boatid' => $scriptProperties['boatid'] ) );
}else{

	return $modx->error->failure("Invalid boat id");

}



// get total count
$count = $modx->getCount('seadBoatCabin',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadBoatCabin`.*
');

// get collection
$collection = $modx->getCollection('seadBoatCabin',$c);

  
/* iterate through users */
$list = array();
foreach ($collection as $item) {

	$itemArray = $item->toArray();

	// check if rate exist

	$itemArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	$list[] = $itemArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getCabinRates: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





