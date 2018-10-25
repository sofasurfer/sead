
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

// load manager lexicon
$modx->lexicon->load('sead:manager');

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'datestart,sortorder');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$onlyactive = $modx->getOption('onlyactive',$scriptProperties,false);
$returnarray = $modx->getOption('returnarray',$scriptProperties,false);  


/* query for bookings */
$c = $modx->newQuery('seadRate');
$c->leftJoin('seadRateType','RateType');

if (!empty($scriptProperties['productcode']) )  {
	$c->where(array('seadRate.productcode' => $scriptProperties['productcode'] ) );
}else{

	return $modx->error->failure("Invalid product code");

}

if (!empty($scriptProperties['query'])) {
    $c->andCondition(array('seadRate.price:LIKE' => '%'.$scriptProperties['query'].'%'));    
}

if (!empty($scriptProperties['typeid']) && $scriptProperties['typeid'] > 0)  {
    $c->andCondition(array('seadRate.typeid' => $scriptProperties['typeid'] ) );
}


// get total count
$count = $modx->getCount('seadRate',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadRate`.*,
    `RateType`.sortorder,
    `RateType`.name AS `typename`
');

// get collection
$tourCollection = $modx->getCollection('seadRate',$c);

  
/* iterate through users */
$list = array();
foreach ($tourCollection as $tourItem) {

	$rateArray = $tourItem->toArray();
	$rateArray['datestart'] = date("d.m.Y",$rateArray['datestart']);
	$rateArray['dateend'] = date("d.m.Y",$rateArray['dateend']);	

	$rateArray['season'] = $rateArray['datestart'] . "-" . $rateArray['dateend'];


	if( !empty($CABINTYPES) ){
		$rateArray['typename'] = $CABINTYPES[ $rateArray['typeid'] ];
	}

	$rateArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	$list[] = $rateArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getRates: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





