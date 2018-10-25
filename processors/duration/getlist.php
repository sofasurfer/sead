
<?php
/**
 * Gets Duration
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
$sort = $modx->getOption('sort',$scriptProperties,'seadDuration.name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);

/* query for bookings */
$c = $modx->newQuery('seadDuration');


// get total count
$count = $modx->getCount('seadDestination',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadDuration`.*
');

// get collection
$durationCollection = $modx->getCollection('seadDuration',$c);

  
/* iterate through users */
$list = array();

if( !empty( $scriptProperties['showempty'] ) ){
	$list[] = array('id'=>0,'name'=>'-- empty --');
}

foreach ( $durationCollection as $durationItem ) {

	$durationArray = $durationItem->toArray();

	$list[] = $durationArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getDuration: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





