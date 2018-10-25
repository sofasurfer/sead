<?php
$xpdo_meta_map['seadCountry']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'country',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'code' => '',
    'latitude' => 0,
    'longitude' => 0,
    'name' => '',
    'description' => '',
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
  ),
  'composites' => 
  array (
    'Destination' => 
    array (
      'class' => 'seadDestination',
      'local' => 'id',
      'foreign' => 'countryid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
