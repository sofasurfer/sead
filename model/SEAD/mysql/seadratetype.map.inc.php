<?php
$xpdo_meta_map['seadRateType']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'rates_type',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'type' => 0,
    'sortorder' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'type' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'sortorder' => 
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
    'Rate' => 
    array (
      'class' => 'seadRate',
      'local' => 'id',
      'foreign' => 'typeid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
