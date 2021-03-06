
<?php

/** SEAD Products
 * 
 * Handels all Product Data
 *
 *
 * @package SEAD data class
 */ 

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('Asia/Singapore');

/**
 * Add SEAD Data information package
 */
$modx->addPackage('SEAD',MODX_CORE_PATH.'components/sead/model/','sead_');

/* Load Lexicon */
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load('sead:default');


// get DG include file
require_once ( $modx->getOption('core_path') .'config/sead.conf.php');

$LANGUAGES = array(
	'1' => 'English',
	'2' => 'German'	
);

$WEEKDAYS = array(
	'1' => 'Daily',
	'2' => 'Monday',	
	'3' => 'Tuesday',
	'4' => 'Wednesday',
	'5' => 'Thursday',
	'6' => 'Friday',
	'7' => 'Saturday',
	'8' => 'Sunday',	
	'9' => 'By Schedule'						
);

$THEMES = array(
	'1' => 'Adventure',
	'2' => 'Classic',	
	'3' => 'Cruises',
	'4' => 'Culture',
	'5' => 'History',
	'6' => 'Free & easy',	
	'7' => 'Scuba Diving',
	'8' => 'Courses'
);

$SEGMENTS = array(
	'1' => 'Family',
	'2' => 'Seniors',	
	'3' => 'Children',
	'4' => 'Check Prerequisits'
);


$SPECTYPES = array(
	'1'	=> 'Boat type',
	'2'	=> 'Electricity',
	'3'	=> 'Diving',
	'4'	=> 'Cabin and Crew ',
	'5'	=> 'Navigation & Communication',
	'6'	=> 'Safety',
	'7'	=> 'Other Special / Facility '		
);

$CABINTYPES = array(
	'1'	=> 'VIP Cabin',
	'7'	=> 'Master Cabin',
	'2'	=> 'Deluxe Double',
	'3'	=> 'Standard Cabin',
	'9'	=> 'Single Bed Cabin',
	'4'	=> 'Double Bed Cabin',
	'5'	=> 'Twin Bed Cabin',
	'10'	=> 'Triple Bed Cabin',
	'6'	=> '4- Bed Cabin',
	'8'	=> 'Bunk Bed Cabin'
);

$PRODUCTS = array(

	'1' => 'Day/Tour(s)',
	'2' => 'Overnight/Tour(s)',
	'3' => 'Diving Day/Tour(s)',
	'4' => 'Diving Course(s)',
	'5' => 'Diving Resort(s)',
	'6' => 'Sailing Charter(s)',
	'7' => 'Liveaboard(s)',
	'8' => 'Hotel(s)'

);


$ROOMCATEGORIES = array(
	'1' => 'Classic',
	'2' => 'Deluxe',
	'3' => 'Executive',
	'4' => 'Junior Suite',
	'5' => 'Room Only',
	'6' => 'Standard',
	'7' => 'Studio',
	'8' => 'Suite',
	'9' => 'Superior'
);

$ROOMTYPES = array(
	'1' => 'Single',
	'2' => 'Double / Twin',
	'5' => 'Triple',
	'6' => 'Famili Room',
	'7' => 'Quad'
);

$languageCode = $modx->getOption('cultureKey');

// set cache key
define('CACHE_KEY', "/bookings/" . session_id() );

// check fo custom types

if( !empty($scriptProperties['type']) && $scriptProperties['type'] == 'diveResorts' ){

	$scriptProperties['type'] = 'tours';
	$scriptProperties['typeid'] = '5';
	$scriptProperties['tmpl'] = "TourList";
	$scriptProperties['tmplrow'] = 'TourListItemResort';

}else if( !empty($scriptProperties['type']) && $scriptProperties['type'] == 'diveCourses' ){

	$scriptProperties['type'] = 'tours';
	$scriptProperties['typeid'] = '4';
	$scriptProperties['tmpl'] = "TourList";
	$scriptProperties['tmplrow'] = 'TourListItem';
}


