<?php
/* SEAD Products
 * Handels all Product Data
 * @package SEAD data class
 * Developed by Kilian Bohnenblust, 2013-2014
 * Extended and debugged by BitSiren, 2015
 */


// get SEAD include file
require_once ( $modx->getOption('core_path') .'config/sead.conf.php');


// set cache key
if( !defined('CACHE_KEY') ){
    define('CACHE_KEY', "/bookings/" . session_id() );
}
// Empty Cache for Debugging, BitSiren, AW, 2015-05-07
// $modx->cacheManager->delete($cacheKey);
// error_reporting(E_ALL);

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
// check fo custom types

if( !empty($scriptProperties['type']) && $scriptProperties['type'] == 'diveResorts' ){

	$scriptProperties['type'] = 'tours';
	$scriptProperties['typeid'] = '5';
	$scriptProperties['tmpl'] = "TourList";
	$scriptProperties['tmplrow'] = 'diveCoursesitem';

}else if( !empty($scriptProperties['type']) && $scriptProperties['type'] == 'diveCourses' ){

	$scriptProperties['type'] = 'tours';
	$scriptProperties['typeid'] = '4';
	$scriptProperties['tmpl'] = "TourList";
	$scriptProperties['tmplrow'] = 'diveCoursesitem';
}else if( !empty($scriptProperties['type']) && $scriptProperties['type'] == 'dayCourses' ){

	$scriptProperties['type'] = 'tours';
	$scriptProperties['typeid'] = '3';
	$scriptProperties['tmpl'] = "TourList";
	$scriptProperties['tmplrow'] = 'diveCoursesitem';        
       
}else if( !empty($scriptProperties['type']) && $scriptProperties['type'] == 'SailingCharter' ){

	$scriptProperties['type'] = 'tours';
	$scriptProperties['typeid'] = '6';
	$scriptProperties['tmpl'] = "TourList";
	$scriptProperties['tmplrow'] = 'diveCoursesitem';
}

$languageCode = $modx->getOption('cultureKey');

// check product type

