<?php
$xpdo_meta_map['seadBoatSpecValue']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'boats_spec_values',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'boatid' => 0,
    'specid' => 0,
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
    'specid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
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
    'BoatSpec' => 
    array (
      'class' => 'seadBoatSpec',
      'local' => 'specid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
