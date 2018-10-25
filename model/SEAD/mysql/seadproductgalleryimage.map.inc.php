<?php
$xpdo_meta_map['seadProductGalleryImage']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'product_gallery_images',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'filename' => NULL,
	'filename_thumb' => NULL,
    'productcode' => NULL,
    'description' => NULL,
    'alttext' => NULL,
  ),
  'fieldMeta' => 
  array (
    'filename' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '256',
      'phptype' => 'string',
      'null' => false,
    ),
	'filename_thumb' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '256',
      'phptype' => 'string',
      'null' => false,
    ),
    'productcode' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'alttext' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '256',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'aggregates' => 
  array (
    'Boat' => 
    array (
      'class' => 'seadBoat',
      'local' => 'productcode',
      'foreign' => 'code',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
	'LiveAboard' => 
    array (
      'class' => 'seadLiveAboard',
      'local' => 'productcode',
      'foreign' => 'productcode',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
	'Tour' => 
    array (
      'class' => 'seadTour',
      'local' => 'productcode',
      'foreign' => 'productcode',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