if( $scriptProperties['type'] == 'basket' ){


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
		
		// Debugging Code, BitSiren, AW, 2015-05-07
		// $tObjectsPassed = 0;
	    // $tLiveAboard= 0;
		
		// Rate Item Count, used to create unique ID for array, BitSiren, AW, 2015-05-07
		$tRatesFound = 0; 
		
		foreach( $bookingObject['rates'] as $bookingItem){
			// Debugging Code, BitSiren, AW, 2015-05-07
		   // $tObjectsPassed++;
			// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] booking item: ' . $tObjectsPassed);
			// var_dump($bookingItem);
			
			// Booking Item Structure, BitSiren, AW, 2015-05-07
			// [0] = Product Type
			// [1] = Boat Schedule Id
			// [2] = Rate Id
			// [3] = ?
			// [4] = Duration   		
			
			// get rate item
			$rateItemArray = explode(":",$bookingItem);
			$rateItem = $modx->getObject('seadRate',$rateItemArray[2]);
 
			if( !empty($rateItem) ){
				// Debugging Code, BitSiren, AW, 2015-05-07
				$tRatesFound++;
				// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] rate item: ' . $tRatesFound);
				
				// Set rate array
				$rateArray = $rateItem->toArray();
				$productCode = $rateItem->get('productcode');           
                                 
				/**
				 * Get product id new
				 */
				
				// Debugging Code, BitSiren, AW, 2015-05-07
                // $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] Old Product ID: ' . $productCodeOld);
				// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] New Product ID: ' . $productCode);   

				// It is unclear why this is necessary. Documented: BitSiren, AW, 2015-05-07
				if( $productCodeOld != $productCode ){              
					// get product type tour
					if( $rateItemArray[0] == 'tour' ){

						$product = $modx->getObject('seadTour', array( 'productcode' => $productCode ));
						$nameArray = $modx->fromJSON($product->get('name'));
						if( !empty($nameArray) ){
							$productName = $nameArray['en'];
						}
						$productId = $product->get('id');
						$bookingList[$productId]['code'] = $product->get('code1');
						$bookingList[$productId]['url'] = $product->get('pageurl');
						$bookingList[$productId]['name'] = $productName;
						$bookingList[$productId]['type'] = $rateItemArray[0];   

					// get product type liveaboard
					}else if( $rateItemArray[0] == 'liveaboard' ){
						// Debugging Code, BitSiren, AW, 2015-05-07
						// $tLiveAboard++;
						// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] Recognized as Liveaboard: ' . $tLiveAboard);
						// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] Currently in booking array: ' . count($bookingList));
						
						// $product = $modx->getObject('seadLiveAboard', array( 'productcode' => $productCode ));
						
						// Get Schedule and Product, Documented: BitSiren, AW, 2015-05-07
						$schedule = $modx->getObject('seadBoatSchedule', array( 'id' => $rateItemArray[1] ));
						$product = $schedule->getOne('LiveAboard');
						$boat = $modx->getObject('seadBoat', array( 'id' => $schedule->get('boatid')));
						$boatNameArray = $modx->fromJSON($boat->get('name'));
						$nameArray = $modx->fromJSON($product->get('name'));
						if( !empty($nameArray) ){
							$productName = $nameArray['en'];
						}				
						$productId = $product->get('id');
						$rateArray['boatid']= $product->get('boatid');
						$bookingList[$productId]['code'] = $product->get('code');

						// Fixed wrong URL output, BitSiren, AW, 2015-05-08
						// $bookingList[$productId]['url'] = "/liveaboard-detail.html?lid=" . $product->get('id');
						$bookingList[$productId]['url'] = "/liveaboards/" . $product->get('nameurl') . ".html";
						
						// $bookingList[$productId]['name'] = $productName . " - " . $product->get('boatid');   
						$bookingList[$productId]['name'] = $productName . " - " . $boatNameArray['en'];  
						$bookingList[$productId]['type'] = $rateItemArray[0];                                         
                                                
					}

					$nameArray = $modx->fromJSON($product->get('name'));
					if( !empty($nameArray) ){
						$productName = $nameArray['en'];
					}
					$bookingList[$productId]['id'] = $product->get('id');

				}   
				
				// Add Information to rateArray, Documented: BitSiren, AW, 2015-05-07                 
				$rateArray['productId'] = $productId;
				$rateArray['productType'] = $product->get('typeid');
				$rateArray['type'] = $rateItemArray[0];
				$rateArray['tripdatestart'] = false;
				$rateArray['flexdate'] = true;
				$rateArray['flexpax'] = true;


				// Check for multilingual content
				$nameArray = $modx->fromJSON( $bookingList[$productId]['name'] );
				if( !empty($nameArray) ){
					if( !empty($nameArray[$languageCode]) ) {
				        $bookingList[$productId]['name'] = $nameArray[$languageCode];
				    }else{
				        $bookingList[$productId]['name'] = $nameArray['en'];
				    }
				}


				$sDeskArray = $modx->fromJSON($bookingList[$productId]['shortdescription']);
				if( !empty($sDeskArray) ){
					if( !empty($sDeskArray[$languageCode]) ) {
				        $bookingList[$productId]['shortdescription'] = $sDeskArray[$languageCode];
				    }else{
				        $bookingList[$productId]['shortdescription'] = $sDeskArray['en'];
				    }
				}

				/**
				 * Get duration
				 */

				// check if 'liveaboard' MV South Siam'
				// This execption has been developed by Kilian Bohnenblust to accommodate for South Siam 3's flexible duration system
				// However, the system was errorenous and has been fixed by BitSiren, AW, 2015-05-11
				if( $rateArray['type'] == 'liveaboard' && $rateArray['boatid'] == 14 ){
					// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] South Siam duration: ' . $rateItemArray[4]);
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

				// $modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old] Rate Id to be added: ' .$rateItem->get('id'));		
				// Bug in Legacy Code. This code cannot possible work for multiple date selections of the same rate code, as rate id is used as key and leads to
				// Overwriting the  array, BitSiren, AW, 2015-05-07
				// $bookingList[$productId]['rates'][$rateItem->get('id')] = $rateArray;

				// print "<pre>";
				// print_r($rateItem);
				// print "</pre>";
				
				// This code asigns a new iteration for each rate,
				// A unique identified id is created for the deltion process.
				// BitSiren, AW, 2015-05-07 
				$rUID = $rateItem->get('id') . '-' . $tRatesFound;
				$rateArray['uid'] = $rUID;
				$bookingList[$productId]['rates'][$rUID] = $rateArray;
				
				// set product code
				//$productCodeOld = $productCode;
                                
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
                        $retHtml .= "<div class=\"tab-content\">";
			$retHtml .= "<div class=\"table-responsive\"><table class=\"table table-striped\">";
			$retHtml .= "<tr><th>Rate Type</th><th>Start Date</th><th style=\"text-align:center;\">Duration</th><th style=\"text-align:right;\">Price </th><th style=\"text-align:center;\">Remove</th></tr>";
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
					$startDateValue = "<input title=\"$titleSeason\" class=\"bDate\" id=\"".$bookingItem['id'] . ":" . $rateItem['uid']."_date\" value=\"" . $startDate  . "\" />";
					$startDateValue .= "<input type=\"hidden\" id=\"".$bookingItem['id'] . ":" . $rateItem['uid']."_range\" value=\"" . $rateItem['datestart'] . ":" .  $rateItem['dateend']  . "\" />";
				}


				// Check id item amount (pax) can change
				if( empty($rateItem['flexpax'])  ){
					$itemType = $rateItem['typename'];
				}else{
					$itemType = getDropdown($bookingItem['id'] . ":" . $rateItem['uid'] ,$rateItem['pax'],"setAdults(this);") . " x "  . $rateItem['typename'];
				}

				$retHtml .= "<tr>";
				$retHtml .= "<td style=\"width:180px;\">" . $itemType . "</td>";
				$retHtml .= "<td>" . $startDateValue . "</td>";
				$retHtml .= "<td style=\"text-align:center;\">" . $rateItem['duration'] . "</td>";
				$retHtml .= "<td style=\"text-align:right;\" title=\"".number_format($rateItem['price'],0) . " " . $rateItem['currency']."/Day/Person\">" . number_format($rateItem['priceTotal'],0) . " " . $rateItem['currency'] . "</td>";
				$retHtml .= "<td style=\"text-align:center;\"><button title=\"remove from list\" onclick=\"bookingDeleteItem('".$bookingItem['id'] . "_" . $rateItem['uid']."');\" class=\"delete\"></button></td>";
				$retHtml .= "</tr>";

				$bookingTotal += $rateItem['priceTotal'];
			}
			$retHtml .= "</table></div>";
                        $retHtml .= "</div>";
		}
		$retHtml .= "<div style=\"float:right;margin-top:20px;font-size:18px;font-weight:bold;\">Total: " . number_format($bookingTotal,0) . " " . $rateItem['currency'] . "</div>";
	}else{
		$retHtml .= "<h4>Your shopping cart is currently empty.</h4>";
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
                        $bookingForm['bookinginfo'] .= "<div class=\"tab-content\">";
                        $bookingForm['bookinginfo'] .= "<div class=\"table-responsive\">";
			$bookingForm['bookinginfo'] .= "<table class=\"table table-striped\">";
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
			$bookingForm['bookinginfo'] .= "</table></div>";
                        $bookingForm['bookinginfo'] .= "</div>";
		}
		$bookingForm['bookinginfo'] .= "<div style=\"float:right;margin-top:20px;font-size:18px;font-weight:bold;\">Total: " . number_format($bookingTotal,0) . " " . $rateItem['currency'] . "</div>";
	}

        
	$bookingForm['customerinfo'] = "<table class=\"table table-striped\">";
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
		$scriptProperties['tmplrow'] = "TourListAccordionItem";
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
		$check_tabs=0;
		foreach($tours as $tourItem){		
			

			// set list properties
			$listProperties = $tourItem->toArray();
			
			

			$listProperties['destinationname'] = $destination->get('name');
			
			// Check for multilingual content
			$nameArray = $modx->fromJSON($listProperties['name']);
			
			if( !empty($nameArray) ){
				if( !empty($nameArray[$languageCode]) ) {
			        $listProperties['name'] = $nameArray[$languageCode];
			    }else{
			        $listProperties['name'] = $nameArray['en'];
			    }
			}


			$sDeskArray = $modx->fromJSON($listProperties['shortdescription']);
			if( !empty($sDeskArray) ){
				if( !empty($sDeskArray[$languageCode]) ) {
			        $listProperties['shortdescription'] = $sDeskArray[$languageCode];
			    }else{
			        $listProperties['shortdescription'] = $sDeskArray['en'];
			    }
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

			
			
			//noom inserted 	
			if($check_tabs==0){
				$listProperties['tab_header_html'] ='<div class="panel panel-default">
															<div class="panel-heading">
																 <a class="panel-title" data-toggle="collapse" data-parent="#panel-838714" href="#'.str_replace(array(' ','/'), '_', $listProperties['durationname']).'">'.$listProperties['durationname'].'</a>
															</div>
															<div id="'.str_replace(array(' ','/'), '_', $listProperties['durationname']).'" class="panel-collapse in">
																	<div class="panel-body">';
				$check_header=$listProperties['durationname'];		
				
			}elseif($check_header != $listProperties['durationname'] &&$check_tabs > 0){
				$listProperties['tab_header_html'] ='</div>
												</div>
											</div>
											<div class="panel panel-default">
															<div class="panel-heading">
																 <a class="panel-title collapsed" data-toggle="collapse" data-parent="#panel-838714" href="#'.str_replace(array(' ','/'), '_', $listProperties['durationname']).'">'.$listProperties['durationname'].'</a>
															</div>
															<div id="'.str_replace(array(' ','/'), '_', $listProperties['durationname']).'" class="panel-collapse collapse">
																	<div class="panel-body">';
				$check_header=$listProperties['durationname'];
			}else{
				$listProperties['tab_header_html'] = '';
			}
		
			//noom inserted 
			
                        
			// get tour list item chunk
			
			$listItemChunk = $modx->getObject('modChunk',array(
			    'name' => $scriptProperties['tmplrow']
			));
			$scriptProperties['tourlist'] .= $listItemChunk->process($listProperties);		
			
			$check_tabs++;
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
		$tourDetail = $modx->getObject('seadTour', array('pageurl'=>$_SERVER['REQUEST_URI']) );
	}

	if( empty($tourDetail) ){
		return "Tour Header not found [$tourcode]..";
	}

	$tourDetailArray = $tourDetail->toArray();

	// Check for multilingual content
	$nameArray = $modx->fromJSON($tourDetailArray['name']);
	if( !empty($nameArray) ){
		if( !empty($nameArray[$languageCode]) ) {
	        $tourDetailArray['name'] = $nameArray[$languageCode];
	    }else{
	        $tourDetailArray['name'] = $nameArray['en'];
	    }
	}


	$sDeskArray = $modx->fromJSON($tourDetailArray['shortdescription']);
	if( !empty($sDeskArray) ){
		if( !empty($sDeskArray[$languageCode]) ) {
	        $tourDetailArray['shortdescription'] = $sDeskArray[$languageCode];
	    }else{
	        $tourDetailArray['shortdescription'] = $sDeskArray['en'];
	    }
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

    // make all porperties public
    $modx->toPlaceholders($tourDetailArray);

	// proccess and return booking chunk
	return $listChunk->process($tourDetailArray);


// Start Section: Tour Details Page
// Comment added by BitSiren, 2015-05-21, AW

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
		$tourDetail = $modx->getObject('seadTour', array('pageurl'=>$_SERVER['REQUEST_URI']) );
	}

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
	if( !empty($nameArray) ){
		if( !empty($nameArray[$languageCode]) ) {
	        $tourDetailArray['name'] = $nameArray[$languageCode];
	    }else{
	        $tourDetailArray['name'] = $nameArray['en'];
	    }
	}


	$sDeskArray = $modx->fromJSON($tourDetailArray['shortdescription']);
	if( !empty($sDeskArray) ){
		if( !empty($sDeskArray[$languageCode]) ) {
	        $tourDetailArray['shortdescription'] = $sDeskArray[$languageCode];
	    }else{
	        $tourDetailArray['shortdescription'] = $sDeskArray['en'];
	    }
	}

	if( strlen($tourDetailArray['description']) == 23 ){

		$description = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['description'],'languagecode'=>$languageCode) );
		if( !empty($description)){
			$tourDetailArray['description'] = $description->get('contenthtml');
		}
	}

	if( strlen($tourDetailArray['itinerary']) == 23 ){

		$itinerary = $modx->getObject('seadContent',array('contentkey'=>$tourDetailArray['itinerary'],'languagecode'=>$languageCode) );
		if( !empty($itinerary)){
			$tourDetailArray['itinerary'] = $itinerary->get('contenthtml');
		}
	}

	if( !empty($duration) ){
		$tourDetailArray['durationname'] = $duration->get('name');
	}
        
	// BitSiren, Noom
	if( isset($tourDetailArray['productcode'])){
		// Changed to using query object to sort, AW, 2015-04-21
		$c = $modx->newQuery('seadProductGalleryImage');
		$c->where(array('productcode' => $tourDetailArray['productcode'],));
		$c->sortby('id','DESC');
		// $seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',array('productcode'=>$tourDetailArray['productcode']) );		
		$seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',$c);		
		
	foreach ($seadProductGalleryImage as $doc) {
		$tourDetailArray['galleryimg']  .= '<img src="'.$doc->get('filename') . '" alt="'.$doc->get('alttext').'"><br/>';        
	}
	// End BitSiren Code           
		 
	}
        
	$pdfAttachment = $modx->fromJSON($tourDetailArray['pdfAttachment']);
	if( !empty($pdfAttachment) ){
		if( !empty($pdfAttachment[$languageCode]) ) {
			if(strpos($pdfAttachment[$languageCode], '.pdf')){
		        $tourDetailArray['pdfAttachment'] ='Download the detailed program information in PDF format: ';
				$tourDetailArray['pdfAttachment'].= '<a class="link-pdf" href="'.$pdfAttachment[$languageCode].'" target="_blank">';
				$tourDetailArray['pdfAttachment'].='<img src="assets/templates/sead-new/images/pdf-download.jpg" alt="pdf" /></a>';
			}else{
				$tourDetailArray['pdfAttachment'] = '&nbsp;';
			}
	    }else{
	        $tourDetailArray['pdfAttachment'] = '&nbsp;';
	    }
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
	$c->sortby('sortorder','ASC');
	$c->sortby('seadRate.datestart','ASC');
	$c->sortby('tourtypeid','ASC');

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
				$modx->log(modX::LOG_LEVEL_ERROR, '[snippet.products_old.php] is called');
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
                        
                        // noome edite Sailing

			if( $rateItem->get('price') > 0 ){
				$rateList[$rKEy]['rates'][$rateItem->get('id')] = "<input type=\"$rType\" id=\"".$rateItem->get('id')."\" name=\"$rKEy\" onclick=\"setRate(this);\" value=\"tour:" . $rateItem->get('typeid') . ":" . $rateItem->get('id') . "\" />";
				$rateList[$rKEy]['rates'][$rateItem->get('id')] .= "<label for=".$rateItem->get('id')."><span></span>" . number_format($price2,0) . " " . $currency . "</label>";
			}else{
				$rateList[$rKEy]['rates'][$rateItem->get('id')] = 'N/A';
			}

			//echo $rateItem->get('id') . "-" . $rateItem->get('typeid') . "-" . $rateItem->get('sortorder') . "<br/>";

		}
	}

	//print_r($rateList);
	$scriptproperties['displaypublic'] = 'none';
	$scriptproperties['displayprivate'] = 'none';      
        
        
        //asort($rateTypes);      
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

    //print_r($scriptProperties);

	$urlName = str_replace('','',$_SERVER["REQUEST_URI"] );
	$urlItems = explode("/", $urlName);

	// get boats
	$c = $modx->newQuery('seadBoat');
	$c->innerJoin('seadLiveAboard','LiveAboard');

    if( !empty($countryid) && $countryid > 0 ){
            $c->where( array('seadBoat.active' => 1, 'LiveAboard.countryid' => $countryid ) );
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

        //echo $c->toSql();

	if( empty($scriptProperties['tmpl']) ){
            $scriptProperties['tmpl'] = 'BoatListItem';
	}

	// check if liveaboard is set
	if( !empty($_GET['lid']) ){

	    $liveaboard = $modx->getObject('seadLiveAboard',$_GET['lid']);
            //$scriptProperties['boatid'] = $liveaboard->get('boatid');
	}
	$boatHtml = "";
        echo $boatactive . " / " . $countryid;
	foreach( $boatList as $boatItem ){

		$boatArray = $boatItem->toArray();

		$nameArray = $modx->fromJSON( $boatItem->get('name') );
		$boatArray['name'] = $nameArray[$languageCode];
        $boatArray['boatactive'] = $boatactive;

        $liveAboards = $boatItem->getMany('LiveAboard');

        $boatArray['SubMenuLiveaboards'] = "<ul>";
        foreach($liveAboards as $liveAboard){
            $nameArray = $modx->fromJSON($liveAboard->get('name'));
            if( !empty($nameArray) ){
            	if( !empty($nameArray[$languageCode]) ) {
			        $liveaboardName = $nameArray[$languageCode];
			    }else{
			        $liveaboardName = $nameArray['en'];
			    }
            }else{
                $liveaboardName = $liveAboard->get('name');
            }
            if( $liveAboard->get('active') == 1 ){
                if( !empty($liveaboard) && $liveaboard->get('id') == $liveAboard->get('id') ){
                        $className = "active";
                }else{
                        $className = "";
                }
                $urlPath = $urlItems[1] . "/" . $urlItems[2] . "/liveaboards/" . $liveAboard->get('nameurl') . ".html";
                $boatArray['SubMenuLiveaboards'] .= '<li class="'.$className.'"><a href="'.$urlPath.'">' . $liveaboardName . '</a></li>';
            }
        }

        $boatArray['SubMenuLiveaboards'] .= '</ul>';


		$listChunk = $modx->getObject('modChunk',array(
		    'name' => $scriptProperties['tmpl']
		));

		// proccess and return booking chunk
		$boatHtml .= $listChunk->process($boatArray);

	}
	return $boatHtml;


}else if( $scriptProperties['type'] == 'liveaboardlist' ){

        $urlName = str_replace('','',$_SERVER["REQUEST_URI"] );
        $urlItems = explode("/", $urlName);

        if( $urlItems[3] == "liveaboards" ){
            $urlname = basename($_SERVER["REQUEST_URI"],'.html');
            $objProduct = $modx->getObject("seadLiveAboard", array('nameurl' => $urlname) );
            if( !empty($objProduct) ){
                $scriptProperties['liveaboardactive'] = $objProduct->get('id');
                $scriptProperties['boatactive'] = $objProduct->get('boatid');
            }
            //get country
            $country = $modx->getObject('seadCountry', array( 'nameurl' => $urlItems[2] ) );
            if( !empty($country) ){
                $scriptProperties['country'] = $country->get('code');
            }
        }

        if( empty($scriptProperties['tpl']) ){
            $scriptProperties['tpl'] = 'LiveaboardList';
        }
        if( empty($scriptProperties['tplRow']) ){
            $scriptProperties['tplRow'] = 'LiveaboardListItem';
        }

		/* query for schedule */
		$c = $modx->newQuery('seadLiveAboard');
		$c->leftJoin('seadBoat','Boat');


		//check if one or more destinations
        if( !empty($scriptProperties['destination']) ) {
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

            // Add where query
            $c->where( array('active' => 1, 'destinationid:IN' => $whereArray ) );
        }
        if( !empty($scriptProperties['country']) ) {

            $country = $modx->getObject('seadCountry', array( 'code' => $scriptProperties['country'] ) );
            if( empty($country) ){
                return "Invalid Country:" . $scriptProperties['country'];
            }else{
                $c->where( array('active' => 1, 'countryid' => $country->get('id')) );
            }
        }
    
	/* Ensure Boat + Tour are Active, BitSiren, 2015-04-09, AW */
	$c->where( array('Boat.active' => 1) );
    $c->where( array('active' => 1) );
	
	// select fields
	$c->select('
	    `seadLiveAboard`.*,
	    `Boat`.code AS boatcode,
	    `Boat`.name AS boatname,
		`Boat`.nameurl AS boatnameurl,
        `Boat`.shortdescription AS boatdescription,
	    `Boat`.imgthumb AS boatimgthumb,
		`Boat`.active AS boatactive
	');

	// sort
	$c->sortby('boatcode,nameurl','ASC');
	
	// get collection
	$liveaboardCollection = $modx->getCollection('seadLiveAboard',$c);
        
        
	//echo $c->toSql();

	$boatArray = array();
	$boatListArray = array();

	foreach ($liveaboardCollection as $liveaboard){

		if( empty($boatListArray[$liveaboard->get('boatid')]) ){
			$boatListArray[$liveaboard->get('boatid')] = $liveaboard->toArray();
			$boatListArray[$liveaboard->get('boatid')]['boaturl'] = $urlItems[1] . "/" . $urlItems[2] . "/boats/" . $liveaboard->get('boatnameurl') . ".html?bid=" . $liveaboard->get('boatid');
		}
		$nameArray = $modx->fromJSON( $liveaboard->get('name') );
		if( !empty($nameArray) ){
			if( !empty($nameArray[$languageCode]) ) {
		        $liveaboardName = $nameArray[$languageCode];
		    }else{
		        $liveaboardName= $nameArray['en'];
		    }
		}else{
			$liveaboardName = $liveaboard->get('name');
		}
		$descArray = $modx->fromJSON( $liveaboard->get('shortdescription') );
		if( !empty($descArray) ){
			if( !empty($descArray[$languageCode]) ) {
		        $liveaboardDescription = $descArray[$languageCode];
		    }else{
		        $liveaboardDescription = $descArray['en'];
		    }
		}else{
			$liveaboardDescription = $liveaboard->get('shortdescription');
		}
        if($liveaboard->get('id') == $scriptProperties['liveaboardactive'] ){
            $className = "active";
        }else{
            $className = "";
        }
        $urlPath = "/liveaboards/" . $liveaboard->get('nameurl') . ".html";
        //$urlPath =  "/liveaboards/" . $liveaboard->get('nameurl')."_".$liveaboard->get('boatnameurl'). ".html";
		//$boatListArray[$liveaboard->get('boatid')]['liveaboard'][] = "<a href=\"[[~545]]?lid=".$liveaboard->get('id')."\">" . $liveaboardName . "</a><br/>";
        $boatListArray[$liveaboard->get('boatid')]['liveaboard'][] = "<li class=\"".$className."\"><a title=\"".$liveaboardDescription."\" href=\"".$urlPath."\">" . $liveaboardName . "</a></li>";
        }
        $count = 0;
       
	foreach( $boatListArray as $boatListItem ){

		$boatListItem['liveaboardlist'] = "<ul class=\"sub-navigation\">";
		foreach( $boatListItem['liveaboard'] as $liveaboard ){
			$boatListItem['liveaboardlist'] .= "" . $liveaboard . "";
		}
		$boatListItem['liveaboardlist'] .= "</ul>";
        $boatListItem['boatactive'] = $scriptProperties['boatactive'];
        $boatListItem['$count'] = $count;

		$listItemChunk = $modx->getObject('modChunk',array(
		    'name' => $scriptProperties['tplRow']
		));

		$boatArray['boatlist'] .= $listItemChunk->process($boatListItem);
        $count++;
	}

	$listChunk = $modx->getObject('modChunk',array(
	    'name' => $scriptProperties['tpl']
	));
        
        
        if(empty($liveaboardCollection)){
            return '<h4 class="justifyleft">Currently there are no offers for this destination.</h4>';
        }
        
	// proccess and return booking chunk
	return $listChunk->process($boatArray);



}else if( $scriptProperties['type'] == 'liveaboarddetail' ){

    $urlName = str_replace('','',$_SERVER["REQUEST_URI"] );
    $urlItems = explode("/", $urlName);

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

    $liveaboardArray['boaturl'] = $urlItems[1] . "/" . $urlItems[2] . "/boats/"
		. $liveaboard->get('boatnameurl') . ".html?bid=" . $liveaboard->get('boatid');

    // proccess and return booking chunk
    return $listChunk->process($liveaboardArray);

}else if( $scriptProperties['type'] == 'liveaboardrates' ){

	/* query for schedule */
	$c = $modx->newQuery('seadBoatSchedule');
	$c->leftJoin('seadLiveAboard','LiveAboard');
	$c->leftJoin('seadBoat','Boat');

	/* check for liveaboard ID */
	if( empty($liveaboardid) && !empty($_GET['lid']) ){
		$liveaboardid = $_GET['lid'];
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
            'seadBoatSchedule.datestart:>=' => ($_GET['arrival']/1000)-86400, 'seadBoatSchedule.dateend:<=' => ($_GET['departure']/1000)+86400
        ));
    }else{
        $c->andCondition(array(
                'seadBoatSchedule.datestart:>=' => time()
        ));
    }


	// sort
	$c->sortby('datestart','ASC');


	// get collection
	$scheduleCollection = $modx->getCollection('seadBoatSchedule',$c);

	//print $c->toSQL();

	$boatId = 0;
	$scheduleList = "";
    $counter = 0;
	foreach( $scheduleCollection as $schedule ){

		if( $boatId != $schedule->get('boatid') ){

			// get rates
			$c = $modx->newQuery('seadRate');
			$c->sortby('price','DESC');
			$c->sortby('typeid','ASC');
			$c->where(  array('productcode' => $productcode ) );
			$rates = $modx->getCollection('seadRate', $c );
			
			// Debugging, BitSiren, AW, 2015-05-11
			// echo '<pre>';
            // print_r($productcode);
            // echo '</pre>';

			//print $c->toSQL();

			$ratesArray = array();
			foreach( $rates as $rate ){
				$ratesArray[ $rate->get('typeid') ] = $rate->toArray();
			}
			//asort($ratesArray);
			// check if table has to be closed
			if( $scheduleList != "" ){
				$scheduleList .= "</table></div>";
			}

			$nameArray = $modx->fromJSON( $schedule->get('boatname') );
			if( !empty($nameArray) ){
				if( !empty($nameArray[$languageCode]) ) {
			        $boatName = $nameArray[$languageCode];
			    }else{
			        $boatName = $nameArray['en'];
			    }
			}else{
				$boatName = $schedule->get('boatname');
			}
			$scheduleList .= "<div class=\"table-responsive\">";
			$scheduleList .= "<table  class=\"table text-center table-striped\" width=\"100%\">";

			$scheduleList .= "<thead><tr>";
			//$scheduleList .= "<th>Code</th>";
			$scheduleList .= "<th>Departure</th>";
			$scheduleList .= "<th>Arrival</th>";
			$scheduleList .= "<th>Days/ nights</th>";

			foreach( $ratesArray as $rateItem ){
				$cabinName = $CABINTYPES[ $rateItem['typeid'] ];
				if( empty($cabinName) ){
					$cabinName = "Not found(" . $rateItem['typeid'] . ")";
				}
				$scheduleList .= "<th>" . $cabinName . "</th>";
			}
			$scheduleList .= "</tr></thead>";
		}


		// get days
		$nights = round(( $schedule->get('dateend')-$schedule->get('datestart') ) / 86400);
		$days = round($nights+1);
		$scheduleList .= "<tr>";
		//$scheduleList .= "<td>" . $schedule->get('code') . "</td>";
		$scheduleList .= "<td>" . date("d.m.Y",$schedule->get('datestart') ) . "</td>";
	        $scheduleList .= "<td>" . date("d.m.Y",$schedule->get('dateend') ) . "</td>";

		$rKEy = 'liveaboard_' . $schedule->get('liveaboardid') . '_code_' . $schedule->get('code');

		// Custom Rate Duration for MV South Siam
		// This execption has been developed by Kilian Bohnenblust to accommodate for South Siam 3's flexible duration system
		// However, the system was errorenous and has been fixed by BitSiren, AW, 2015-05-11
		if( $schedule->get('boatid') == 14 ){
			$scheduleList .= "<td><select id=\"rateduration$rKEy\">";

			for( $iD=1; $iD<$days; $iD++ ){
				$scheduleList .= "<option value=\"".intval($iD+1)."\">" . intval($iD+1) . " days / " . $iD . " nights</option>";
			}
			$scheduleList .= "</select></td>";

			// set days to defaul
			$days = 1;

		}else{
			$scheduleList .= "<td  align=\"center\">" . $days . " / " . $nights . "" . "</td>";
		}
                /*
                echo '<pre>';
                print_r($ratesArray);
                echo '</pre>';
                 * 
                 */
                
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
                        // noom edited liveaboard rate                        
                        
                        $rKEy = 'liveaboard_' . $schedule->get('liveaboardid') . '_code_' . $schedule->get('code').'_'.$counter;
                        $rKEy = str_replace(" ","_",$rKEy);
                        
			//$rSelect = "<input type=\"checkbox\" name=\"$rKEy\" id=\"".$rateItem['uid']."\" onclick=\"setRate(this);\" value=\"liveaboard:"  . $schedule->get('id') . ":" . $rateItem['uid'] . "\" /> ";
                        $rSelect = "<input id=".$rKEy.$rateItem['id'] ." type=\"checkbox\" name=\"$rKEy\" onclick=\"setRate(this);\" value=\"liveaboard:" . $schedule->get('id') . ":" . $rateItem['id'] . "\" /> ";
                        
			//$scheduleList .= "<td title=\"$title\">$rSelect" . number_format($price2,0) . " " . $currency . "</td>";
                        $scheduleList .= "<td title=\"$title\" align=\"left\">$rSelect<label for=".$rKEy.$rateItem['id']."><span></span>" . number_format($price2,0) . " " . $currency ."</label></td>";
                        $counter++;                        
                        }

		$scheduleList .= "</tr>";
		$boatId = $schedule->get('boatid');
	}

	$scheduleList .= '</table>';

	return $scheduleList;

}else if( $scriptProperties['type'] == 'boatdetail' ){

    $urlName = str_replace('','',$_SERVER["REQUEST_URI"] );
    $urlItems = explode("/", $urlName);

	// get boat detail
	$boatDetail = $modx->getObject('seadBoat', $_GET['bid'] );
	if( empty($boatDetail) ){
		return "Invalid Boat ID:" . $_GET['bid'];
	}
	$boatDetailArray = $boatDetail->toArray();
        
       
	$boatNameArray = $modx->fromJSON( $boatDetail->get('name') );
	if( !empty($boatNameArray) ){
	    if( !empty($boatNameArray[$languageCode]) ) {
	        $boatDetailArray['name'] = $boatNameArray[$languageCode];
	    }else{
	        $boatDetailArray['name'] = $boatNameArray['en'];
	    }
	}

	$boatDescArray = $modx->fromJSON( $boatDetail->get('description') );
	if( !empty($boatDescArray) ){
	    if( !empty($boatDescArray[$languageCode]) ) {
	        $boatDetailArray['description'] = $boatDescArray[$languageCode];
	    }else{
	        $boatDetailArray['description'] = $boatDescArray['en'];
	    }
	}

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
	$boatDetailArray['SpecList'] = "<div class=\"table-responsive\"><table class=\"table  table-striped\" width=\"100%\">";
	foreach( $specCollection as $spec){

		if( $specTypeId != $spec->get('typeid') ){
			$boatDetailArray['SpecList'] .= "<tr><th colspan=\"2\" >" . $SPECTYPES[$spec->get('typeid')] . "</th></tr>";
		}

		if( $spec->get('unit') == 'text' ){
			$boatDetailArray['SpecList'] .= "<tr><td colspan=\"2\" >" . $spec->get('value') . "</td></tr>";
		}else if( $spec->get('value') != "" ){
			$boatDetailArray['SpecList'] .= "<tr><td width=\"30%\" style=\"font-weight: bold;\">" . $spec->get('name') . "</td><td>" . $spec->get('value') . " " . $spec->get('unit') . "</td></tr>";
		}

		$specTypeId = $spec->get('typeid');
                
	}
	$boatDetailArray['SpecList'] .= "</table></div>";

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

	$boatDetailArray['CabinList'] = "<div class=\"cabin\">";
        $countrow=1;
	foreach( $cabinCollection as $cabin ){

		$cabinName = $CABINTYPES[$cabin->get('typeid')];
		if( empty($cabinName) ){
			$cabinName = "Not found(" . $cabin->get('typeid') . ")";
		}

		$boatDetailArray['CabinList'] .= "<div class=\"col-md-4 column\">";
		$boatDetailArray['CabinList'] .= "<img src=\"".$cabin->get('imgthumb')."\" />";
		$boatDetailArray['CabinList'] .= "<h3>" . $cabinName . "</h3><p>" . $cabin->get('value') . "</p>";
		$boatDetailArray['CabinList'] .= "</div>";
		if($countrow%3==0){
			$boatDetailArray['CabinList'] .= "<div class=\"clearfix\"><p>&nbsp;</p></div>";
		}  
               
		$countrow++;
	}
	$boatDetailArray['CabinList'] .= "</div>";

      
    // noom
	
	if( isset($boatDetailArray['code'])){
		// Changed to using query object to sort, AW, 2015-04-21
		$c = $modx->newQuery('seadProductGalleryImage');
		$c->where(array('productcode' => $boatDetailArray['code'],));
		$c->sortby('id','DESC');
		// $seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',array('productcode'=>$boatDetailArray['code']) );			
		$seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage',$c);
				
		
		//$tourDetailArray['galleryimg'] = $seadProductGalleryImage->get('filename');
		//$seadProductGalleryImage = $modx->getCollection('seadProductGalleryImage' );
		foreach ($seadProductGalleryImage as $doc) {
			$boatDetailArray['galleryimg']  .= '<img src="'.$doc->get('filename') . '" alt="'.$doc->get('alttext').'"><br/>';
		}
               
	}
       
	/* query for schedule */
	$c = $modx->newQuery('seadBoatSchedule');
	$c->leftJoin('seadLiveAboard','LiveAboard');

	$c->where(array('seadBoatSchedule.boatid' => $boatDetailArray['id'], 'LiveAboard.active' => 1 ) );

	// select fields
	$c->select('
	    `seadBoatSchedule`.*,
	    `LiveAboard`.name AS `liveaboardname`,
	    `LiveAboard`.nameurl AS `liveaboardnameurl`,
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
			$c->sortby('price','DESC');
			$c->sortby('typeid','ASC');
			$c->where(  array('productcode' => $schedule->get('productcode') ) );
			$rates = $modx->getCollection('seadRate', $c );
			$ratesArray = array();
			foreach( $rates as $rate ){
				$ratesArray[ $rate->get('typeid') ] = $rate->toArray();
			}
			//asort($ratesArray);
			// check if table has to be closed
                       
                        
			if( $boatDetailArray['ScheduleList'] != "" ){
				$boatDetailArray['ScheduleList'] .= "</table></div>";
			}


			$nameArray = $modx->fromJSON( $schedule->get('liveaboardname') );
			if( !empty($nameArray) ){
				if( !empty($nameArray[$languageCode]) ) {
			        $liveaboardname = $nameArray[$languageCode];
			    }else{
			        $liveaboardname = $nameArray['en'];
			    }
			}else{
				$liveaboardname = $schedule->get('liveaboardname');
			}
	    	//$urlPath = $urlItems[1] . "/" . $urlItems[2] . "/liveaboards/" . $schedule->get('liveaboardnameurl') . ".html";
            $urlPath = "/liveaboards/" . $schedule->get('liveaboardnameurl') . ".html";
			$boatDetailArray['ScheduleList'] .= "<a href=".$urlPath."><h3>" . $liveaboardname . "</h3></a><div class=\"table-responsive\"><table class=\"table table-striped\" width=\"100%\">";

			$boatDetailArray['ScheduleList'] .= "<tr>";
			$boatDetailArray['ScheduleList'] .= "<th style=\"text-align:left;\">Departure</th>";
            $boatDetailArray['ScheduleList'] .= "<th style=\"text-align:left;\">Arrival</th>";
			$boatDetailArray['ScheduleList'] .= "<th style=\"text-align:center;\">Days/nights</th>";

			foreach( $ratesArray as $rateItem ){
				$cabinName = $CABINTYPES[ $rateItem['typeid'] ];
				if( empty($cabinName) ){
					$cabinName = "Not found(" . $rateItem['typeid'] . ")";
				}
				$boatDetailArray['ScheduleList'] .= "<th style=\"width:138px;text-align:center;\">" . $cabinName . "</th>";
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
		// This execption has been developed by Kilian Bohnenblust to accommodate for South Siam 3's flexible duration system
		// However, the system was errorenous and has been fixed by BitSiren, AW, 2015-05-11
		if( $schedule->get('boatid') == 14 ){
			$rKEy = 'liveaboard_' . $schedule->get('liveaboardid') . '_code_' . $schedule->get('code');
			$boatDetailArray['ScheduleList'] .= "<td align=\"center\"><select id=\"rateduration$rKEy\">";

			for( $iD=1; $iD<$days; $iD++ ){
				$boatDetailArray['ScheduleList'] .= "<option value=\"".intval($iD+1)."\">" . intval($iD+1) . " days / " . $iD . " night</option>";
			}
			$boatDetailArray['ScheduleList'] .= "</select></td>";

			// set days to defaul
			$days = 1;

		}else{
			$boatDetailArray['ScheduleList'] .= "<td align=\"center\">" . $days . " / " . $nights . "" . "</td>";
		}

        $counter = $counter;
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
                        
            //BitSiren, Noom edited 03/09/2015.
                        
			$rKEy = 'liveaboard_' . $schedule->get('liveaboardid') . '_code_' . $schedule->get('code').'_'.$counter;                                               
                        
			//$rSelect = "<input id=".$rKEy.$rateItem['uid'] ." type=\"checkbox\" name=\"$rKEy\" onclick=\"setRate(this);\" value=\"liveaboard:" . $schedule->get('id') . ":" . $rateItem['uid'] . "\" />";
             $rSelect = "<input id=".str_replace(" ","_",$rKEy).$rateItem['uid'] ." type=\"checkbox\" name=".str_replace(" ","_",$rKEy).$rateItem['uid']." onclick=\"setRate(this);\" value=\"liveaboard:" . $schedule->get('id') . ":" . $rateItem['id'] . "\" />";
                        
			//$boatDetailArray['ScheduleList'] .= "<td title=\"$title\" align=\"left\">$rSelect<label for=".$rKEy.$rateItem['uid']."><span></span>" . number_format($price2,0) . " " . $currency . "</label></td>";
            $boatDetailArray['ScheduleList']  .= "<td title=\"$title\" align=\"left\">$rSelect<label for=".str_replace(" ","_",$rKEy).$rateItem['uid']."><span></span>" . number_format($price2,0) . " " . $currency . "</label></td>";
            
            $counter++;                        
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
        
	$hotelArray['rates'] = "<div class=\"table-responsive\"><table class=\"table table-striped \" width=\"100%\">";
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
