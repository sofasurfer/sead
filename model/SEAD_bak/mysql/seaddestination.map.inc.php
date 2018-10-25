<?php
$xpdo_meta_map['seadDestination']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'destinations',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'code' => '',
    'countryid' => 0,
    'typeid' => 0,
    'latitude' => 0,
    'longitude' => 0,
    'name' => '',
    'description' => '',
    'active' => 0,
  ),
  'fieldMeta' => 
  array (
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
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
    'typeid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'latitude' => 
    array (
      'dbtype' => 'float',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'longitude' => 
    array (
      'dbtype' => 'float',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
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
  ),
  'composites' => 
  array (
    'Tour' => 
    array (
      'class' => 'seadTour',
      'local' => 'id',
      'foreign' => 'destinationid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Hotel' => 
    array (
      'class' => 'seadHotel',
      'local' => 'id',
      'foreign' => 'destinationid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'LiveAboard' => 
    array (
      'class' => 'seadLiveAboard',
      'local' => 'id',
      'foreign' => 'destinationid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Country' => 
    array (
      'class' => 'seadCountry',
      'local' => 'countryid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
