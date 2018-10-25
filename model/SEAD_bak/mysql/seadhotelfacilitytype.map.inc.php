<?php
$xpdo_meta_map['seadHotelFacilityType']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'hotels_facility_type',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
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
  ),
  'composites' => 
  array (
    'HotelFacility' => 
    array (
      'class' => 'seadHotelFacility',
      'local' => 'id',
      'foreign' => 'facilityid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
