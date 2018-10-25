
<?php
/**
 * Gets Gallery Images
 *
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 *
 * @package modx
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

// load manager lexicon
$modx->lexicon->load('sead:manager');

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start', $scriptProperties, 0);
$limit = $modx->getOption('limit', $scriptProperties, 10);
$sort = $modx->getOption('sort', $scriptProperties, 'alttext');
$dir = $modx->getOption('dir', $scriptProperties, 'ASC');
$menu = $modx->getOption('menu', $scriptProperties, true);

/* query for gallery images */
$c = $modx->newQuery('seadProductGalleryImage');

if (!empty($scriptProperties['productcode']) )  {
	$c->where(array('seadProductGalleryImage.productcode' => $scriptProperties['productcode']));
}else{
	return $modx->error->failure("Invalid product code");
}

// get total count
$count = $modx->getCount('seadProductGalleryImage', $c);

// sort
$c->sortby($sort, $dir);

// limit for pageing
if ($isLimit) $c->limit($limit, $start);

// get collection
$galleryCollection = $modx->getCollection('seadProductGalleryImage', $c);

  
/* iterate through users */
$list = array();
foreach ($galleryCollection as $galleryItem) {
	$galleryArray = $galleryItem->toArray();
	$galleryArray['menu'] = array(
		array(
			'text' => $modx->lexicon('sead.action_delete'),
			'handler' => 'this.removeGalleryImage',
		),
	);
	$list[] = $galleryArray;
}

return $this->outputArray($list,$count);





