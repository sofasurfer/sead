<?php
/**
 * Loads the Tours list
 *
 * @package SEAD
 * @subpackage manager.sead.tours
 */

// load manager lexicon
$modx->lexicon->load('sead:manager');

/* register JS scripts */

$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.combo.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.destination-list-grid.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.tours-list-grid.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.tours-list.js');

return '<div id="sead-panel-tours-div"></div>';

