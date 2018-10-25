
<?php
/**
 * Gets Liveaboards
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
$c = $modx->newQuery('seadHotel');
$c->leftJoin('seadDestination','Destination');


if (!empty($scriptProperties['query'])) {
    $c->where(array('seadHotel.name:LIKE' => '%'.$scriptProperties['query'].'%'));    
    $c->orCondition(array('seadHotel.code:LIKE' => '%'.$scriptProperties['query'].'%'));
}
if (!empty($scriptProperties['starrating']) && $scriptProperties['starrating'] > 0)  {
    $c->andCondition(array('seadHotel.stars' => $scriptProperties['starrating'] ) );
}
if (!empty($scriptProperties['countryid']) && $scriptProperties['countryid'] > 0)  {
    $c->andCondition(array('seadHotel.countryid' => $scriptProperties['countryid'] ) );
}
if (!empty($scriptProperties['destinationid']) && $scriptProperties['destinationid'] > 0)  {
    $c->andCondition(array('seadHotel.destinationid' => $scriptProperties['destinationid'] ) );
}

// get total count
$count = $modx->getCount('seadHotel',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadHotel`.*,
    `Destination`.name AS destinationname
');

// get collection
$tourCollection = $modx->getCollection('seadHotel',$c);

  
/* iterate through users */
$list = array();
foreach ($tourCollection as $tourItem) {

	$tourArray = $tourItem->toArray();
	 
	// check if menu is required
	if( $menu ){
		$tourArray['menu'] = array(
			array(
			    'text' => $modx->lexicon('sead.action_update'),
			    'handler' => 'this.update'
			),
                        array(
                            'text' => $modx->lexicon('sead.action_copy'),
                            'handler' => 'this.copy'
                        ),

			'-',
			array(
			    'text' => $modx->lexicon('sead.action_delete'),
			    'handler' => 'this.remove'
			),
		);
	}

	$list[] = $tourArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor seadHotel: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





