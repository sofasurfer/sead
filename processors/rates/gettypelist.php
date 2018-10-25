
<?php
/**
 * Gets Rates Types
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


/* query for bookings */
$c = $modx->newQuery('seadRateType');


// Check if tourType is set
if( !empty($scriptProperties['ratetype']) ){

	$c->where( array('type'=>$scriptProperties['ratetype']) );
}


// get total count
$count = $modx->getCount('seadRateType',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadRateType`.*
');

// get collection
$typeCollection = $modx->getCollection('seadRateType',$c);

/* iterate through users */
$list = array();


if( !empty( $scriptProperties['showempty'] ) ){
	$list[] = array('typeid'=>0,'name'=>'-- empty --');
}
  

foreach ($typeCollection as $typeItem) {

	$typeArray = $typeItem->toArray();
	$typeArray['typeid'] = $typeArray['id'];
	$list[] = $typeArray;
}
// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getRateType: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





