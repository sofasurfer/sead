

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


/**
 * Check if cache file exist
 */

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'seadDestination.name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$menu = $modx->getOption('menu',$scriptProperties,true);
$dataType = $modx->getOption('dataType',$scriptProperties,'output');
$typeId  = $modx->getOption('typeid',$scriptProperties,0);
$countryId  = $modx->getOption('countryid',$scriptProperties,0);
$groupBy  = $modx->getOption('groupBy',$scriptProperties,false);

// get cache files
$cacheKey   = "json/" . $typeId . "_" . $countryId . "_" . $scriptProperties['groupBy'];
$cachedList = $modx->cacheManager->get( $cacheKey );

if( !empty($cachedList) ){

	$list  = $cachedList;
	$count = count($cachedList);

}else{
	/* query for bookings */
	if( $typeId <= 6 ){
		$c = $modx->newQuery('seadDestination');
		$c->innerJoin('seadTour','Tour');
		$c->innerJoin('seadCountry','Country');
		$c->where(array('seadDestination.active' => 1, 'Tour.active' => 1 ) );
	
		// check tour type
		if (!empty($typeId) && $scriptProperties['typeid'] > 0)  {

		    $c->andCondition( array('Tour.typeid' => $typeId ) );
		}

	}else if( $typeId == 7 ){
		$c = $modx->newQuery('seadDestination');
		$c->innerJoin('seadLiveAboard','LiveAboard');
		$c->innerJoin('seadCountry','Country');
		$c->where(array('seadDestination.active' => 1, 'LiveAboard.active' => 1  ) );

	}else if( $typeId == 8 ){
		$c = $modx->newQuery('seadDestination');
		$c->innerJoin('seadHotel','Hotel');
		$c->innerJoin('seadCountry','Country');
		$c->where(array('seadDestination.active' => 1, 'Hotel.active' => 1 ) );
	}

	if ( $countryId > 0)  {
	    $c->andCondition(array('seadDestination.countryid' => $countryId ) );
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
	    `Country`.name AS countryname
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
		$destinationArray['cache'] = time();
		$destinationArray['menu'] = array(
			array(
			    'text' => $modx->lexicon('sead.action_delete'),
			    'handler' => 'this.remove',
			),
		);

		$list[] = $destinationArray;
	}

	// log query  
	$modx->log(modX::LOG_LEVEL_INFO, "Processor getDestinationsByType: " . count($list) . " / $count \n" . $c->toSql() . "\n" . print_r($scriptProperties,true) . "\n" );

	// save list to cache
	$modx->cacheManager->set( $cacheKey, $list );
}

if( $dataType == 'array' ){

	return $modx->error->success('Total destinations: ' . $count,$list);

}else{
	return $this->outputArray($list,$count);
}

