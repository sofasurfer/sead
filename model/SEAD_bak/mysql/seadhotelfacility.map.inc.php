<?php
$xpdo_meta_map['seadHotelFacility']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'hotels_facility',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'hotelid' => 0,
    'facilityid' => 0,
    'remarks' => '',
  ),
  'fieldMeta' => 
  array (
    'hotelid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'facilityid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'remarks' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'Hotel' => 
    array (
      'class' => 'seadHotel',
      'local' => 'hotelid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'HotelFacilityType' => 
    array (
      'class' => 'seadHotelFacilityType',
      'local' => 'facilityid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
