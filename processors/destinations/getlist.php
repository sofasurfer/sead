
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
$sort = $modx->getOption('sort',$scriptProperties,'seadDestination.name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$dataType = $modx->getOption('dataType',$scriptProperties,'output');
$groupBy  = $modx->getOption('groupBy',$scriptProperties,false);

/* query for bookings */
$c = $modx->newQuery('seadDestination');
$c->leftJoin('seadCountry','Country');

if (!empty($scriptProperties['query'])) {
    $c->where(array('seadDestination.name:LIKE' => '%'.$scriptProperties['query'].'%'));    
    $c->orCondition(array('seadDestination.code:LIKE' => '%'.$scriptProperties['query'].'%'));
    $c->orCondition(array('Country.name:LIKE' => '%'.$scriptProperties['query'].'%'));

}

if (!empty($scriptProperties['countryid']) && $scriptProperties['countryid'] > 0)  {
    $c->andCondition(array('seadDestination.countryid' => $scriptProperties['countryid'] ) );
}

if (!empty($scriptProperties['typeid']) && $scriptProperties['typeid'] > 0)  {

	$typeId = $scriptProperties['typeid'];
/*
    if( $scriptProperties['typeid'] < 6 ){
	$typeId=1;
    }else{
	$typeId=2;
    }
*/
    $c->andCondition(array('seadDestination.typeid' => $typeId ) );
}

if (!empty($scriptProperties['active']) && $scriptProperties['active'] > 0)  {
    $c->andCondition(array('seadDestination.active' => $scriptProperties['active'] ) );
}

if( !empty($groupBy) ){

	$c->groupBy($groupBy);

}

// get total count
$count = $modx->getCount('seadDestination',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadDestination`.*,
    `Country`.name AS `countryname`
');

// get collection
$destinationCollection = $modx->getCollection('seadDestination',$c);

  
/* iterate through users */
$list = array();

if( !empty( $scriptProperties['showempty'] ) ){
	$list[] = array('id'=>0,'name'=>'-- empty --');
}

foreach ( $destinationCollection as $destinationItem ) {

	$destinationArray = $destinationItem->toArray();

	$destinationArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	$list[] = $destinationArray;
}

// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getDestinations: " . count($list) . " / $count \n" . $c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );


if( $dataType == 'array' ){

	return $modx->error->success('Total destinations: ' . $count,$list);

}else{
	return $this->outputArray($list,$count);
}




