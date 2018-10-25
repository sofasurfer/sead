<?php
/**
 * Import Flex Rates
 *
 * @param integer $id The ID of the rate
 *
 * @package modx
 * @subpackage processors.rate
 */


$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;

// Set PHP Timeout to 0
ini_set("memory_limit",-1);
set_time_limit(0);


// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$filename = MODX_ASSETS_PATH . $scriptProperties['filename'];
$filename = str_replace("/assets/assets/","/assets/",$filename);
$fileExist = file_exists($filename);
$fileIsvalid = strpos($filename,".csv");


if( empty($fileExist) ){

    	$modx->log(modX::LOG_LEVEL_ERROR, "File not found: " . $filename );

}else if( empty($fileIsvalid) ){
    	$modx->log(modX::LOG_LEVEL_ERROR, "Invalid file format you can only import .CSV files : " . $filename );
}else{

    	$modx->log(modX::LOG_LEVEL_INFO, "Start importing file: " . $filename . " please wait.." );
	sleep(2);	



	// loop throu csv rows
	$counter = 0;
	$total = -1;
	$oldPercent = 0;
	$rates = array();

	if (($handle = fopen($filename, "r")) !== FALSE) {

	    while (($cols = fgetcsv($handle, 1000, ",")) !== FALSE) {


		if( !empty($cols) && !empty($cols[4]) ){
			
			if( empty($total) < 0 ){
				$total = count($cols);
			}

			// schedule array
			$scheduleArray = array();
			$isValid = true;
			$error = '';
			$scheduleArray['code'] = $cols[0];
			if( empty($scheduleArray['code']) || $scheduleArray['code'] == "code"){
				$error = "Invalid Code: " . $cols[0];
				$isValid = false;
			}

			// get boat
			$scheduleArray['boatid'] = $cols[1];
			$boat = $modx->getObject('seadBoat',$scheduleArray['boatid']);
			if( !empty($boat) ){
				$scheduleArray['boatname'] = $boat->get('name');
			}else{
				$error = "Boat not found: " . $scheduleArray['boatid'];
				$isValid = false;
			}

			$scheduleArray['liveaboardid'] = $cols[2];
			$liveaboard = $modx->getObject('seadLiveAboard',$scheduleArray['liveaboardid']);
			if( !empty($liveaboard) ){
				$scheduleArray['liveaboardname'] = $liveaboard->get('name');
			}else{
				$error = "Liveaboard not found: " . $scheduleArray['liveaboardid'];
				$isValid = false;
			}


			$d1 = explode('.',$cols[3]);
			if( !empty($d1) && is_numeric($d1[0]) ){
				$scheduleArray['datestart'] = mktime(0,0,0,$d1[1],$d1[0],$d1[2]);
			}
			if( empty($scheduleArray['datestart']) ){
				$error = "Invalid StartDate: " . $cols[3];
				$isValid = false;
			}

			$d2 = explode('.',$cols[4]);
			if( !empty($d2) && is_numeric($d2[0]) ){
				$scheduleArray['dateend'] = mktime(0,0,0,$d2[1],$d2[0],$d2[2]);
			}
			if( empty($scheduleArray['dateend']) ){
				$error = "Invalid EndDate: " . $cols[4];
				$isValid = false;
			}


			if( $isValid ){
				/*$modx->log(modX::LOG_LEVEL_INFO, "Import Boat:" 
					. $scheduleArray['boatname']
					. "LB:" .  $scheduleArray['liveaboardname']
					. "date start:" . date("d.m.Y",$scheduleArray['startdate'])
					. "end start:" . date("d.m.Y",$scheduleArray['startdate'])
					);*/

				$scheduleItem = $modx->newObject('seadBoatSchedule');
				$scheduleItem->fromArray($scheduleArray);
				$scheduleItem->save();

				$counter++;
			}else{
				//$modx->log(modX::LOG_LEVEL_ERROR, "Invalid Row Values" . $error . " => "  . print_r($cols,true) );
			}
		}else{
			//$modx->log(modX::LOG_LEVEL_ERROR, "Invalid Row:" . implode(";",$cols) );
		}
	    }
	}
}



sleep(2);

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,  "Import finished total rates ($counter) run time:" . $totalTime);

sleep(2);

$modx->log(modX::LOG_LEVEL_INFO,'COMPLETED');
