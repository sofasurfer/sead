<?php
/**
 * Loads Boats Detail Page
 *
 * @package modx
 */
 
 /* get SEAD data package */
$modx->addPackage('SEAD',$modx->getOption('core_path').'components/sead/model/','sead_');

// load manager lexicon
$modx->lexicon->load('sead:manager');

if (empty($_REQUEST['id'])) return $modx->error->failure("Error no Boat ID");
$boat = $modx->getObject('seadBoat',$_REQUEST['id']);
if ($boat == null) return $modx->error->failure("Boat not found: [" . $_REQUEST['id'] . "] [" . $boat . "]" );

/* get media source if exists*/
$this->modx->loadClass('sources.modMediaSource');
$mediaSource = $modx->getObject('sources.modMediaSource',$modx->getOption('default_media_source') );
if( !empty($mediaSource) ){
	$mediaSourceArray = $mediaSource->get('properties');
	$mediaSourceId = $mediaSource->get('id');
	$baseUrl = $mediaSourceArray['baseUrl']['value'];
}

/* register JS scripts */
$modx->regClientCSS(  $modx->getOption('site_url') . 'assets/components/babel/css/babel.css?v=6');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/babel/js/babel.js?v=3');


$modx->regClientStartupScript($modx->getOption('manager_url').'assets/modext/widgets/core/modx.grid.settings.js');
$modx->regClientStartupScript($modx->config['assets_url'].'components/tinymce/jscripts/tiny_mce/tiny_mce.js');
$modx->regClientStartupScript($modx->config['assets_url'].'components/tinymce/xconfig.js');
$modx->regClientStartupScript($modx->config['assets_url'].'components/tinymce/tiny.min.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/Ext.ux.TinyMCE.min.js');

$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.combo.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.boats-update-panel.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.boats-update.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.gallery-list-grid.js');


/*
 * Load Languages
 */

$babel = $modx->getService('babel','Babel',$modx->getOption('babel.core_path',null,$modx->getOption('core_path').'components/babel/').'model/babel/',$scriptProperties);
if (!($babel instanceof Babel)) return "Babel not found";

/* create babel-box with links to translations */
$outputLanguageItems = '';
$lang = "en";
if( !empty($_GET['lang']) ){
	$lang = $_GET['lang'];
}
/* grab manager actions IDs */
$actions = $modx->request->getAllActionIDs();
$contextKeys = $babel->getGroupContextKeys('web');
foreach($contextKeys as $contextKey) {
	/* for each (valid/existing) context of the context group a button will be displayed */
	$context = $modx->getObject('modContext', array('key' => $contextKey));
	$cultureKey = $context->getOption('cultureKey');
	if($contextKey == 'web'){
		$cultureKey = 'en';
	}else{
		$cultureKey = 'de';		
	}
	if( $cultureKey == $lang ){
		$className = 'selected';
	}else{
		$className = '';
	}
	$context->prepare();
	$resourceUrl = '?a='.$actions['sead:controllers/boats/get'].'&amp;id='.$boat->get('id').'&amp;lang='.$cultureKey;
	$placeholders = array(
		'contextKey' => $contextKey,
		'cultureKey' => $cultureKey,
		'resourceUrl' => $resourceUrl,
		'className' => $className,
		'showLayer' => false,
		'showTranslateButton' => true,
		'showUnlinkButton' => false,
		'showSecondRow' => 'true'
	);
	$outputLanguageItems .= $babel->getChunk('mgr/babelBoxItem', $placeholders);
}

$modx->regClientStartupHTMLBlock('
<script type="text/javascript">
// <![CDATA[
Ext.onReady(function() {
	MODx.load({
		xtype: "sead-page-boat-update"
		,lang: "'.$lang.'"		
		,media_source: "' . $mediaSourceId . '"
		,boat: "'.$boat->get('id').'"
		,product: "'.$boat->get('code').'"
	});
});
// ]]>
</script>');

return '<div id="sead-panel-boat-update-div"></div><div id="babel-box">'.$outputLanguageItems.'</div>';
