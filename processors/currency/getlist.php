
<?php
/**
 * Gets Currencies
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
$sort = $modx->getOption('sort',$scriptProperties,'seadCurrency.name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);

/* query for bookings */
$c = $modx->newQuery('seadCurrency');

if (!empty($scriptProperties['query'])) {
    $c->where(array('seadCurrency.name:LIKE' => '%'.$scriptProperties['query'].'%'));    

}


// get total count
$count = $modx->getCount('seadCurrency',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadCurrency`.*
');

// get collection
$currencyCollection = $modx->getCollection('seadCurrency',$c);

  
/* iterate through users */
$list = array();
if( !empty( $scriptProperties['showempty'] ) ){
	$list[] = array('code'=>'','name'=>'-- empty --');
}
foreach ( $currencyCollection as $currencyItem ) {

	$currencyArray = $currencyItem->toArray();
	$currencyArray['name_long'] = $currencyArray['name'] . " (". $currencyArray['code'] . ")";
	$currencyArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	$list[] = $currencyArray;
}
return $this->outputArray($list,$count);