// check product type
if( $scriptProperties['type'] == 'search' ){

	$bookingInfo = $_GET;

	//print_r($bookingInfo);

	// get desination
	$destination = $modx->getObject('seadDestination',$bookingInfo['destination']);

	$modx->toPlaceholders($bookingInfo);

	// check tour type
	if($bookingInfo['trip'] <= 6 ){

		$c = $modx->newQuery('seadTour');
		$c->innerJoin('seadRate','Rate');
		$c->innerJoin('seadDuration','Duration');
		$c->where(array(
			'seadTour.active' => 1, 
			'seadTour.typeid' => $bookingInfo['trip'], 
			'seadTour.destinationid' => $bookingInfo['destination']));  

		if( !empty( $bookingInfo['arrival']) && !empty( $bookingInfo['departure']) ){

			$c->andCondition(array( 
				'Rate.datestart:<=' => ($bookingInfo['arrival']/1000)-86400
				,'Rate.dateend:>=' => ($bookingInfo['departure']/1000)+86400
			));
		}

		if( !empty($bookingInfo['children']) &&  $bookingInfo['children'] > 0 ){
			$c->andCondition(array('Rate.typeid' => 6, 'Rate.price:>' => 0 ));  
		}

		// get selected duration
		if( !empty($bookingInfo['arrival']) && !empty($bookingInfo['departure']) ){
			$days = ((($bookingInfo['departure']-$bookingInfo['arrival'])/1000)/86400);
			$c->andCondition(array('Duration.days:<=' => intval($days+1) ));  
		}


		$c->sortby("seadTour.duration","ASC"); 
		$c->select('
		    `seadTour`.*,
		    `seadTour`.duration as durationid,
		    `Rate`.price,
		    `Rate`.typeid as ratetypeid
		');
		$bookingInfo['total'] = $modx->getCount('seadTour',$c);
		$bookingInfo['listItem'] = "SearchResultTourItem";
		$results = $modx->getCollection('seadTour', $c );
			
	/* Search LiveAboard */
	}else if($bookingInfo['trip'] == 7 ){

		$c = $modx->newQuery('seadBoatSchedule');
		$c->leftJoin('seadLiveAboard','LiveAboard');
		$c->leftJoin('seadBoat','Boat');
		
		$c->where(array(
			'LiveAboard.destinationid' => $bookingInfo['destination'],
			'seadBoatSchedule.datestart:>' => ($bookingInfo['arrival']/1000)-86400,
			'seadBoatSchedule.dateend:<' => ($bookingInfo['departure']/1000)+86400
		)); 

		$c->groupby("Boat.id"); 
		$c->sortby("Boat.name,LiveAboard.name","DESC"); 
		$c->select('
		    `LiveAboard`.*,
		    `Boat`.name as boatname,
		    `LiveAboard`.duration as durationid
		');
		$bookingInfo['listItem'] = "SearchResultLiveaboardItem";
		$bookingInfo['total'] = $modx->getCount('seadBoatSchedule',$c);
		$results = $modx->getCollection('seadBoatSchedule', $c );


	/* Search Hotels */		
	}else if($bookingInfo['trip'] == 8 ){

		$c = $modx->newQuery('seadHotel');
		$c->where(array('seadHotel.active' => 1, 'seadHotel.destinationid' => $bookingInfo['destination']));  

		$c->sortby("seadHotel.name","ASC"); 
		$c->select('
		    `seadHotel`.*
		');
		$bookingInfo['total'] = $modx->getCount('seadHotel',$c);
		$bookingInfo['listItem'] = "SearchResultHotelItem";
		$results = $modx->getCollection('seadHotel', $c );
	}

	//echo $c->toSql();

	$bookingInfo['productname'] = $PRODUCTS[$bookingInfo['trip']];

	// HTML List
	$bookingInfo['results'] = "<div>";
	$counter = 1;
	if( !empty($results) ){
		foreach( $results as $result ){

			$listArray = $result->toArray();

			// check tour type
			if( intval($bookingInfo['trip']) <= 6 ){
				$listArray['url'] = $result->get('pageurl');
			}else if( intval($bookingInfo['trip']) == 7 ){
				$listArray['url'] = "/liveaboard-detail.html?lid=" . $result->get('id');
			}else if( intval($bookingInfo['trip']) == 8 ){
				$listArray['url'] = "/hotels/hotel-detail.html?hid=" . $result->get('id');
			}

			// get duration
			$duration = $modx->getObject('seadDuration',$result->get('durationid') );
			if( !empty($duration) ){
				$listArray['durationname'] = $duration->get('name');
			}else{
				$listArray['durationname'] = '';	
			}

			// get destination
			$destination = $modx->getObject('seadDestination',$bookingInfo['destination']);
			if( !empty($destination) ){
				$listArray['destinationname'] = $destination->get('name');
			}else{
				$listArray['destinationname'] = 'Destination not found.';	
			}

			// Check for multilingual content
			if( !empty($listArray['name']) ){
				$nameArray = $modx->fromJSON($listArray['name']);
				if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

					$listArray['name'] = $nameArray[$languageCode];
				}
			}
			if( !empty($listArray['shortdescription']) ){
				$nameArray = $modx->fromJSON($listArray['shortdescription']);
				if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

					$listArray['shortdescription'] = $nameArray[$languageCode];
				}
			}

			// get list item chunk
			$itemChunk = $modx->getObject('modChunk',array(
			    'name' => $bookingInfo['listItem']
			));

			$listArray['counter'] = $counter;

			$bookingInfo['results'] .= $itemChunk->process( $listArray );
			$counter++;
		}
	}else{
		$bookingInfo['results'] .= "<h3>" . $modx->lexicon('quick_search_noresults',array(
				'dates' => date("d.m.Y",$bookingInfo['arrival']/1000 ) . " - " .  date("d.m.Y",$bookingInfo['departure']/1000 )
		 )) . "</h3>";	
	}
	$bookingInfo['results'] .= "</div>";
	$bookingInfo['total'] = $counter-1;


	//print_r($bookingInfo);

	// get tour list chunk
	$searchChunk = $modx->getObject('modChunk',array(
	    'name' => 'search'
	));
		            

	$modx->toPlaceholders( $bookingInfo );

	// proccess and return booking chunk
	return $searchChunk->process( $bookingInfo );


}else if( $scriptProperties['type'] == 'basket' ){

	// get bookings from cache
	$bookingList = $modx->cacheManager->get(CACHE_KEY);
	$bookingBasket = "";

	if( !empty($bookingList) && count($bookingList) > 0 ){
		$bookingBasket .= "<div class=\"bookingBasket\">";
		$bookingBasket .= "<h2>My Trip Details</h2><ul>";
		foreach( $bookingList as $bookingItem ){

			$bookingBasket .= "<li><a href=\"" . $bookingItem['url'] . "\">" . $bookingItem['name'] . "</a></li>";
		
			foreach( $bookingItem['rates'] as $rateItem ){
				$bookingTotal += $rateItem['priceTotal'];
			}
		}
		$bookingBasket .= "</ul><h3>Total: " . number_format($bookingTotal,0) . " " . $rateItem['currency']."</h3>";
		$bookingBasket .= "<input type=\"button\" onclick=\"document.location='/booking.html?checkout=1';\" class=\"button\" value=\"Check Out\" /></div>";
	}

	return $bookingBasket;
	
}else if( $scriptProperties['type'] == 'booking' ){

	// get bookings from cache
	$bookingList = $modx->cacheManager->get(CACHE_KEY);


	$publicFiels['cachekey'] = CACHE_KEY;
	$modx->toPlaceholders($publicFiels);


	// check if booking items has to be deleted
	if( !empty($_GET) && !empty($_GET['delete']) ){

		$deleteItems = explode(":",$_GET['delete']);

		foreach( $deleteItems as $deleteItem ){
			$delArray = explode("_",$deleteItem);

			unset($bookingList[$delArray[0]]['rates'][$delArray[1]]);	

			if( count($bookingList[$delArray[0]]['rates']) < 1 ){
				unset($bookingList[$delArray[0]]);	
			}

		}

		// save to cache
		$modx->cacheManager->set(CACHE_KEY,$bookingList);

	// check for start date update
	}else if( !empty($_GET) && !empty($_GET['setDate']) ){

		$newDate = explode(":",$_GET['setDate']);

		// get current pax
		$bookingList[$newDate[0]]['rates'][$newDate[1]]['tripdatestart'] = intval($newDate[2]/1000);

		// save to cache
		$modx->cacheManager->set(CACHE_KEY,$bookingList);

	// check for adults update
	}else if( !empty($_GET) && !empty($_GET['setPax']) ){

		$paxItems = explode(":",$_GET['setPax']);

		// get current pax
		$paxCurrent = $bookingList[$paxItems[0]]['rates'][$paxItems[1]]['pax'];

		// get price/pax
		if( $paxCurrent > 1 ){
			$pricePax = ($bookingList[$paxItems[0]]['rates'][$paxItems[1]]['priceTotal']/$paxCurrent);
		}else{
			$pricePax = ($bookingList[$paxItems[0]]['rates'][$paxItems[1]]['priceTotal']);
		}

		$bookingList[$paxItems[0]]['rates'][$paxItems[1]]['pax'] = $paxItems[2];
		$bookingList[$paxItems[0]]['rates'][$paxItems[1]]['priceTotal'] = ($pricePax*$paxItems[2]);

		// save to cache
		$modx->cacheManager->set(CACHE_KEY,$bookingList);

	// check if booking information are added
	}else if( !empty($_GET) ){

		$bookingObject = $_GET;
		$bookingObject['currency'] = "EUR";
		$bookingObject['rates'] = explode(";",urldecode($bookingObject['rates']));

		// get rates by booking id
		$productCodeOld = "";
		foreach( $bookingObject['rates'] as $bookingItem ){
		
			// get rate item
			$rateItemArray = explode(":",$bookingItem);
			$rateItem = $modx->getObject('seadRate',$rateItemArray[2]);

			if( !empty($rateItem) ){

				// Set rate array
				$rateArray = $rateItem->toArray();
				$productCode = $rateItem->get('productcode');

				/**
				 * Get product id new
				 */
				if( $productCodeOld != $productCode ){

					// get product
					if( $rateItemArray[0] == 'tour' ){
						$product = $modx->getObject('seadTour', array( 'productcode' => $productCode ));
						$productId = $product->get('id');
						$bookingList[$productId]['code'] = $product->get('code1');
						$bookingList[$productId]['url'] = $product->get('pageurl');
						//$bookingList[$productId]['name'] = $productName;


					}else if( $rateItemArray[0] == 'liveaboard' ){

						//$product = $modx->getObject('seadLiveAboard', array( 'productcode' => $productCode ));
						$schedule = $modx->getObject('seadBoatSchedule', array( 'id' => $rateItemArray[1] ));
						$product = $schedule->getOne('LiveAboard');
						$productId = $product->get('id');
						$rateArray['boatid']= $product->get('boatid');
						$bookingList[$productId]['code'] = $product->get('code');
						$bookingList[$productId]['url'] = "/liveaboard-detail.html?lid=" . $product->get('id');
						//$bookingList[$productId]['name'] = $productName . " - " . $product->get('boatid');
					}
					$nameArray = $modx->fromJSON($product->get('name'));
					if( !empty($nameArray) ){
						$bookingList[$productId]['name'] = $nameArray['en'];
					}
					$bookingList[$productId]['id'] = $product->get('id');

				}
				$rateArray['productId'] = $productId;
				$rateArray['productType'] = $product->get('typeid');
				$rateArray['type'] = $rateItemArray[0];
				$rateArray['tripdatestart'] = false;
				$rateArray['flexdate'] = true;
				$rateArray['flexpax'] = true;


				// Check for multilingual content
				$nameArray = $modx->fromJSON( $bookingList[$productId]['name'] );
				if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

					$bookingList[$productId]['name'] = $nameArray[$languageCode];
				}


				$sDeskArray = $modx->fromJSON($bookingList[$productId]['shortdescription']);
				if( !empty($sDeskArray) && !empty($sDeskArray[$languageCode])  ){

					$bookingList[$productId]['shortdescription'] = $sDeskArray[$languageCode];
				}

				/**
				 * Get duration
				 */

				// check if 'liveaboard' MV South Siam
				if( $rateArray['type'] == 'liveaboard' && $rateArray['boatid'] == 14 ){

					$rateArray['datestart'] = $schedule->get('datestart');
					$rateArray['dateend'] = $schedule->get('dateend');
					$rateArray['days'] = $rateItemArray[4];
					$rateArray['duration'] = $rateArray['days'] . " days / " . intval($rateArray['days']-1) . " nights *";

				// Check if liveaboard
				}else if($rateArray['type'] == 'liveaboard'){

					$rateArray['flexdate'] = false;
					$rateArray['tripdatestart'] = $schedule->get('datestart');
					$nights = round(( $schedule->get('dateend')-$schedule->get('datestart') ) / 86400);
					$rateArray['days']  = round($nights+1);
					$rateArray['duration'] = $rateArray['days'] . " days / " . intval($rateArray['days']-1) . " nights";

				// Check if Sailing
				}else if($rateArray['productType'] == 6 ){

					$rateArray['flexpax'] = false;
					$rateArray['days'] = $rateItemArray[4];
					if( $rateArray['days'] > 1 ){
						$rateArray['duration'] = $rateArray['days'] . " days *";
					}else{
						$rateArray['duration'] = $rateArray['days'] . " x day 24hrs *";
					}

				// check if duration is set and dive resort
				}else if( $rateItemArray[4] > 0 && $rateArray['productType'] == 5 ){

					$rateArray['days'] = $rateItemArray[4];
					$rateArray['duration'] = intval($rateArray['days']+1) . " days / " . intval($rateArray['days']) . " nights *";

				// check if duration is set
				}else if( $rateItemArray[4] > 0 && $rateArray['type'] == 'tour' ){

					$rateArray['days'] = $rateItemArray[4];
					$rateArray['duration'] = $rateArray['days'] . " days / " . intval($rateArray['days']-1) . " nights *";

				// Check if tour
				}else if( $rateArray['type'] == 'tour'){

					// get duration by rate
					$rateDuration = $rateItem->getOne('Duration');
					if( empty($rateDuration) || ( !empty($rateDuration) && $rateDuration->get('days') < 1 ) ){

						// get duration by tour
						$productDuration = $product->getOne('Duration');
						$rateDuration = $productDuration;
					}
					if( !empty($rateDuration) ){
						$rateArray['days'] = $rateDuration->get('days');
						$rateArray['duration'] = $rateDuration->get('name'). "";
					}

				}

				/**
				 * Get rate type
				 */
				if( $rateItemArray[0] == 'tour' ){

					$rateType = $rateItem->getOne('RateType');

					// check if join in tour adults
					if( $rateType->get('id') == 5){

						if( $rateItemArray[3] > 1){
							$rateArray['typename'] = "Adults";
						}else{
							$rateArray['typename'] = "Adult";
						}
						$rateArray['pax'] = $rateItemArray[3];

					// check if join in tour children
					}else if( $rateType->get('id') == 6 ){
						if( $rateItemArray[3] > 1){
							$rateArray['typename'] = "Children";
						}else{
							$rateArray['typename'] = "Child";
						}
						$rateArray['pax'] = $rateItemArray[3];

					// check for private tours
					}else if( $rateType->get('id') <= 4 ){

						$rateArray['typename'] = $rateType->get('name');
						preg_match('/[0-9]/', $rateArray['typename'], $matches);
						$rateArray['pax'] = $matches[0];
						$rateArray['typename'] = $rateArray['pax'] . " x " . str_replace($matches[0],"",$rateArray['typename']);
						
						// Number of quests can't be changed
						$rateArray['flexpax'] = false;

					// else set default values
					}else{
						$rateArray['typename'] = $rateType->get('name');
						$rateArray['pax'] = 1;
					}
				}else{

					$rateArray['typename'] = $CABINTYPES[$rateItem->get('typeid')];
					$rateArray['pax'] = 1;
				}

				/**
				 * Get rates
				 */
				$price = $rateItem->get('price');

				// check for markup
				if( $product->get('markup1') > 0 ){
					$price = ($price + (($price/100)*$product->get('markup1')));
				}
				if( $product->get('markup2') > 0 ){
					$price = ($price + $product->get('markup2'));
				}

				// check if currency is different
				if( $rateItem->get('currency') != $bookingObject['currency'] ){
				
					$currencyObject = $modx->getObject('seadCurrency',array( 'code' => $rateItem->get('currency') ) );
					$currencyRate = $currencyObject->get( strtolower($bookingObject['currency']) );
					$price = ($price*($currencyRate));
				}


				// Multiply Person
				$priceTotal = ($price*$rateArray['pax']);


				// Multiply duration for Private 
				if( ( $product->get('typeid') == 5 || $product->get('typeid') == 6 ) && $rateItem->get('duration') == 15 ){
					$priceTotal = ($price*$rateArray['days']);

				// Multiply duration for liveaboard
				}else if($rateItemArray[0] == 'liveaboard'){
					$priceTotal = ($price*$rateArray['days']);
				}
				$priceTitle .= " Total: = " . $priceTotal . " " . $bookingObject['currency'];

				// round up price
				$rateArray['price'] 	 = ceil($price);
				$rateArray['priceTotal'] = ceil($priceTotal);
				$rateArray['priceTitle'] = $priceTitle;
				$rateArray['currency'] 	 = $bookingObject['currency'];

				// Add Rate to List
				$bookingList[$productId]['rates'][$rateItem->get('id')] = $rateArray;

				// set product code
				$productCodeOld = $productCode;
			}
		}

		// save to cache
		$modx->cacheManager->set(CACHE_KEY,$bookingList);

	}

	//print_r($bookingList);	

	// generate booking list
	$retHtml = "";
	$bookingTotal = 0;

	if( !empty($bookingList) && count($bookingList) > 0 ){
		foreach( $bookingList as $bookingItem ){


			$retHtml .= "<h2 style=\"padding:0;margin-top:20px;margin-bottom:5px;\"><a href=\"" . $bookingItem['url'] . "\">" . $bookingItem['name'] . " <span style=\"font-size:12px\">(" . $bookingItem['code']  .  ")</span></a></h2>";
		
			$retHtml .= "<table>";
			$retHtml .= "<tr><th>Rate Type</th><th>Start Date</th><th style=\"text-align:center;\">Duration</th><th style=\"text-align:right;\">Price </th><th></th></tr>";
			foreach( $bookingItem['rates'] as $rateItem ){

				// check for start dates
				if( !empty($rateItem['tripdatestart']) ){
					$startDate = date("d M Y",$rateItem['tripdatestart']);
				}else{
					$startDate = "";
				}

				// Check if start date can change
				if( empty($rateItem['flexdate']) ){
					$startDateValue = $startDate;
				}else{
					$titleSeason = "Season: " .  date("d M Y",$rateItem['datestart']) . " - " . date("d M Y",$rateItem['dateend']);
					$startDateValue = "<input title=\"$titleSeason\" class=\"bDate\" id=\"".$bookingItem['id'] . ":" . $rateItem['id']."_date\" value=\"" . $startDate  . "\" />";
					$startDateValue .= "<input type=\"hidden\" id=\"".$bookingItem['id'] . ":" . $rateItem['id']."_range\" value=\"" . $rateItem['datestart'] . ":" .  $rateItem['dateend']  . "\" />";
				}


				// Check id item amount (pax) can change
				if( empty($rateItem['flexpax'])  ){
					$itemType = $rateItem['typename'];
				}else{
					$itemType = getDropdown($bookingItem['id'] . ":" . $rateItem['id'] ,$rateItem['pax'],"setAdults(this);") . " x "  . $rateItem['typename'];
				}

				$retHtml .= "<tr>";
				$retHtml .= "<td style=\"width:180px;\">" . $itemType . "</td>";
				$retHtml .= "<td>" . $startDateValue . "</td>";
				$retHtml .= "<td style=\"text-align:center;\">" . $rateItem['duration'] . "</td>";
				$retHtml .= "<td style=\"text-align:right;\" title=\"".number_format($rateItem['price'],0) . " " . $rateItem['currency']."/Day/Person\">" . number_format($rateItem['priceTotal'],0) . " " . $rateItem['currency'] . "</td>";
				$retHtml .= "<td style=\"text-align:right;\"><button title=\"remove from list\" onclick=\"bookingDeleteItem('".$bookingItem['id'] . "_" . $rateItem['id']."');\" class=\"delete\"></button></td>";
				$retHtml .= "</tr>";
			
				$bookingTotal += $rateItem['priceTotal'];
			}
			$retHtml .= "</table>";
		}
		$retHtml .= "<div style=\"float:right;margin-top:20px;font-size:18px;font-weight:bold;\">Total: " . number_format($bookingTotal,0) . " " . $rateItem['currency'] . "</div>";
	}else{
		$retHtml .= "<h3>There are no bookings at this moment</h3>";
	}
	return $retHtml;

}else if( $scriptProperties['type'] == 'bookingconfirmation' ){

	// get bookings from cache
	$bookingList = $modx->cacheManager->get(CACHE_KEY);

	$bookingForm['bookinginfo'] = "";
	$bookingTotal = 0;
	if( !empty($bookingList) && count($bookingList) > 0 ){

		foreach( $bookingList as $bookingItem ){

			$bookingForm['bookinginfo'] .= "<h3 style=\"padding:0;margin-top:20px;margin-bottom:5px;\"><a href=\"" . $bookingItem['url'] . "\">" . $bookingItem['name'] . " <span style=\"font-size:12px\">(" . $bookingItem['code']  .  ")</span></a></h3>";
		
			$bookingForm['bookinginfo'] .= "<table>";
			$bookingForm['bookinginfo'] .= "<tr><th>Rate Type</th><th>Start Date</th><th style=\"text-align:center;\">Duration</th><th style=\"text-align:right;\">Price </th></tr>";
			foreach( $bookingItem['rates'] as $rateItem ){

				// Check id item amount (pax) can change
				if( empty($rateItem['flexpax'])  ){
					$itemType = $rateItem['typename'];
				}else{
					$itemType = $rateItem['pax'] . " x "  . $rateItem['typename'];
				}

				// Set Item HTML
				$bookingForm['bookinginfo'] .= "<tr>";
				$bookingForm['bookinginfo'] .= "<td>" . $itemType . "</td>";
				$bookingForm['bookinginfo'] .= "<td>" . date("d M Y",$rateItem['tripdatestart']) . "</td>";
				$bookingForm['bookinginfo'] .= "<td>" . $rateItem['duration'] . "</td>";
				$bookingForm['bookinginfo'] .= "<td align=\"right\">" . number_format($rateItem['priceTotal'],0) . " " . $rateItem['currency'] . "</td>";
				$bookingForm['bookinginfo'] .= "</tr>";
			
				$bookingTotal += $rateItem['priceTotal'];
			}
			$bookingForm['bookinginfo'] .= "</table>";
		}
		$bookingForm['bookinginfo'] .= "<div style=\"float:right;margin-top:20px;font-size:18px;font-weight:bold;\">Total: " . number_format($bookingTotal,0) . " " . $rateItem['currency'] . "</div>";
	}


	$bookingForm['customerinfo'] = "<table border=0>";
	$bookingForm['customerinfo'] .= "<tr><td>Name</td><td>" . $_POST['name'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "<tr><td>Email</td><td>" . $_POST['email'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "<tr><td>Phone</td><td>" . $_POST['phone'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "<tr><td>Nationality</td><td>" . $_POST['nationality'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "<tr><td>Arrival</td><td>" . $_POST['bookingArrival'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "<tr><td>Departure</td><td>" . $_POST['bookingDeparture'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "<tr><td>Remarks</td><td>" . $_POST['text'] . "</td></tr>";
	$bookingForm['customerinfo'] .= "</table>";

    $bookingForm['customername'] = $_POST['name'];
/*
	$emailChunk = $modx->getObject('modChunk',array(
	    'name' => 'BookingFormEmail'
	));
		            
	// proccess and return booking chunk
	$htmlMail = $emailChunk->process($bookingForm);


	// Send e-mail report to emailsender from MODX system settings
	$modx->getService('mail', 'mail.modPHPMailer');
	$modx->mail->set(modMail::MAIL_BODY, $htmlMail);
	$modx->mail->set(modMail::MAIL_FROM, $modx->getOption('emailsender'));
	$modx->mail->set(modMail::MAIL_FROM_NAME, $modx->getOption('site_name'));
	$modx->mail->set(modMail::MAIL_SENDER, $modx->getOption('emailsender'));
	$modx->mail->set(modMail::MAIL_SUBJECT,"Booking Confirmation: " . $modx->getOption('site_name') );
	$modx->mail->address('to', $_POST['email'], $_POST['name']);
	$modx->mail->address('cc', $modx->getOption('emailsender'), "CustomerSupport");
	$modx->mail->address('bcc', "webmaster@sofasurfer.ch", "WebMaster" );	    
	$modx->mail->setHTML(true);

	if( !$modx->mail->send() ){
		print "<div class=\"error\">ERROR send mail to:" . $modx->getOption('emailsender') . "</div>";
	}
	$modx->mail->reset();
*/

	$modx->cacheManager->delete(CACHE_KEY);

	$confirmationChunk = $modx->getObject('modChunk',array(
	    'name' => 'BookingConfirmation'
	));
	return $confirmationChunk->process($bookingForm);

}else if( $scriptProperties['type'] == 'tours' ){

	// check if templates are set
	if( empty($scriptProperties['tmpl']) ){
		$scriptProperties['tmpl'] = "TourListAccordion";
	}
	if( empty($scriptProperties['tmplrow']) ){
		$scriptProperties['tmplrow'] = "TourListItemAccordion";
	}
	
	$scriptProperties['tourlist'] = "";
	
	// check for country
	if( !empty($scriptProperties['destination']) ){
	
		// get destination id from code
		$destination = $modx->getObject('seadDestination', array('code'=>$scriptProperties['destination']) );

		if( empty($destination) ){

			return "Invalid destination or no results for: " . $scriptProperties['destination'] . ")";

		}
		
		/* query for tours */
		$c = $modx->newQuery('seadTour');

		// check if type id is set
		if( empty($scriptProperties['typeid']) ){
			$c->where(array(
				'seadTour.destinationid' => $destination->get('id')
				, 'active' => 1 
				, 'seadTour.typeid:<=' => 2
			) ); 
		}else{
			$c->where(array(
				'seadTour.destinationid' => $destination->get('id')
				, 'active' => 1 
				, 'seadTour.typeid' => $scriptProperties['typeid']
			) ); 
		}


		// check if tourtype is set
		if( !empty($_GET['tid']) ){
			$c->andCondition(array('seadTour.typeid' => $_GET['tid'], 'active' => 1 ) ); 
		}
		
		// select fields
		$c->select('
		    `seadTour`.*
		');


		// sort
		if( empty($scriptProperties['sort']) ){
			$scriptProperties['sort'] = 'duration';
		}
		$c->sortby($scriptProperties['sort'],'ASC');


		// get collection
		$tours = $modx->getCollection('seadTour',$c);

		//echo $c->toSql();
	
		if( empty($tours) || count($tours) < 1 ){

			return "<h2>No products</h2>";
	
		}		

		// list tours
		$oldDuration = -1;
		foreach($tours as $tourItem){
		
			// set list properties
			$listProperties = $tourItem->toArray();
			
			$listProperties['destinationname'] = $destination->get('name');

			// Check for multilingual content
			$nameArray = $modx->fromJSON($listProperties['name']);
			if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

				$listProperties['name'] = $nameArray[$languageCode];
			}


			$sDeskArray = $modx->fromJSON($listProperties['shortdescription']);
			if( !empty($sDeskArray) && !empty($sDeskArray[$languageCode])  ){

				$listProperties['shortdescription'] = $sDeskArray[$languageCode];
			}

			// get duration
			$duration = $tourItem->getOne('Duration');
			
			if( !empty($duration) ){
				$listProperties['durationname'] = $duration->get('name');
				$durationId = $duration->get('id');
			}else{
				$listProperties['durationname'] = 'N/A';	
				$durationId = 0;
			}

			if( $oldDuration == -1 ){
			
				$listProperties['header'] = "<h1>" . $listProperties['durationname'] . "</h1><div>";

			}else if( $durationId != $oldDuration ){

				$listProperties['header'] = "</div><h1>" . $listProperties['durationname'] . "</h1><div>";			

			}else{
				$listProperties['header'] = "";
			}

			// week days frequency
			$fArray = explode(',',$listProperties['frequency']);
			$fArrayValues = array();
			foreach( $fArray as $dId ){
				$fArrayValues[] = $WEEKDAYS[$dId];
			}
			$listProperties['frequencynames'] = implode(', ',$fArrayValues);

			// themes
			$tArray = explode(',',$listProperties['theme']);
			$tArrayValues = array();
			foreach( $tArray as $tId ){
				$tArrayValues[] = $THEMES[$tId];
			}
			$listProperties['themenames'] = implode(', ',$tArrayValues);

			// segmentation
			$sArray = explode(',',$listProperties['segment']);
			$sArrayValues = array();
			foreach( $sArray as $sId ){
				$sArrayValues[] = $SEGMENTS[$sId];
			}
			$listProperties['segmentnames'] = implode(', ',$sArrayValues);

			// get language name
			$scriptProperties['languagename'] = $LANGUAGES[$tourItem->get('language')];

			// get tour list item chunk
			$listItemChunk = $modx->getObject('modChunk',array(
			    'name' => $scriptProperties['tmplrow']
			));		
			
			$scriptProperties['tourlist'] .= $listItemChunk->process($listProperties);
		

			$oldDuration = $durationId;
		}
		
	}


	// get tour list chunk
	$listChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tmpl']
	));
		            
	// proccess and return booking chunk
	return $listChunk->process($scriptProperties);
	
}else if( $scriptProperties['type'] == 'tourheader' ){

	// get tour code from param
	if( !empty($_GET['code']) ){
		$tourcode = $_GET['code'];
	}else if ( !empty($scriptProperties['tourcode']) ){
		$tourcode = $scriptProperties['tourcode'];
	}
	
	// get tour detail
	$tourDetail = $modx->getObject('seadTour', array('code1'=>$tourcode) );
	if( empty($tourDetail) ){
		return "Tour Header not found [$tourcode]..";
	}	
	
	$tourDetailArray = $tourDetail->toArray();

	// Check for multilingual content
	$nameArray = $modx->fromJSON($tourDetailArray['name']);
	if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

		$tourDetailArray['name'] = $nameArray[$languageCode];
	}


	$sDeskArray = $modx->fromJSON($tourDetailArray['shortdescription']);
	if( !empty($sDeskArray) && !empty($sDeskArray[$languageCode])  ){

		$tourDetailArray['shortdescription'] = $sDeskArray[$languageCode];
	}

	if( strlen($tourDetailArray['description']) == 23 ){

		$description = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['description'],'languagecode'=>$languageCode) ); 
		$tourDetailArray['description'] = $description->get('contenthtml');

	}

	if( strlen($tourDetailArray['itinerary']) == 23 ){

		$itinerary = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['itinerary'],'languagecode'=>$languageCode) ); 
		$tourDetailArray['itinerary'] = $itinerary->get('contenthtml');
	}
			
	if( !empty($duration) ){
		$tourDetailArray['durationname'] = $duration->get('name');
	}

	// get destination id from code
	$destination = $tourDetail->getOne('Destination');
			
	if( !empty($destination) ){
		$tourDetailArray['destinationname'] = $destination->get('name');
	}else{
		$tourDetailArray['destinationname'] = "Destination not found";
	}

	// get language name
	$tourDetailArray['languagename'] = $LANGUAGES[$tourDetail->get('language')];
			
	// get duration
	$duration = $tourDetail->getOne('Duration');
	
	if( !empty($duration) ){		
		$tourDetailArray['durationname'] = $duration->get('name');
	}else{
		$tourDetailArray['durationname'] = "";
	}

	// week days frequency
	$fArray = explode(',',$tourDetailArray['frequency']);
	$fArrayValues = array();
	foreach( $fArray as $dId ){
		$fArrayValues[] = $WEEKDAYS[$dId];
	}
	$tourDetailArray['frequencynames'] = implode(', ',$fArrayValues);

	// themes
	$tArray = explode(',',$tourDetailArray['theme']);
	$tArrayValues = array();
	foreach( $tArray as $tId ){
		$tArrayValues[] = $THEMES[$tId];
	}
	$tourDetailArray['themenames'] = implode(', ',$tArrayValues);

	// segmentation
	$sArray = explode(',',$tourDetailArray['segment']);
	$sArrayValues = array();
	foreach( $sArray as $sId ){
		$sArrayValues[] = $SEGMENTS[$sId];
	}
	$tourDetailArray['segmentnames'] = implode(', ',$sArrayValues);

	
	// get tour list chunk
	if( empty($scriptProperties['tmpl']) ){
		$scriptProperties['tmpl'] = "TourHeader";
	}
		
	$listChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tmpl']
	));
		            
	// proccess and return booking chunk
	return $listChunk->process($tourDetailArray);


}else if( $scriptProperties['type'] == 'tourdetail' ){

	// get tour code from param
	if( empty($tourcode) ){
		if( !empty($_GET['code']) ){
			$tourcode = $_GET['code'];
		}else if ( !empty($scriptProperties['tourcode']) ){
			$tourcode = $scriptProperties['tourcode'];
		}else{
			// get tour code from URL
			$tourCode = $_SERVER['REQUEST_URI'];
			$tourCode = str_replace("/tour-","",$tourCode);
			$tourCode = str_replace(".html","",$tourCode);	
		}
	}

	// get tour detail
	$tourDetail = $modx->getObject('seadTour', array('code1'=>$tourcode) );
	
	if( empty($tourDetail) ){
		return "No tour information found [$tourCode] !";
	}

	$tourDetailArray = $tourDetail->toArray();

	// get destination id from code
	$destination = $tourDetail->getOne('Destination');
			
	$tourDetailArray['destinationname'] = $destination->get('name');

	// get language name
	$tourDetailArray['languagename'] = $LANGUAGES[$tourDetail->get('language')];
			
	// get duration
	$duration = $tourDetail->getOne('Duration');

	// Check for multilingual content
	$nameArray = $modx->fromJSON($tourDetailArray['name']);
	if( !empty($nameArray) && !empty($nameArray[$languageCode]) ){

		$tourDetailArray['name'] = $nameArray[$languageCode];
	}


	$sDeskArray = $modx->fromJSON($tourDetailArray['shortdescription']);
	if( !empty($sDeskArray) && !empty($sDeskArray[$languageCode])  ){

		$tourDetailArray['shortdescription'] = $sDeskArray[$languageCode];
	}

	if( strlen($tourDetailArray['description']) == 23 ){

		$description = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['description'],'languagecode'=>$languageCode) ); 
		$tourDetailArray['description'] = $description->get('contenthtml');

	}

	if( strlen($tourDetailArray['itinerary']) == 23 ){

		$itinerary = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['itinerary'],'languagecode'=>$languageCode) ); 
		$tourDetailArray['itinerary'] = $itinerary->get('contenthtml');
	}
			
	if( !empty($duration) ){
		$tourDetailArray['durationname'] = $duration->get('name');
	}

        // make all porperties public
        $modx->toPlaceholders($tourDetailArray);

	// get tour list chunk
	if( empty($scriptProperties['tmpl']) ){
		$scriptProperties['tmpl'] = "TourDetail";
	}
		
	$listChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tmpl']
	));
		            
	// proccess and return booking chunk
	return $listChunk->process($tourDetailArray);


}else if( $scriptProperties['type'] == 'tourprice' ){

	// get tour code from URL
	if( !empty($_GET['code']) ){
		$tourcode = $_GET['code'];
	}else if ( !empty($scriptProperties['tourcode']) ){
		$tourcode = $scriptProperties['tourcode'];
	}
	
	// get tour detail
	$tourDetail = $modx->getObject('seadTour', array('code1'=>$tourcode) );
	if( empty($tourDetail) ){
		return "Tour Header not found [$tourcode]..";
	}

	// get rates
	$c = $modx->newQuery('seadRate');	
	$c->innerJoin('seadRateType','RateType');
	$c->innerJoin('seadTour','Tour');
	$c->where(array('seadRate.productcode' => $tourDetail->get('productcode') ) );	

	// select fields
	$c->select('
	    `seadRate`.*,
	    `RateType`.sortorder,
	    `Tour`.markup1,
	    `Tour`.markup2,
	    `Tour`.duration as durationid,
	    `Tour`.typeid as tourtypeid
	');

	$c->sortby('datestart,duration,sortorder','ASC');

	// get collection
	$rateCollection = $modx->getCollection('seadRate',$c);	

	//echo $c->toSql();

	$rateList = array();
	$rateTypes = array();
	foreach( $rateCollection as $rateItem ){
	
		if( $rateItem->get('price') > 0 ){

			// Dive Courses
			if( $rateItem->get('tourtypeid') == 4 ){

				$rKEy = "divecourse_" . $rateItem->get('datestart') . '_public';
				$rateList[$rKEy]['type'] = 'public';
				$scriptProperties['tmpl'] = 'DiveCoursesRates';
				$rateList[$rKEy]['listChunk'] = 'DiveCoursesRatesItem';
				$rType = 'checkbox';			

			// Dive Resorts
			}else if( $rateItem->get('tourtypeid') == 5 ){
				$rKEy = "diveresort_" . $rateItem->get('datestart') . $rateItem->get('typeid') . $rateItem->get('duration') . '_public';
				$rateList[$rKEy]['type'] = 'public';
				$scriptProperties['tmpl'] = 'DiveResortRates';
				$rateList[$rKEy]['listChunk'] = 'DiveResortRatesItem';
				$rType = 'checkbox';

			// Sailing Charter
			}else if( $rateItem->get('tourtypeid') == 6 ){

				$rKEy = "sailing_" . $rateItem->get('datestart')  . $rateItem->get('duration') . '_private';
				$rateList[$rKEy]['type'] = 'private';
				$scriptProperties['tmpl'] = 'SailingCharterRates';
				$rateList[$rKEy]['listChunk'] = 'TourRatesItem';
				$rType = 'radio';

			// Public/JoinIn Tour
			}else if( $rateItem->get('typeid') == 5 || $rateItem->get('typeid') == 6 ) {
				$rKEy = $rateItem->get('datestart') . '_public';
				$rateList[$rKEy]['type'] = 'public';
				$rateList[$rKEy]['listChunk'] = 'TourRatesItemPublic';
				$rType = 'checkbox';

			// Private Tour
			}else{
				$rKEy = $rateItem->get('datestart') . '_private';
				$rateList[$rKEy]['type'] = 'private';
				$rateList[$rKEy]['listChunk'] = 'TourRatesItem';
				$rType = 'radio';
			}

			// add rate type to list
			if( empty($rateTypes[$rateItem->get('typeid')]) ){
			
				$rate = $modx->getObject('seadRateType', $rateItem->get('typeid') );
				if( !empty($rate) ){
					$rateTypes[$rateItem->get('typeid')] = $rate->get('name');
				}else{
					$rateTypes[$rateItem->get('typeid')] = "Not found (" . $rateItem->get('typeid') . ")";
				}
			}
			$rateList[$rKEy]['typeName'] = $rateTypes[$rateItem->get('typeid')];

			// get duration
			$duration = $rateItem->getOne('Duration');

			if( empty($duration) ){
				$duration = $modx->getObject('seadDuration', $rateItem->get('durationid') );
			}
	
			if( !empty($duration) ){	
				// check if duration is per day	
				if( $duration->get('id') == 15 && $rateItem->get('tourtypeid') == 5  ){

					$numberOfDays = intval(($rateItem->get('dateend') - time())/86400);

					$rateList[$rKEy]['durationname'] = "<select id=\"rateduration".$rKEy."\" style=\"width:120px;\">";
					for($nD=1;$nD<31;$nD++){
						$rateList[$rKEy]['durationname'] .= "<option value=\"".intval($nD)."\">" . intval($nD+1) . " days / " . $nD . " nights</option>";
					}
					$rateList[$rKEy]['durationname'] .= "<option> +31 days</option>";
					$rateList[$rKEy]['durationname'] .= "</select>";
				}else if( $duration->get('id') == 15 && $rateItem->get('tourtypeid') == 6 ){

					$numberOfDays = intval(($rateItem->get('dateend') - time())/86400);

					$rateList[$rKEy]['durationname'] = "<select id=\"rateduration".$rKEy."\" style=\"width:120px;\">";
					for($nD=1;$nD<31;$nD++){
						if( $nD == 1 ){
							$rateList[$rKEy]['durationname'] .= "<option value=\"".intval($nD)."\">" . intval($nD) . " x day 24hrs</option>";
						}else{
							$rateList[$rKEy]['durationname'] .= "<option value=\"".intval($nD)."\">" . intval($nD) . " days</option>";
						}
					}
					$rateList[$rKEy]['durationname'] .= "<option> +31 days</option>";
					$rateList[$rKEy]['durationname'] .= "</select>";
				}else{
					$rateList[$rKEy]['durationname'] = $duration->get('name');
				}
			}else{
				$rateList[$rKEy]['durationname'] = "Not Found.";
			}


			$rateList[$rKEy]['datestart'] = $rateItem->get('datestart');
			$rateList[$rKEy]['dateend'] = $rateItem->get('dateend');

			// check for markup
			$title = "Markup:";
			$price = $rateItem->get('price');
			if( $rateItem->get('markup1') > 0 ){
				$title .= " " . $rateItem->get('markup1') . " % ";
				$price = ($price + (($price/100)*$rateItem->get('markup1')));
			}
			if( $rateItem->get('markup2') > 0 ){
				$title .= " " . $rateItem->get('markup1') . " " . $rateItem->get('currency');
				$price = ($price + $rateItem->get('markup2'));
			}

			// check if currency is different
			if( $rateItem->get('currency') != $currency ){
				
				$currencyObject = $modx->getObject('seadCurrency',array( 'code' => $rateItem->get('currency') ) );
				$currencyRate = $currencyObject->get( strtolower($currency) );
				$price2 = ($price*($currencyRate));
				$title .= "* Currency: " . $price . " " . $rateItem->get('currency') . " = " . $price2 . " " . $currency . " (" . $currencyRate . ")";
			}else{
				$price2 = $price;
			}

			// round up price
			$price2 = ceil($price2);

			// check if admin
			if( empty($isAdmin) ){
				$title = "";
			}
			
			if( $rateItem->get('price') > 0 ){
				$rateList[$rKEy]['rates'][$rateItem->get('id')] = "<input type=\"$rType\" id=\"".$rateItem->get('id')."\" name=\"$rKEy\" onclick=\"setRate(this);\" value=\"tour:" . $rateItem->get('typeid') . ":" . $rateItem->get('id') . "\" />"; 
				$rateList[$rKEy]['rates'][$rateItem->get('id')] .= "<span title=\"$title\">" . number_format($price2,0) . " " . $currency . "</span>";
			}else{
				$rateList[$rKEy]['rates'][$rateItem->get('id')] = 'N/A';
			}

			//echo $rateItem->get('id') . "-" . $rateItem->get('typeid') . "-" . $rateItem->get('sortorder') . "<br/>";

		}
	}

	//print_r($rateList);
	$scriptproperties['displaypublic'] = 'none';
	$scriptproperties['displayprivate'] = 'none';

	// set rates header
	foreach( $rateTypes as $key => $value ){

		// Check for title
		if( $key == 5 ){

			$scriptproperties['headerpublic'] .= "<th>" . $modx->lexicon('adults') . "</th>";

		}else if($key == 6 ){

			$scriptproperties['headerpublic'] .= "<th>" . $modx->lexicon('children') . " *</th>";

		}else if( strpos("Private",$value) !== 'false' ){

			$scriptproperties['header'] .= "<th class=\"rates\">" . str_replace("Private","",$value) . "</th>";

		}else{

			$scriptproperties['header'] .= "<th class=\"rates\">" . $value . "</th>";

		}

	}
		
	// set rates list
	foreach( $rateList as $rateItem ){ 

		// Get List chunk
		$itemChunk = $modx->getObject('modChunk',array(
		    'name' => $rateItem['listChunk']
		));

		// Check for public tour
		if( $rateItem['type'] == 'public' ){

			// set rate list
			$rCount = 1;
			foreach( $rateItem['rates'] as $ratePrice ){
				$rateItem['rate'.$rCount] .= $ratePrice;
				$rCount++;
			}

			// proccess and return booking chunk
			$scriptproperties['listpublic'] .= $itemChunk->process($rateItem);
			$scriptproperties['displaypublic'] = 'block';	

		}else{

			// set rate list
			foreach( $rateItem['rates'] as $ratePrice ){
				$rateItem['ratelist'] .= "<td class=\"rates\">" . $ratePrice . "</td>";
			}

			// proccess and return booking chunk
			$scriptproperties['list'] .= $itemChunk->process($rateItem);
			$scriptproperties['displayprivate'] = 'block';	
		}
	}
	
	// get tour price chunk
	if( empty($scriptProperties['tmpl']) ){
		$scriptProperties['tmpl'] = "TourRates";
	}
	$listChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tmpl']
	));

	$properties = $scriptproperties;

	$properties['datestart'] = $rateItem['datestart'];
	$properties['dateend']   = $rateItem['dateend'];

	// proccess and return booking chunk
	return $listChunk->process($properties);
		            


}else if( $scriptProperties['type'] == 'boatlist' ){

	// get boats
	$c = $modx->newQuery('seadBoat');	
	$c->innerJoin('seadLiveAboard','LiveAboard');
	
        if( !empty($scriptProperties['countryId']) ){
            $c->where( array('seadBoat.active' => 1, 'LiveAboard.countryid' =>  $scriptProperties['countryId'] ) );		
	}else{
            $c->where(array('seadBoat.active' => 1 ) );	
        }

	// select fields
	$c->select('
	    `seadBoat`.*,
            `LiveAboard`.countryid,
            `LiveAboard`.boatid
	');

	$c->sortby('seadBoat.name','ASC');

	// get collection
	$boatList = $modx->getCollection('seadBoat',$c);

echo "<p>[" . $scriptProperties['countryId'] . "] " . $c->toSql() . '</p>';

	if( empty($scriptProperties['tmpl']) ){
			$scriptProperties['tmpl'] = 'BoatListItem';
	}

	// check if liveaboard is set
	if( !empty($_GET['lid']) ){
	
	    $liveaboard = $modx->getObject('seadLiveAboard',$_GET['lid']);
		$_GET['bid'] = $liveaboard->get('boatid');
	}

	$boatHtml = "";
	foreach( $boatList as $boatItem ){

		$boatArray = $boatItem->toArray();
			
		$nameArray = $modx->fromJSON( $boatItem->get('name') );
		$boatArray['name'] = $nameArray[$languageCode];
					
		// check if boat is active
		if( !empty($_GET['bid']) && $_GET['bid'] == $boatItem->get('id') ){

			$boatArray['className'] = 'navActive';
					
			$liveAboards = $boatItem->getMany('LiveAboard');
			
			$boatArray['SubMenuLiveaboards'] = "<ul>";
			foreach($liveAboards as $liveAboard){
				
				$nameArray = $modx->fromJSON($liveAboard->get('name'));
				if( !empty($nameArray) ){
					$liveaboardName = $nameArray[$languageCode];
				}else{
					$liveaboardName = 	$liveAboard->get('name');
				}
				if( $liveAboard->get('active') == 1 ){
					if( !empty($liveaboard) && $liveaboard->get('id') == $liveAboard->get('id') ){
						$className = "navActive";
					}else{
						$className = "";
					}
					$boatArray['SubMenuLiveaboards'] .= '<li class="'.$className.'"><a href="liveaboard-detail.html?lid='.$liveAboard->get('id').'">' . $liveaboardName . '</a></li>';
				}
			}
			$boatArray['SubMenuLiveaboards'] .= '</ul>';
		}else{
			$boatArray['className'] = "";
			$boarArray['SubMenuLiveaboards'] = "";
		}

		$listChunk = $modx->getObject('modChunk',array(
		    'name' => $scriptProperties['tpl']
		));
				    
		// proccess and return booking chunk
		$boatHtml .= $listChunk->process($boatArray);
		
	}
	return $boatHtml;


}else if( $scriptProperties['type'] == 'liveaboardlist' ){

	//check if one or more destinations
	$destinationCodes = explode(",",$scriptProperties['destination']);

	$whereArray = array();
	foreach( $destinationCodes as $code ){
		// get destination
		$destination = $modx->getObject('seadDestination', array( 'code' => $code ) );
	
		if( empty($destination) ){
			return "Invalid Destination";
		}else{
			array_push($whereArray, $destination->get('id') );
		}

	}
	
	/* query for schedule */
	$c = $modx->newQuery('seadLiveAboard');
	$c->leftJoin('seadBoat','Boat');

	$c->where( array('active' => 1, 'destinationid:IN' => $whereArray ) );	

	// select fields
	$c->select('
	    `seadLiveAboard`.*,
	    `Boat`.code AS boatcode,
	    `Boat`.name AS boatname,
	    `Boat`.imgthumb AS boatimgthumb
	');

	// sort
	$c->sortby('boatcode,boatname','ASC');


	// get collection
	$liveaboardCollection = $modx->getCollection('seadBoatSchedule',$c);

	//echo $c->toSql();	

	$boatArray = array();
	$boatListArray = array();
	foreach ($liveaboardCollection as $liveaboard){
	
		if( empty($boatListArray[$liveaboard->get('boatid')]) ){
			$boatListArray[$liveaboard->get('boatid')] = $liveaboard->toArray();
		}
		$nameArray = $modx->fromJSON( $liveaboard->get('name') );
		if( !empty($nameArray) ){
			$liveaboardName = $nameArray[$languageCode];
		}else{
			$liveaboardName = $liveaboard->get('name');
		}
				
		$boatListArray[$liveaboard->get('boatid')]['liveaboard'][] = "<a href=\"[[~545]]?lid=".$liveaboard->get('id')."\">" . $liveaboardName . "</a><br/>";

	}

	foreach( $boatListArray as $boatListItem ){

		$boatListItem['liveaboardlist'] = "<ul>";
		foreach( $boatListItem['liveaboard'] as $liveaboard ){
			$boatListItem['liveaboardlist'] .= "<li>" . $liveaboard . "</li>";
		}
		$boatListItem['liveaboardlist'] .= "</ul>";

		$listItemChunk = $modx->getObject('modChunk',array(
		    'name' => 'LiveaboardListItem'
		));

		$boatArray['boatlist'] .= $listItemChunk->process($boatListItem);

	}
	
	$listChunk = $modx->getObject('modChunk',array(
	    'name' => 'LiveaboardList'
	));
			    
	// proccess and return booking chunk
	return $listChunk->process($boatArray);



}else if( $scriptProperties['type'] == 'liveaboarddetail' ){

	$liveaboard = $modx->getObject('seadLiveAboard',$_GET['lid']);

	if( empty($liveaboard) ){
		return "Invalid Liveaboard ID:" . $_GET['lid'];
	}

	$listChunk = $modx->getObject('modChunk',array(
	    'name' => 'LiveaboardDetail'
	));

    // make all porperties public
	$liveaboardArray = $liveaboard->toArray();
    $modx->toPlaceholders($liveaboardArray);
			    

	// proccess and return booking chunk
	return $listChunk->process($liveaboardArray);

}else if( $scriptProperties['type'] == 'liveaboardrates' ){

	/* query for schedule */
	$c = $modx->newQuery('seadBoatSchedule');
	$c->leftJoin('seadLiveAboard','LiveAboard');
	$c->leftJoin('seadBoat','Boat');
	
	/* check for liveaboard ID */
	if( empty($lid) && !empty($_GET['lid']) ){
		$lid = $_GET['lid'];
		} 

	$c->where(array(
		'seadBoatSchedule.liveaboardid' => $liveaboardid,
		'LiveAboard.active' => 1,
		'Boat.active' => 1
	));	

	// select fields
	$c->select('
	    `seadBoatSchedule`.*,
	    `Boat`.name AS boatname,
	    `LiveAboard`.markup1,
	    `LiveAboard`.markup2,
	    `LiveAboard`.productcode
	');
	
	if( !empty($_GET['arrival']) && !empty($_GET['departure']) ){
		
			$c->andCondition(array( 
				'seadBoatSchedule.datestart:>=' => ($_GET['arrival']/1000)-86400
				,'seadBoatSchedule.dateend:<=' => ($_GET['departure']/1000)+86400  
			));	
		}			


	// sort
	$c->sortby('datestart','ASC');


	// get collection
	$scheduleCollection = $modx->getCollection('seadBoatSchedule',$c);

	//print $c->toSQL();

	$boatId = 0;
	$scheduleList = "";
	foreach( $scheduleCollection as $schedule ){

		if( $boatId != $schedule->get('boatid') ){

			// get rates
			$c = $modx->newQuery('seadRate');
			$c->sortby('price','ASC');
			$c->where(  array('productcode' => $productcode ) );
			$rates = $modx->getCollection('seadRate', $c );

			//print $c->toSQL();

			$ratesArray = array();
			foreach( $rates as $rate ){
				$ratesArray[ $rate->get('typeid') ] = $rate->toArray();
			}
			//asort($ratesArray);
			// check if table has to be closed
			if( $scheduleList != "" ){
				$scheduleList .= "</table>";
			}			

			$nameArray = $modx->fromJSON( $schedule->get('boatname') );
			if( !empty($nameArray) ){
				$boatName = $nameArray[$languageCode];
			}else{
				$boatName = $schedule->get('boatname');
			}			
			
			$scheduleList .= "<a href=\"boat-detail.html?bid=".$schedule->get('boatid')."\"><h3>" . $boatName . "</h3></a><table  class=\"scheduleTable\" width=\"100%\">";

			$scheduleList .= "<tr>";
			//$scheduleList .= "<th>Code</th>";
			$scheduleList .= "<th style=\"text-align:left !important;\">Departure</th>";
                        $scheduleList .= "<th style=\"text-align:left !important;\">Arrival</th>";
			$scheduleList .= "<th style=\"text-align:center !important;\">Days/ nights</th>";

			foreach( $ratesArray as $rateItem ){
				$cabinName = $CABINTYPES[ $rateItem['typeid'] ];
				if( empty($cabinName) ){
					$cabinName = "Not found(" . $rateItem['typeid'] . ")";
				}
				$scheduleList .= "<th style=\"text-align:right;\">" . $cabinName . "</th>";
			}
			$scheduleList .= "</tr>";
		}


		// get days
		$nights = round(( $schedule->get('dateend')-$schedule->get('datestart') ) / 86400);
		$days = round($nights+1);
		$scheduleList .= "<tr>";
		//$scheduleList .= "<td>" . $schedule->get('code') . "</td>";
		$scheduleList .= "<td style=\"text-align:left;\">" . date("d.m.Y",$schedule->get('datestart') ) . "</td>";
	        $scheduleList .= "<td style=\"text-align:left;\">" . date("d.m.Y",$schedule->get('dateend') ) . "</td>";

		$rKEy = 'liveaboard_' . $schedule->get('liveaboardid') . '_code_' . $schedule->get('code');

		// Custom Rate Duration for MV South Siam
		if( $schedule->get('boatid') == 14 ){
			$scheduleList .= "<td align=\"center\"><select id=\"rateduration$rKEy\">";

			for( $iD=1; $iD<$days; $iD++ ){
				$scheduleList .= "<option value=\"".intval($iD+1)."\">" . intval($iD+1) . "days / " . $iD . "night</option>";		
			}
			$scheduleList .= "</select></td>";

			// set days to defaul
			$days = 1;

		}else{
			$scheduleList .= "<td align=\"center\">" . $days . " / " . $nights . "" . "</td>";
		}

		foreach( $ratesArray as $rateItem ){

			// get price
			$price = $rateItem['price'];

			// check for markup
			$title = "Markup:";
			if( $schedule->get('markup1') > 0 ){
				$title .= " " . $schedule->get('markup1') . " % ";
				$price = ($price + (($price/100)*$schedule->get('markup1')));
			}else	if( $schedule->get('markup2') > 0 ){
				$title .= " " . $schedule->get('markup1') . " " . $rateItem['currency'];
				$price = ($price + $schedule->get('markup2'));
			}else{
				$title .= " No Markup " . $schedule->get('markup1') . "/" . $schedule->get('markup2') ;
			}

			// check if currency is different
			if( $rateItem['currency'] != $currency ){
			
				$currencyObject = $modx->getObject('seadCurrency',array( 'code' => $rateItem['currency'] ) );
				$currencyRate = $currencyObject->get( strtolower($currency) );
				$price2 = ($price*($currencyRate));
				$title .= " Currency: " . $price . " " . $rateItem['currency'] . " = " . $price2 . " " . $currency . " (" . $currencyRate . ")";
			}else{
				$price2 = $price;
			}

			// add number of days to price
			$price2 = ($price2*$days);

			// round up price
			$price2 = ceil($price2);

			// check if admin
			if( empty($isAdmin) ){
				$title = "";
			}
		
			$rSelect = "<input type=\"checkbox\" name=\"$rKEy\" id=\"".$rateItem['id']."\" onclick=\"setRate(this);\" value=\"liveaboard:"  . $schedule->get('id') . ":" . $rateItem['id'] . "\" />"; 

			$scheduleList .= "<td title=\"$title\" align=\"right\">$rSelect" . number_format($price2,0) . " " . $currency . "</td>";
		}

		$scheduleList .= "</tr>";
		$boatId = $schedule->get('boatid');
	}

	$scheduleList .= '</table>';

	return $scheduleList;
	
}else if( $scriptProperties['type'] == 'boatdetail' ){

	// get boat detail
	$boatDetail = $modx->getObject('seadBoat', $_GET['bid'] );
	if( empty($boatDetail) ){
		return "Invalid Boat ID:" . $_GET['bid'];	
	}
	$boatDetailArray = $boatDetail->toArray();

	$boatNameArray = $modx->fromJSON( $boatDetail->get('name') );
	$boatDetailArray['name'] = $boatNameArray[ $languageCode ];

	$boatDescArray = $modx->fromJSON( $boatDetail->get('description') );
	$boatDetailArray['description'] = $boatDescArray[ $languageCode ];
	
	// make all porperties public
	$modx->toPlaceholders($boatDetailArray);

	/* Query for spec list */
	$c = $modx->newQuery('seadBoatSpecValue');
	$c->innerJoin('seadBoatSpec','BoatSpec');
	$c->where(array('seadBoatSpecValue.boatid' => $boatDetailArray['id'] ) );

	// sort
	$c->sortby('typeid,specid','DESC');

	// select fields
	$c->select('
	    `seadBoatSpecValue`.*,
	    `BoatSpec`.name,   
	    `BoatSpec`.unit,  
	    `BoatSpec`.typeid    
	');

	// get collection
	$specCollection = $modx->getCollection('seadBoatSpecValue',$c);

	$specTypeId = 0;
	$boatDetailArray['SpecList'] = "<table width=\"100%\">";
	foreach( $specCollection as $spec){
		
		if( $specTypeId != $spec->get('typeid') ){
			$boatDetailArray['SpecList'] .= "<tr><th colspan=\"2\" >" . $SPECTYPES[$spec->get('typeid')] . "</th></tr>";
		}

		if( $spec->get('unit') == 'text' ){
			$boatDetailArray['SpecList'] .= "<tr><td colspan=\"2\" >" . $spec->get('value') . "</td></tr>";
		}else if( $spec->get('value') != "" ){
			$boatDetailArray['SpecList'] .= "<tr><td width=\"20%\">" . $spec->get('name') . "</td><td>" . $spec->get('value') . " " . $spec->get('unit') . "</td></tr>";
		}

		$specTypeId = $spec->get('typeid');	
	}
	$boatDetailArray['SpecList'] .= "</table>";

	/* query for cabins */
	$c = $modx->newQuery('seadBoatCabin');
	$c->where(array('seadBoatCabin.boatid' => $boatDetailArray['id'] ) );

	// select fields
	$c->select('
	    `seadBoatCabin`.*
	');
	$c->sortBy('typeid','ASC');
	// get collection
	$cabinCollection = $modx->getCollection('seadBoatCabin',$c);

	$boatDetailArray['CabinList'] = "";
	foreach( $cabinCollection as $cabin ){

		$cabinName = $CABINTYPES[$cabin->get('typeid')];
		if( empty($cabinName) ){
			$cabinName = "Not found(" . $cabin->get('typeid') . ")";
		}

		$boatDetailArray['CabinList'] .= "<div style=\"width:200px;float:left;padding-right:10px;text-align:center;\"><strong>" . $cabinName . "</strong>";
		$boatDetailArray['CabinList'] .= "<p><img src=\"".$cabin->get('imgthumb')."\" /><br/>" . $cabin->get('value') . "</p></div>";

	}
	$boatDetailArray['CabinList'] .= "<div class=\"space\"></div>";

	/* query for schedule */
	$c = $modx->newQuery('seadBoatSchedule');
	$c->leftJoin('seadLiveAboard','LiveAboard');

	$c->where(array('seadBoatSchedule.boatid' => $boatDetailArray['id'], 'LiveAboard.active' => 1 ) );	

	// select fields
	$c->select('
	    `seadBoatSchedule`.*,
	    `LiveAboard`.name AS `liveaboardname`,
	    `LiveAboard`.destinationid,
	    `LiveAboard`.productcode,
	    `LiveAboard`.markup1,
	    `LiveAboard`.markup2,
	    `LiveAboard`.boatid
	');

	// sort
	$c->sortby('liveaboardname,datestart','ASC');

	// get collection
	$scheduleCollection = $modx->getCollection('seadBoatSchedule',$c);

	$liveaboardId = 0;
	$boatDetailArray['ScheduleList'] = "";
	foreach( $scheduleCollection as $schedule ){

		if( $liveaboardId != $schedule->get('liveaboardid') ){
			
			$destination = $modx->getObject('seadDestination',$schedule->get('destinationid') );

			// get rates
			$c = $modx->newQuery('seadRate');
			$c->sortby('price','ASC');
			$c->where(  array('productcode' => $schedule->get('productcode') ) );
			$rates = $modx->getCollection('seadRate', $c );
			$ratesArray = array();
			foreach( $rates as $rate ){
				$ratesArray[ $rate->get('typeid') ] = $rate->toArray();
			}
			//asort($ratesArray);
			// check if table has to be closed
			if( $boatDetailArray['ScheduleList'] != "" ){
				$boatDetailArray['ScheduleList'] .= "</table>";
			}			
			
			
			$nameArray = $modx->fromJSON( $schedule->get('liveaboardname') );
			if( !empty($nameArray) ){
				$liveaboardname = $nameArray[ $languageCode ];
			}else{
				$liveaboardname = $schedule->get('liveaboardname');				
			}
	
			$boatDetailArray['ScheduleList'] .= "<a href=\"liveaboard-detail.html?lid=".$schedule->get('liveaboardid')."\"><h3>" . $liveaboardname . "</h3></a><table class=\"scheduleTable\" width=\"100%\">";

			$boatDetailArray['ScheduleList'] .= "<tr>";
			$boatDetailArray['ScheduleList'] .= "<th style=\"width:100px;text-align:left;\">Departure</th>";
            $boatDetailArray['ScheduleList'] .= "<th style=\"width:100px;text-align:left;\">Arrival</th>";
			$boatDetailArray['ScheduleList'] .= "<th style=\"width:80px;text-align:center;\">Days/nights</th>";

			foreach( $ratesArray as $rateItem ){
				$cabinName = $CABINTYPES[ $rateItem['typeid'] ];
				if( empty($cabinName) ){
					$cabinName = "Not found(" . $rateItem['typeid'] . ")";
				}
				$boatDetailArray['ScheduleList'] .= "<th style=\"width:100px;text-align:center;\">" . $cabinName . "</th>";
			}

			$boatDetailArray['ScheduleList'] .= "</tr>";


		}


		// get days
		$nights = round(( $schedule->get('dateend')-$schedule->get('datestart') ) / 86400);
		$days = round($nights+1);
		$boatDetailArray['ScheduleList'] .= "<tr>";
		$boatDetailArray['ScheduleList'] .= "<td>" . date("d.m.Y",$schedule->get('datestart') ) . "</td>";
        $boatDetailArray['ScheduleList'] .= "<td>" . date("d.m.Y",$schedule->get('dateend') ) . "</td>";

		// Custom Rate Duration for MV South Siam
		if( $schedule->get('boatid') == 14 ){
			$boatDetailArray['ScheduleList'] .= "<td align=\"center\"><select>";

			for( $iD=1; $iD<$days; $iD++ ){
				$boatDetailArray['ScheduleList'] .= "<option>" . intval($iD+1) . "days / " . $iD . "night</option>";		
			}
			$boatDetailArray['ScheduleList'] .= "</select></td>";

			// set days to defaul
			$days = 1;

		}else{
			$boatDetailArray['ScheduleList'] .= "<td align=\"center\">" . $days . " / " . $nights . "" . "</td>";
		}


		foreach( $ratesArray as $rateItem ){

			// get price
			$price = $rateItem['price'];

			// check for markup
			$title = "Markup:";
			if( $schedule->get('markup1') > 0 ){
				$title .= " " . $schedule->get('markup1') . " % ";
				$price = ($price + (($price/100)*$schedule->get('markup1')));
			}
			if( $schedule->get('markup2') > 0 ){
				$title .= " " . $schedule->get('markup1') . " " . $rateItem['currency'];
				$price = ($price + $schedule->get('markup2'));
			}

			// check if currency is different
			if( $rateItem['currency'] != $currency ){
				
				$currencyObject = $modx->getObject('seadCurrency',array( 'code' => $rateItem['currency'] ) );
				$currencyRate = $currencyObject->get( strtolower($currency) );
				$price2 = ($price*($currencyRate));
				$title .= " Currency: " . $price . " " . $rateItem['currency'] . " = " . $price2 . " " . $currency . " (" . $currencyRate . ")";
			}else{
				$price2 = $price;
			}

			// add number of days to price
			$price2 = ($price2*$days);

			// round up price
			$price2 = ceil($price2);

			// check if admin
			if( empty($isAdmin) ){
				$title = "";
			}
			
			$rKEy = 'liveaboard_' . $schedule->get('liveaboardid') . '_code_' . $schedule->get('code');
		
			$rSelect = "<input type=\"checkbox\" name=\"$rKEy\" onclick=\"setRate(this);\" value=\"liveaboard:" . $schedule->get('id') . ":" . $rateItem['id'] . "\" />"; 

			$boatDetailArray['ScheduleList'] .= "<td title=\"$title\" align=\"right\">$rSelect" . number_format($price2,0) . " " . $currency . "</td>";
		}

		$boatDetailArray['ScheduleList'] .= "</tr>";

		$liveaboardId = $schedule->get('liveaboardid');
	}
	$boatDetailArray['ScheduleList'] .= '</table>';

	// get boat chunk
	if( empty($scriptProperties['tmpl']) ){
		$scriptProperties['tmpl'] = "BoatDetail";
	}
	
	$boatChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tmpl']
	));
		            
	// proccess and return booking chunk
	return $boatChunk->process($boatDetailArray);

}else if( $scriptProperties['type'] == 'hoteldetail' ){

	$hotel = $modx->getObject('seadHotel', $_GET['hid'] );

	if( empty($hotel) ){
		return "<h1>Invalid hotel ID</h1>";
	}

	$hotelArray = $hotel->toArray();
	$hotelArray['hotelname'] = $hotel->get('name');

        $modx->toPlaceholders($hotelArray);

	// get facilities
	$c = $modx->newQuery('seadHotelFacility');
	$c->innerJoin('seadHotelFacilityType','HotelFacilityType');
	$c->where(array('seadHotelFacility.hotelid' => $hotelArray['id'] ) );
	$c->sortby('name','ASC');
	$c->select('
	    `seadHotelFacility`.*,
	    `HotelFacilityType`.name
	');
	$facilityCollection = $modx->getCollection('seadHotelFacility',$c);

	// loop facilities
	$hotelArray['facilities'] = "<ul>";
	foreach( $facilityCollection as $facilityItem ){

		$hotelArray['facilities'] .= "<li>" . $facilityItem->get('name') . "";
		
		if( $facilityItem->get('remarks') != '' ){
			$hotelArray['facilities'] .= " (" . $facilityItem->get('remarks') . ")"; 
		}

		$hotelArray['facilities'] .= "</li>";
	}
	$hotelArray['facilities'] .= "</ul>";

	// get rates
	$c = $modx->newQuery('seadRate');
	$c->leftJoin('seadRateType','RateType');
	$c->where( array('seadRate.productcode' => $hotel->get('productcode'),'seadRate.price:>' => 0 ) );
	$c->select('
	    `seadRate`.*,
	    `RateType`.sortorder,
	    `RateType`.name AS `typename`
	');

	// get collection
	$c->sortby('typeid','ASC');
	$c->sortby('sortorder','ASC');
	$c->sortby('datestart','ASC');
	$rateCollection = $modx->getCollection('seadRate',$c);

	$oldCat = 0;
	$hotelArray['rates'] = "<table width=\"100%\">";
	foreach( $rateCollection as $rateItem ){

		if( $oldCat != $rateItem->get('typeid') ){
			$hotelArray['rates'] .= "<tr><td colspan=\"5\"><h2 style=\"padding:0;margin:0;padding-top:5px;\">" . $rateItem->get('typename') . " Rooms</h2></td></tr>"; 
		}

		$hotelArray['rates'] .= "<tr>";
		$hotelArray['rates'] .= "<td><input type=\"radio\"/></td>";		
		$hotelArray['rates'] .= "<td>" . date("d.m.Y",$rateItem->get('datestart')) . " - " . date("d.m.Y",$rateItem->get('dateend')) . "</td>";
		$hotelArray['rates'] .= "<td>" . $ROOMTYPES[$rateItem->get('categoryid')] . "</td>"; 
		$hotelArray['rates'] .= "<td>" . number_format( $rateItem->get('price'),0) . "</td>"; 
		$hotelArray['rates'] .= "<td>" . $rateItem->get('currency') . "</td>"; 
		$hotelArray['rates'] .= "</tr>";

		$oldCat = $rateItem->get('typeid');
	}
	$hotelArray['rates'] .= "</table>";



	$hotelChunk = $modx->getObject('modChunk',array(
	    'name' => 'asdHotelDetail'
	));
		            
	// proccess and return booking chunk
	return $hotelChunk->process($hotelArray);

}else{

	return "Invalid request " . $scriptProperties['type'];

}
