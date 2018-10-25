<?php
$xpdo_meta_map['seadBoatSchedule']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'boats_schedule',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'code' => '',
    'boatid' => 0,
    'liveaboardid' => 0,
    'datestart' => 0,
    'dateend' => 0,
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
    'boatid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'liveaboardid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
  ),
  'aggregates' => 
  array (
    'LiveAboard' => 
    array (
      'class' => 'seadLiveAboard',
      'local' => 'liveaboardid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
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
