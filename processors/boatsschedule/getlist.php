
<?php
/**
 * Get BoatsSchedule
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
$limit = $modx->getOption('limit',$scriptProperties,50);
$sort = $modx->getOption('sort',$scriptProperties,'datestart');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$onlyactive = $modx->getOption('onlyactive',$scriptProperties,false);
$returnarray = $modx->getOption('returnarray',$scriptProperties,false);  
$languageCode = $modx->getOption('cultureKey','en');

/* query for schedule */
$c = $modx->newQuery('seadBoatSchedule');
$c->leftJoin('seadLiveAboard','LiveAboard');

$c->where(array('seadBoatSchedule.boatid' => $scriptProperties['boatid'] ) );

if( !empty($scriptProperties['liveaboardid']) && $scriptProperties['liveaboardid'] > 0 ){
	
	$c->andCondition(array('seadBoatSchedule.liveaboardid' => $scriptProperties['liveaboardid'] ) );
	
}

// get total count
$count = $modx->getCount('seadBoatSchedule',$c);

//$modx->log(modX::LOG_LEVEL_INFO, "Processor GET seadBoatSchedule: " . $c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadBoatSchedule`.*,
    `LiveAboard`.destinationid,
    `LiveAboard`.name AS tourname
');

// get collection
$scheduleCollection = $modx->getCollection('seadBoatSchedule',$c);

  
/* iterate through users */
$list = array();
foreach ($scheduleCollection as $scheduleItem) {

	$scheduleArray = $scheduleItem->toArray();
	
	// Check for multilingual content
	$nameArray = $modx->fromJSON($scheduleArray['tourname']);
	if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){
		$scheduleArray['tourname'] = $nameArray[$languageCode];
	}
		
	if( $scheduleArray['datestart'] > 0 ){
		$scheduleArray['datestart'] = date("d.m.Y",$scheduleArray['datestart']);
	}else{
		$scheduleArray['datestart'] = "";
	}
	if( $scheduleArray['dateend'] > 0 ){
		$scheduleArray['dateend'] = date("d.m.Y",$scheduleArray['dateend']);	
	}else{
		$scheduleArray['dateend'] = "";
	}

	$destination = $modx->getObject('seadDestination', $scheduleArray['destinationid']);
	if( !empty($destination) ){
		$scheduleArray['destinationname'] = $destination->get('name') . " / " . $scheduleArray['tourname'];
	}else{
		$scheduleArray['destinationname'] = '';
	}
	
	$scheduleArray['menu'] = array(
		array(
		    'text' => $modx->lexicon('sead.action_updateliveaboard'),
		    'handler' => 'this.updateLiveaboard',
		),
		array(
		    'text' => $modx->lexicon('sead.action_delete'),
		    'handler' => 'this.remove',
		),
	);

	
	$list[] = $scheduleArray;
}

/* Check CONTEXT */
if( $modx->context->key == 'mgr' ){
    return $this->outputArray($list,$count);
}else{
    return $modx->error->success('',array('total'=>$count,"rows"=>$list));
}





