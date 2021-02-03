<?php
$xpdo_meta_map['StripeOrder']= array (
  'package' => 'stripe',
  'version' => '1.1',
  'table' => 'stripe_orders',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'order_id' => 0,
    'stripe_id' => '',
  ),
  'fieldMeta' => 
  array (
    'order_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'stripe_id' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => true,
      'default' => '',
    ),
  ),
  'indexes' => 
  array (
    'order_id' => 
    array (
      'alias' => 'order_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'order_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'stripe_id' => 
    array (
      'alias' => 'stripe_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'stripe_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'msOrder' => 
    array (
      'class' => 'msOrder',
      'local' => 'order_id',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
