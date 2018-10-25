<?php
$xpdo_meta_map['seadContent']= array (
  'package' => 'SEAD',
  'version' => NULL,
  'table' => 'content',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'contentkey' => '',
    'languagecode' => '',
    'contenthtml' => '',
  ),
  'fieldMeta' => 
  array (
    'contentkey' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'languagecode' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'contenthtml' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
  ),
);
