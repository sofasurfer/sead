<?php
 
$modx->addPackage('SEAD',MODX_CORE_PATH.'components/sead/model/','sead_');


$scriptProperties = array_merge($scriptProperties,$_GET);
$scriptProperties['dataType'] = 'array';

// Run import processor 
$response = $modx->runProcessor( $scriptProperties['processor'],
	$scriptProperties,
	array(
		'processors_path' => MODX_CORE_PATH .	'components/sead/processors/'
	)
	
);

if ($response->isError()) {
	$modx->log(modX::LOG_LEVEL_ERROR, "ERROR: snippet.jSon".  $response->getMessage() );
}

return $modx->toJSON( $response->getObject() );
