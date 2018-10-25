<?php
/**
 * Loads the Currency list
 *
 * @package SEAD
 * @subpackage manager.sead.currencies
 */

// load manager lexicon
$modx->lexicon->load('sead:manager');

/* register JS scripts */

$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.combo.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.currency-list-grid.js');
$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.currency-list.js');

return '<div id="sead-panel-currency-div"></div>';

