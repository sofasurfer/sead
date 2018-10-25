<?php
/**
 * Delete Gallery Images
 *
 * @param integer $id The ID of the gallery image
 *
 * @package modx
 * @subpackage processors.galleryimages
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) {
	return $modx->error->failure("Error no Gallery Image ID");
}

$idList = explode(',' ,$_DATA['id']);

foreach($idList as $id) {
	$gallery = $modx->getObject('seadProductGalleryImage', $id);
	if ($gallery == null) {
		return $modx->error->failure("Error no Gallery Image found");
	}

	if ($gallery->remove() == false) {
	    return $modx->error->failure("Error while deleting gallery image");
	}
}

return $modx->error->success('',$gallery);
