<?php
$xpdo_meta_map['seadDuration']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'duration',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'days' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'days' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
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
      'foreign' => 'duration',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Tour' => 
    array (
      'class' => 'seadTour',
      'local' => 'id',
      'foreign' => 'duration',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
