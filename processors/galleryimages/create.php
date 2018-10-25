<?php
/**
 * Add a Gallery Image
 *
 * @package modx
 * @subpackage processors.rate
 */

// set log level
$modx->setLogLevel(modX::LOG_LEVEL_INFO);

$_DATA = $scriptProperties;

if (!empty($_FILES['filename']['name'])) {
	$gallery = $modx->newObject('seadProductGalleryImage');
	
	//setting image path variable
	if ($_DATA['producttype'] == 'tour') {
		//get country name for set image path (only tour product)
		$product =  $modx->getObject('seadTour', array('productcode' => $_DATA['productcode']));
		$country =  $modx->getObject('seadCountry', $product->get('countryid'));
		$countryName = $country->get('nameurl');
		$imagePath = 'assets/images/sead/'.$countryName.'/gallery_images/';
	} else if($_DATA['producttype'] == 'liveaboard' || $_DATA['producttype'] == 'boat') {
		$imagePath = 'assets/images/sead/liveaboard/gallery_images/';
	}
	$imageLargePath = $imagePath.'large/';
	$imageThumbPath = $imagePath.'thumb/';
	
	//Create a folder if it doesn't already exist in this country
	if (!file_exists(MODX_BASE_PATH.'/'.$imagePath)) {
		mkdir(MODX_BASE_PATH.$imagePath, 0777, true);
		mkdir(MODX_BASE_PATH.$imageLargePath, 0777, true);
		mkdir(MODX_BASE_PATH.$imageThumbPath, 0777, true);
	}

	$imageLargeWidth = $modx->getOption('sead.gallery_images_large_dim_width');
	$imagesThumbWidth = $modx->getOption('sead.gallery_images_thumb_dim_width');
	
	$file = explode('.', $_FILES['filename']['name']);
	$filename = $file[0];
	$extension = $file[1];
	//$imageName = 'south_east_asia_dreams_gallery_'.str_replace(' ', '_', strtolower($filename)).'_'. microtime(true).'.'.$extension;
	$imageName = str_replace(' ', '-', strtolower($filename)).'-south-east-asia-dreams'.'.'.$extension;

	//Change image name if it already exist
	$file_exists = MODX_BASE_PATH.$imageLargePath.$imageName;
	$i = 1;
	while (file_exists($file_exists)) {
		$imageName = str_replace(' ', '-', strtolower($filename)).'-'.$i.'-south-east-asia-dreams'.'.'.$extension;
		$file_exists = MODX_BASE_PATH.$imageLargePath.$imageName;
		$i++;
	}
	
	if (copy($_FILES['filename']['tmp_name'], MODX_BASE_PATH.$imageLargePath.$imageName)) {
		$size = GetimageSize($_FILES['filename']['tmp_name']);
		$imageOrig = ImageCreateFromJPEG($_FILES['filename']['tmp_name']);
		$imageX = ImagesX($imageOrig);
		$imageY = ImagesY($imageOrig);
		if ($imageX > $imageLargeWidth && $imageX >= $imageY) {
			/* Copy image to large folder */
			$width = $imageLargeWidth; //Fix Width & Heigh (Auto caculate) 
			$height = round($width * $size[1] / $size[0]);
			$imageFin = ImageCreateTrueColor($width, $height);
			ImageCopyResampled($imageFin, $imageOrig, 0, 0, 0, 0, $width+1, $height+1, $imageX, $imageY);
			ImageJPEG($imageFin, MODX_BASE_PATH.$imageLargePath.$imageName);
		}
		/* Copy image to thumbnail folder */
		$width = $imagesThumbWidth;
		$height = round($width * $size[1] / $size[0]);
		$imageFin = ImageCreateTrueColor($width, $height);
		ImageCopyResampled($imageFin, $imageOrig, 0, 0, 0, 0, $width+1, $height+1, $imageX, $imageY);
		ImageJPEG($imageFin, MODX_BASE_PATH.$imageThumbPath.$imageName);
		
		ImageDestroy($imageOrig);
		ImageDestroy($imageFin);
		
		$gallery->fromArray($_DATA);
		$gallery->set('filename', $imageLargePath.$imageName);
		$gallery->set('filename_thumb', $imageThumbPath.$imageName);
		
		if ($gallery->save() == false) {
			return $modx->error->failure("Error while adding gallery image");
		}
	}
}

return $modx->error->success('',$gallery);
