<?php
$xpdo_meta_map['seadBoatCabin']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'boats_cabins',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'boatid' => 0,
    'typeid' => 0,
    'imgthumb' => '',
    'value' => '',
  ),
  'fieldMeta' => 
  array (
    'boatid' => 
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
    'imgthumb' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '500',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '500',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'Boat' => 
    array (
      'class' => 'seadBoat',
      'local' => 'boatid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
