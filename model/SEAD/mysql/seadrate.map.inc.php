<?php
$xpdo_meta_map['seadRate']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'rates',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'productcode' => '',
    'typeid' => 0,
    'categoryid' => 0,
    'price' => 0,
    'currency' => '',
    'remarks' => '',
    'datestart' => 0,
    'dateend' => 0,
    'duration' => 0,
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
    'typeid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'categoryid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'price' => 
    array (
      'dbtype' => 'float',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
      'index' => 'index',
    ),
    'currency' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'remarks' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
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
    'duration' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Tour' => 
    array (
      'class' => 'seadTour',
      'local' => 'productcode',
      'foreign' => 'productcode',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Hotel' => 
    array (
      'class' => 'seadHotel',
      'local' => 'productcode',
      'foreign' => 'productcode',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'LiveAboard' => 
    array (
      'class' => 'seadLiveAboard',
      'local' => 'productcode',
      'foreign' => 'productcode',
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
    'RateType' => 
    array (
      'class' => 'seadRateType',
      'local' => 'typeid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
