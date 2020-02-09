<?php

use App\Connection;
use App\FakeCurl;

require "vendor/autoload.php";

/**
 * Load Config
 */

$dbConfig = include "config/database.php";
$authConfig = include "config/auth.php";

/**
 * Load Database Connection
 */
$connection = new Connection(
    $dbConfig['host'],
    $dbConfig['database'],
    $dbConfig['username'],
    $dbConfig['password']
);
$connection->setTable('transactions');

/**
 * Create FakeCurl to post new data to disburse API
 */

$curl = new FakeCurl();
$curl->setOpt(CURLOPT_USERPWD, $authConfig['auth_key'] . ':');
$curl->post('https://nextar.flip.id/disburse', [
    'bank_code' => 'bni',
    'account_number' => '1234567890',
    'amount' => 10000,
    'remark' => 'sample',
]);

$response = json_decode($curl->response, true);

$response['transaction_id'] = $response['id'];

unset($response['id']);

$newRecord = $connection->create($response);

$transactionId = $newRecord['transaction_id'];

/**
 * Create FakeCurl to get disburse detail using transaction id
 */
$curl = new FakeCurl();
$curl->setOpt(CURLOPT_USERPWD, $authConfig['auth_key'] . ':');
$curl->get('https://nextar.flip.id/disburse/' . $transactionId);

$response = json_decode($curl->response, true);

$updateRecord = $connection->update([
    'transaction_id' => $transactionId,
], [
    'status' => $response['status'],
    'receipt' => $response['receipt'],
    'time_served' => $response['time_served'],
]);

echo "Disbursement process is success!";
