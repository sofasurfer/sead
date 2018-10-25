
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

/**
 * Function to convert Hotel Name to a browserfriendly URL
 */
function formatUrl($origName){
	$origName = str_replace("/", "_",$origName);
	$origName = str_replace("-", "_",$origName);	
	$origName = str_replace("$", "",$origName);
	$origName = str_replace("*", "",$origName);
	$origName = str_replace("&", "",$origName);
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

// load manager lexicon
$modx->lexicon->load('sead:manager');

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'seadBoat.name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$lang = $modx->getOption('lang',$scriptProperties,'en');

/* query for bookings */
$c = $modx->newQuery('seadBoat');

if (!empty($scriptProperties['query'])) {
    $c->where(array('seadBoat.name:LIKE' => '%'.$scriptProperties['query'].'%'));    
    $c->orCondition(array('seadBoat.code:LIKE' => '%'.$scriptProperties['query'].'%'));
}

if (!empty($scriptProperties['countryid']) && $scriptProperties['countryid'] > 0)  {
    $c->andCondition(array('seadBoat.countryid' => $scriptProperties['countryid'] ) );
}


// get total count
$count = $modx->getCount('seadBoat',$c);

// sort
$c->sortby($sort,$dir);

// limit for pageing
if ($isLimit) $c->limit($limit,$start);

// select fields
$c->select('
    `seadBoat`.*
');

// get collection
$boatCollection = $modx->getCollection('seadBoat',$c);

  
/* iterate through users */
$list = array();

if( !empty( $scriptProperties['showempty'] ) ){
	$list[] = array('id'=>0,'name'=>'-- empty --');
}

foreach ( $boatCollection as $boatItem ) {

	$boatArray = $boatItem->toArray();

	// Check for multilingual content
	$nameArray = $modx->fromJSON($boatArray['name']);
	if( empty($nameArray) ){
		$nameArray = array();
		$nameArray['de'] = $boatArray['name'];
		$nameArray['en'] = $boatArray['name'];
		$boatItem->set('name',$modx->toJson($nameArray) );
		$boatItem->save();
	}
	$boatArray['name'] = $nameArray[ $lang ];

	if( $boatArray['nameurl'] == "" ){
		$boatArray['nameurl'] = formatUrl( $boatArray['name'] );
		$boatItem->set('nameurl', $boatArray['nameurl']);
		$boatItem->save();		
	}

	$descArray = $modx->fromJSON($boatArray['description']);
	if( empty($descArray) ){
		$descArray = array();
		$descArray['de'] = $boatArray['description'];
		$descArray['en'] = $boatArray['description'];
		$boatItem->set('description',$modx->toJson($descArray) );
		$boatItem->save();
	}
	$boatArray['description'] = $descArray[ $lang ];

	$descArray = $modx->fromJSON($boatArray['shortdescription']);
	if( empty($descArray) ){
		$descArray = array();
		$descArray['de'] = $boatArray['shortdescription'];
		$descArray['en'] = $boatArray['shortdescription'];
		$boatItem->set('shortdescription',$modx->toJson($descArray) );
		$boatItem->save();
	}
	$boatArray['shortdescription'] = $descArray[ $lang ];
	
	$boatArray['menu'] = array(
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

	$list[] = $boatArray;
}


// log query  
//$modx->log(modX::LOG_LEVEL_INFO, "Processor getBoats: " . count($list) . " / $count \n" .$c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

return $this->outputArray($list,$count);





