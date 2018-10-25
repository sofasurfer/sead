<?php
$xpdo_meta_map['seadBoat']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'boats',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'code' => '',
    'imgthumb' => '',
    'imgspecthumb' => '',
    'name' => '',
    'nameurl' => '',
    'description' => '',
    'lastupdate' => NULL,
    'active' => 0,
  ),
  'fieldMeta' => 
  array (
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'imgthumb' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '500',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'imgspecthumb' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '500',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '200',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'nameurl' => 
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
    'lastupdate' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'datetime',
      'null' => false,
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
    'LiveAboard' => 
    array (
      'class' => 'seadLiveAboard',
      'local' => 'id',
      'foreign' => 'boatid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'BoatSpecValue' => 
    array (
      'class' => 'seadBoatSpecValue',
      'local' => 'id',
      'foreign' => 'boatid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'BoatCabin' => 
    array (
      'class' => 'seadBoatCabin',
      'local' => 'id',
      'foreign' => 'boatid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Schedule' => 
    array (
      'class' => 'seadBoatSchedule',
      'local' => 'id',
      'foreign' => 'boatid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
