
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

/**
* Function to convert Hotel Name to a browserfriendly URL
*/
function formatUrl($origName){
	$origName = str_replace("/", "_",$origName);
	$origName = str_replace("-", "_",$origName);	
	$origName = str_replace("$", "",$origName);
	$origName = str_replace("*", "",$origName);
	$origName = str_replace("&", "",$origName);
	$origName = str_replace("+", "",$origName);
	$origName = str_replace(",", "",$origName);
	$origName = str_replace("'", "",$origName);
	$origName = str_replace("`", "",$origName);			
	$origName = str_replace(".", "_",$origName);
	$origName = str_replace("", "",$origName);
	$origName = str_replace(" ", "_",$origName);
	$origName = str_replace("  ", "_",$origName);							
	$origName = str_replace("(", "",$origName);
	$origName = str_replace(")", "",$origName);
	$origName = str_replace("___", "_",$origName);
	$origName = str_replace("__", "_",$origName);
	$origName = strtolower($origName);
	return $origName;
}

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$onlyactive = $modx->getOption('onlyactive',$scriptProperties,false);
$returnarray = $modx->getOption('returnarray',$scriptProperties,false);  
$lang = $modx->getOption('lang',$scriptProperties,'en');

/* query for bookings */
$c = $modx->newQuery('seadLiveAboard');
$c->leftJoin('seadDestination','Destination');


if (!empty($scriptProperties['query'])) {
	$c->where(array('seadLiveAboard.name:LIKE' => '%'.$scriptProperties['query'].'%'));    
	$c->orCondition(array('seadLiveAboard.code:LIKE' => '%'.$scriptProperties['query'].'%'));
}

if (!empty($scriptProperties['boatid']) && $scriptProperties['boatid'] > 0)  {
	$c->andCondition(array('seadLiveAboard.boatid' => $scriptProperties['boatid'] ) );
}
if (!empty($scriptProperties['countryid']) && $scriptProperties['countryid'] > 0)  {
	$c->andCondition(array('seadLiveAboard.countryid' => $scriptProperties['countryid'] ) );
}
if (!empty($scriptProperties['destinationid']) && $scriptProperties['destinationid'] > 0)  {
	$c->andCondition(array('seadLiveAboard.destinationid' => $scriptProperties['destinationid'] ) );
}

// get total count
$count = $modx->getCount('seadLiveAboard',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
	`seadLiveAboard`.*,
	`Destination`.name AS destinationname
');

// get collection
$tourCollection = $modx->getCollection('seadLiveAboard',$c);


/* iterate through users */
$list = array();
foreach ($tourCollection as $tourItem) {
	
	$tourArray = $tourItem->toArray();
	
	$tourArray['datestart'] = date("d.m.Y",$tourArray['datestart']);
	$tourArray['dateend'] = date("d.m.Y",$tourArray['dateend']);	

	// get name
	$nameArray = $modx->fromJSON($tourArray['name']);
	if( !empty($nameArray) ){
		$tourArray['name'] = $nameArray[$lang];
	}
	$tourArray['fullname'] = $tourArray['destinationname'] . " / " . $tourArray['name'];

	$boat = $tourItem->getOne('Boat');
	if ($boat) {
		$boatNameArray = $modx->fromJSON($boat->get('name'));
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $boatNameArray[$lang]);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '_', $clean);
		
		if( 1==1 || $tourArray['nameurl'] == "" ){
			$tourArray['nameurl'] = formatUrl( $tourArray['name'] );
			$newurlname = $tourArray['nameurl']."_".$clean ;
			$tourItem->set('nameurl', $newurlname);
			$tourItem->save();		
		}
	}
	
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
//$modx->log(modX::LOG_LEVEL_INFO, "Processor seadLiveAboard: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);
