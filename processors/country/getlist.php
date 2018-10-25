
<?php
/**
 * Gets DEstinations
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
$sort = $modx->getOption('sort',$scriptProperties,'seadCountry.name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$dataType = $modx->getOption('dataType',$scriptProperties,'output');

/* query for bookings */
$c = $modx->newQuery('seadCountry');

if (!empty($scriptProperties['query'])) {
    $c->where(array('seadCountry.name:LIKE' => '%'.$scriptProperties['query'].'%'));    
    $c->orCondition(array('seadCountry.code:LIKE' => '%'.$scriptProperties['query'].'%'));
}

// get total count
$count = $modx->getCount('seadCountry',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadCountry`.*
');

// get collection
$destinationCollection = $modx->getCollection('seadCountry',$c);

  
/* iterate through users */
$list = array();

if( !empty( $scriptProperties['showempty'] ) ){
	$list[] = array('id'=>0,'name'=>'-- empty --');
}

foreach ( $destinationCollection as $destinationItem ) {

	$countryArray = $destinationItem->toArray();

	$countryArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	$list[] = $countryArray;
}

// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getCountry List: " . count($list) . " / $count \n" . $c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );


if( $dataType == 'array' ){

	return $modx->error->success('Total destinations: ' . $count,$list);

}else{
	return $this->outputArray($list,$count);
}




