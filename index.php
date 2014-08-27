<?php
require_once('bootstrap.php');




$options = array(
'username' => 'pj-ql-01',
'password' => 'pj-ql-01p',
'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101'
);

echo "<pre>";
//------------------------ CUSTOMER CLIENT ------------------
$customerClient = new CustomerClient($options);


//Create a new customer
$data = array(
    'firstName' => 'david',
    'lastName' => 'johnson'

);

$response = json_decode($customerClient->create($data)); //@todo, this particular response does not provide a content type within the header. May need to contact payjunction to see whats up
var_dump($response);

$id = $response->customerId;

//Get that customers information

$response = $customerClient->read($id);



//Update that customers information
$data = array(
    'firstName' => 'davidssss',
    'lastName' => 'johnson'

);

$response = $customerClient->update($id,$data);


//Delete that Customer
$customerClient->delete($id);


//Check to ensure that the customer does not still exist
$response = $customerClient->read($id);


//------------------------ TRANSACTION CLIENT ------------------

$transactionClient = new TransactionClient($options);
$id = 1;
$data = array();

//Create a new transaction
$response = $transactionClient->create($data);

//Read an existing transaction
$response = $transactionClient->read($id);

//Update an existing transaction
$data = array(
    'amountTax' => '1',

);
$response = $transactionClient->update($id,$data);

//Add a signature @todo talk about what they mean by "raw data" and how that is handled through curl
$response = $transactionClient->addSignature(1,$data);


//------------------------ RECEIPT CLIENT ------------------

$receiptClient = new ReceiptClient($options);
$id = 1;
$data = array();

//Read an index of receipts
$response = $receiptClient->read($id);

//Read a thermal receipt for a transaction @todo currently returning null, not sure if working
$response = $receiptClient->readThermal($id);

//Read full page receipt for a transaction
$response = $receiptClient->readFullPage($id);

//Email a receipt. requires to and replyTo
//$response = $receiptClient->email($id);







var_dump($response);




