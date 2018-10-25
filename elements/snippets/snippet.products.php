<?php

	/** SEAD Products
	 * 
	 * Handels all Product Data
	 *
	 *
	 * @package SEAD data class
	 */ 

	// get SEAD include file
	require_once ( $modx->getOption('core_path') .'config/sead.conf.php');


	// Set properties
	$properties = $scriptProperties;
	
       
	/* Run Processor */
	if( !empty($properties['action'])){
		$response = $modx->runProcessor( $properties['action'] ,
			$properties,
			array(
				'processors_path'   => MODX_CORE_PATH . 'components/sead/processors/'
			)
		);
		//exit(print_r($response));
                
		/* Check if valid response */
		if (  !empty($response)  && $response->isError() ){
			$properties['error'] = $response->getMessage();
		}else if( !empty($response) ){
			// merge resonse
			$properties = array_merge($properties, $response->getObject() );
		}
	}else{
		$properties['error'] = "Invalid action: " . $scriptProperties['type'];	
	}

	// Check for error
	if( !empty($properties['error']) ){
		$properties['tpl'] = "MessageError";
	}
	$tplChunk = $modx->getObject('modChunk',array(
	    'name' => $properties['tpl']
	));

	// Check if tpl is set
	if( !empty($tplChunk) ){
		// make all porperties public
		$modx->toPlaceholders($properties);	
		
		
		// proccess and return booking chunk
		return $tplChunk->process($properties);
	}else{
		$properties['error'] = "Invalis template";	
	}
	

    
    
