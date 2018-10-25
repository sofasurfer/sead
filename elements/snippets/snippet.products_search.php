<?php
/** SEAD Products Search
 * 
 * Handels all Product Search
 *
 *
 * @package SEAD data class
 */ 


// get SEAD include file
require_once ( $modx->getOption('core_path') .'config/sead.conf.php');


// set cache key
if( !defined('CACHE_KEY') ){
    define('CACHE_KEY', "/bookings/" . session_id() );
}

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

$bookingInfo = $_GET;

//print_r($bookingInfo);

// get desination
$destination = $modx->getObject('seadDestination',$bookingInfo['destination']);

$modx->toPlaceholders($bookingInfo);

// check tour type
if($bookingInfo['trip'] <= 6 ){

    $c = $modx->newQuery('seadTour');
    $c->innerJoin('seadRate','Rate');
    //$c->innerJoin('seadDuration','Duration');
    $c->where(array(
        'seadTour.active' => 1, 
        'seadTour.typeid' => $bookingInfo['trip'], 
        'seadTour.destinationid' => $bookingInfo['destination']));  

    if( !empty( $bookingInfo['arrival']) && !empty( $bookingInfo['departure']) ){

        $c->andCondition(array( 
            'Rate.datestart:<=' => ($bookingInfo['arrival']/1000)-86400
            ,'OR:Rate.dateend:>=' => ($bookingInfo['departure']/1000)+86400
        ));
    }

    if( !empty($bookingInfo['children']) &&  $bookingInfo['children'] > 0 ){
        $c->andCondition(array('Rate.typeid' => 6, 'Rate.price:>' => 0 ));  
    }

    // get selected duration
    if( !empty($bookingInfo['arrival']) && !empty($bookingInfo['departure']) ){
        $days = ((($bookingInfo['departure']-$bookingInfo['arrival'])/1000)/86400);
        //$c->andCondition(array('Duration.days:<=' => intval($days+1) ));  
    }


//      $c->sortby("seadTour.duration","ASC"); 
    $c->select('
        `seadTour`.*,
        `seadTour`.duration as durationid,
        `Rate`.price,
        `Rate`.typeid as ratetypeid,
        `Rate`.datestart as ratestart,
        `Rate`.dateend as rateend
    ');
    $bookingInfo['total'] = $modx->getCount('seadTour',$c);
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
            // Fixed wrong URL output, BitSiren, AW, 2015-05-08
            // $listArray['url'] = "/liveaboard-detail.html?lid=" . $result->get('id');
            $listArray['pageurl'] = "/liveaboards/" . $result->get('nameurl') . ".html";
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
            if( !empty($nameArray) ){
                if( !empty($nameArray[$languageCode]) ) {
                    $listArray['name'] = $nameArray[$languageCode];
                }else{
                    $listArray['name'] = $nameArray['en'];
                }
            }
        }
        if( !empty($listArray['shortdescription']) ){
            $nameArray = $modx->fromJSON($listArray['shortdescription']);
            if( !empty($nameArray) ){
                if( !empty($nameArray[$languageCode]) ) {
                    $listArray['shortdescription'] = $nameArray[$languageCode];
                }else{
                    $listArray['shortdescription'] = $nameArray['en'];
                }
            }
        }

        // get list item chunk
        $itemChunk = $modx->getObject('modChunk',array(
            'name' => $scriptProperties['listItem']
        ));

        $listArray['counter'] = $counter;

        $bookingInfo['results'] .= $itemChunk->process( $listArray );
        $counter++;
    }
}elseif (!$bookingInfo['trip']){
    $bookingInfo['results'] .= "<p>We could not find any results for the dates provided. Please try again by seleting different arrival and/or departures dates.</p>";
} else {
    /*$bookingInfo['results'] .= "<h3>" . $modx->lexicon('quick_search_noresults',array(
            'dates' => date("d.m.Y",$bookingInfo['arrival']/1000 ) . " - " .  date("d.m.Y",$bookingInfo['departure']/1000 )
     )) . "</h3>";  
     * 
     */
    $bookingInfo['results'] .= "<p>We could not find any results for the dates provided. Please try again by seleting different arrival and/or departures dates..</p>";
}
$bookingInfo['results'] .= "</div>";
$bookingInfo['total'] = $counter-1;


//print_r($bookingInfo);

// get tour list chunk
$searchChunk = $modx->getObject('modChunk',array(
    'name' => $scriptProperties['tpl']
));
                

$modx->toPlaceholders( $bookingInfo );

// proccess and return booking chunk
return $searchChunk->process( $bookingInfo );