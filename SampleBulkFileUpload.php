<?php

// Bulk upload using CSV file
// CSV file contains five fields:
// video title, description, tags, url (http or ftp pull with no auth), type (Video, Audio, or Image)

// BUG: It says in the docs that the conversion profile can be in the CSV file for bulk uploads:
// http://www.kaltura.com/api_v3/testmeDoc/index.php?service=bulkupload
// WHAT ARE *ALL* THE POSSIBLE CSV FIELDS?

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("USER_SECRET",  "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");

require_once "KalturaClient.php";

$user = "SomeoneWeKnow";  // If this user does not exist in your KMC, then it will be created.
$kconf = new KalturaConfiguration(PARTNER_ID);
$kclient = new KalturaClient($kconf);
$ksession = $kclient->session->start(ADMIN_SECRET, $user, KalturaSessionType::ADMIN);

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