<?php

// Bulk upload using CSV file
// See the BulkUpload.csv file in this directory for CSV file format details
// Note that each entry in the CSV file for bulk uploads must have either 5 fields (V1) 
// or 12 fields (V2).
// For this test, only the first 5 fields are used. If you want to use 12 fields be
// sure and indicate the empty fields with two double quotes ""

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("USER_SECRET",  "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");

require_once "KalturaClient.php";

$user = "SomeoneWeKnow";  // If this user does not exist in your KMC, then it will be created.
$kconf = new KalturaConfiguration(PARTNER_ID);
// If you want to use the API against your self-hosted CE,
// go to your KMC and look at Settings -> Integration Settings to find your partner credentials
// and add them above. Then insert the domain name of your CE below.
// $kconf->serviceUrl = "http://www.mySelfHostedCEsite.com/";
$kclient = new KalturaClient($kconf);
$ksession = $kclient->session->start(ADMIN_SECRET, $user, KalturaSessionType::ADMIN, PARTNER_ID);

if (!isset($ksession)) {
	die("Could not establish Kaltura session. Please verify that you are using valid Kaltura partner credentials.");
}

$kclient->setKs($ksession);

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$kconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;

$conversionProfileID = -1;  //use default

$CSVfile = realpath("./BulkUpload.csv");

$result = $kclient->bulkUpload->add($conversionProfileID, $CSVfile);

echo '<h3>PHP Structure returned by bulk file upload</h3>';
echo '<pre>';
print_r($result);
echo '</pre>';

?>