<?php
$xpdo_meta_map['seadTour']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'tours',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'productcode' => '',
    'code1' => '',
    'code2' => '',
    'agent' => '',
    'typeid' => 0,
    'destinationid' => 0,
    'countryid' => 0,
    'imgthumb' => '',
    'name' => '',
    'shortdescription' => '',
    'description' => '',
    'itinerary' => '',
    'frequency' => '',
    'theme' => '',
    'segment' => '',
    'datestart' => 0,
    'dateend' => 0,
    'pageurl' => '',
    'departure' => '',
    'markup1' => 0,
    'markup2' => 0,
    'currency' => '',
    'lastupdate' => NULL,
    'editor' => '',
    'active' => 0,
    'language' => '',
    'duration' => 0,
    'pdfAttachment' => NULL,
  ),
  'fieldMeta' => 
  array (
    'productcode' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'code1' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'code2' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'agent' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'typeid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'destinationid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'countryid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'imgthumb' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '500',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5000',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'shortdescription' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'itinerary' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'frequency' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'theme' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'segment' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'datestart' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'dateend' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'pageurl' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'departure' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'markup1' => 
    array (
      'dbtype' => 'float',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'markup2' => 
    array (
      'dbtype' => 'float',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'currency' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'lastupdate' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'editor' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'active' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'language' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'duration' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'pdfAttachment' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ), 
      
  ),
  'composites' => 
  array (
    'Rate' => 
    array (
      'class' => 'seadRate',
      'local' => 'productcode',
      'foreign' => 'productcode',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
	'ProductGalleryImage' => 
    array (
      'class' => 'seadProductGalleryImage',
      'local' => 'productcode',
      'foreign' => 'productcode',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Destination' => 
    array (
      'class' => 'seadDestination',
      'local' => 'destinationid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Duration' => 
    array (
      'class' => 'seadDuration',
      'local' => 'duration',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
