
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
$c = $modx->newQuery('seadTour');


if (!empty($scriptProperties['query'])) {
    $c->where(array('seadTour.name:LIKE' => '%'.$scriptProperties['query'].'%'));    
    $c->orCondition(array('seadTour.code1:LIKE' => '%'.$scriptProperties['query'].'%'));
    $c->orCondition(array('seadTour.code2:LIKE' => '%'.$scriptProperties['query'].'%'));    
    $c->orCondition(array('seadTour.agent:LIKE' => '%'.$scriptProperties['query'].'%'));        
}

if (!empty($scriptProperties['countryid']) && $scriptProperties['countryid'] > 0)  {
    $c->andCondition(array('seadTour.countryid' => $scriptProperties['countryid'] ) );
}

if (!empty($scriptProperties['destinationid']) && $scriptProperties['destinationid'] > 0)  {
    $c->andCondition(array('seadTour.destinationid' => $scriptProperties['destinationid'] ) );
}

if (!empty($scriptProperties['typeid']) && $scriptProperties['typeid'] > 0)  {
    $c->andCondition(array('seadTour.typeid' => $scriptProperties['typeid'] ) );
}


// get total count
$count = $modx->getCount('seadTour',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadTour`.*
');

// get collection
$tourCollection = $modx->getCollection('seadTour',$c);

  
/* iterate through users */
$list = array();
foreach ($tourCollection as $tourItem) {

	$tourArray = $tourItem->toArray();

	// Check for multilingual content
	$nameArray = $modx->fromJSON($tourArray['name']);
	if( !empty($nameArray) ){

		$tourArray['name'] = $nameArray['en'];
	}

	// check if menu is required
	if( $menu ){
		$tourArray['menu'] = array(
			array(
			    'text' => $modx->lexicon('sead.action_update'),
			    'handler' => 'this.update',
			),
			'-',
			array(
			    'text' => $modx->lexicon('sead.action_delete'),
			    'handler' => 'this.remove',
			),
		);
	}

	$list[] = $tourArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getTours: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





