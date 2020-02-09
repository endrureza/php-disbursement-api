<?php

use App\Connection;

require "vendor/autoload.php";

/**
 * Load Config
 */

$dbConfig = include "config/database.php";

/**
 * Load Database Connection
 */
$connection = new Connection(
    $dbConfig['host'], 
    $dbConfig['database'], 
    $dbConfig['username'],
    $dbConfig['password']
);

/**
 * Recreate table
 */
echo "\nPurging table...";

$connection->purgeTable();

echo "\nPurge is success!";

echo "\nMigrating table...";

$connection->createTable('transactions', [
    "id" => "bigint auto_increment primary key",
    "transaction_id" => "bigint not null",
    "amount" => "double not null",
    "status" => "varchar(10) not null",
    "timestamp" => "datetime not null",
    "bank_code" => "varchar(10) not null",
    "account_number" => "varchar(50) not null",
    "beneficiary_name" => "varchar(50)",
    "remark" => "varchar(50) not null",
    "receipt" => "varchar(255) null",
    "time_served" => "varchar(25) not null",
    "fee" => "double not null"
]);

echo "\nMigrate is success!";