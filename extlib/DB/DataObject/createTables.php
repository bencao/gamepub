#!/usr/bin/php -q
<?php
// since this version doesnt use overload, 
// and I assume anyone using custom generators should add this..

define('DB_DATAOBJECT_NO_OVERLOAD',1);

require_once 'DB/DataObject/Generator.php';

$_DB_DATAOBJECT['CONFIG']['database'] = "mysql://shaier:shaier@localhost/shaishaidb";
$_DB_DATAOBJECT['CONFIG']['schema_location'] = "/tmp";
$_DB_DATAOBJECT['CONFIG']['class_location'] = "/tmp";
$_DB_DATAOBJECT['CONFIG']['db_driver'] = "MDB2";
$_DB_DATAOBJECT['CONFIG']['quote_identifiers'] = true;

set_time_limit(0);

// use debug level from file if set..
DB_DataObject::debugLevel(1);

$generator = new DB_DataObject_Generator;
$generator->start();
 
