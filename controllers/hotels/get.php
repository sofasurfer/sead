<?php
/**
 * Loads Hotel Detail Page
 *
 * @package modx
 */
 
 /* get SEAD data package */
$modx->addPackage('SEAD',$modx->getOption('core_path').'components/sead/model/','sead_');

// load manager lexicon
$modx->lexicon->load('sead:manager');

if (empty($_REQUEST['id'])) return $modx->error->failure("Error no seadHotel ID");
$hotel = $modx->getObject('seadHotel',$_REQUEST['id']);
if ($hotel == null) return $modx->error->failure("seadHotel not found: [" . $_REQUEST['id'] . "] [" . $hotel . "]" );



/*
 *  Initialize RichText Editor
 */




$modx->regClientStartupScript($modx->config['assets_url'].'components/tinymce/jscripts/tiny_mce/tiny_mce.js');
$modx->regClientStartupScript($modx->config['assets_url'].'components/tinymce/xconfig.js');
$modx->regClientStartupScript($modx->config['assets_url'].'components/tinymce/tiny.min.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/Ext.ux.TinyMCE.min.js');


/* register JS scripts */
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.combo.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.rate-list-grid.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.hotel-update-panel.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.hotel-update.js');
$modx->regClientStartupHTMLBlock('
<script type="text/javascript">
// <![CDATA[
Ext.onReady(function() {
	MODx.load({
		xtype: "sead-page-hotel-update"
	        ,resource: "'.$hotel->get('id').'"
	        ,record: "'.$modx->toJSON($hotel).'"
		,hotel: "'.$hotel->get('id').'"
		,product: "'.$hotel->get('productcode').'"
	});
});
// ]]>
</script>');


return '<div id="sead-panel-hotel-update-div"></div>';
