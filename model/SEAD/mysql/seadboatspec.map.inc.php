<?php
$xpdo_meta_map['seadBoatSpec']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'boats_spec',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'typeid' => 0,
    'name' => '',
    'unit' => '',
  ),
  'fieldMeta' => 
  array (
    'typeid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
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
    'unit' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'composites' => 
  array (
    'BoatSpecValue' => 
    array (
      'class' => 'seadBoatSpecValue',
      'local' => 'id',
      'foreign' => 'specid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
