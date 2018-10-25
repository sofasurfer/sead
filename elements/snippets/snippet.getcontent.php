<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$pageId = $_GET['pageId'];
$tpl = $_GET['tpl'];
$resource = $modx->getObject('modResource',$pageId);

if( !empty($resource) && !empty($tpl) ){
    $properties = $resource->toArray();
    
    // get template chunk
    $itemChunk = $modx->getObject('modChunk',array(
        'name' => $tpl
    ));
    return $itemChunk->process( $properties );
}else if( !empty($resource) ){
    return "<h1>" . $resource->get('longtitle') . "</h1>"  .   $resource->get('content');
}else{
    return "Page [$pageId] not found!";
}