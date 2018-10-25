<?php
/**
 * Update Gallery Images
 *
 * @param integer $id The ID of the gallery image
 *
 * @package modx
 * @subpackage processors.galleryimages
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $modx->fromJSON($scriptProperties['data']);

if (empty($_DATA['id'])) {
	return $modx->error->failure("Error no Gallery Image ID");
}

$gallery = $modx->getObject('seadProductGalleryImage', $_DATA['id']);
if ($gallery == null) {
	return $modx->error->failure("Error no Gallery Image found");
}

$gallery->fromArray($_DATA);

if ($gallery->save() == false) {
    return $modx->error->failure("Error while saving gallery image information");
}

return $modx->error->success();
