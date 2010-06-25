<?php

/**
* Example of dependent multirequest (where multiple requests are batch executed and
* the output of one request depends on the output of a previous request in the same batch.
* This sample code will show how to merge the above 3 requests into a single unified 
* request (a multirequest).
**/

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("USER_SECRET",  "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");

require_once "KalturaClient.php";

// Upload a single video

$user = "SomeoneWeKnow";  // If this user does not exist in your KMC, then it will be created.
$kconf = new KalturaConfiguration(PARTNER_ID);
$kclient = new KalturaClient($kconf);
$ksession = $kclient->session->start(USER_SECRET, $user, KalturaSessionType::USER);

if (!isset($ksession)) {
	die("Could not establish Kaltura session. Please verify that you are using valid Kaltura partner credentials.");
}

$kclient->setKs($ksession);

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$kconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;

// Example of dependent multiple requests:
//     2nd request depends on output of 1st request
//     3rd request depends on output of 2nd request
//Start Multiple Request stream
$kclient->startMultiRequest(); // This causes the requests below to be queued (not executed)

$movie = realpath('./piano.m4v');

// Request #1 -- upload file
$kclient->media->upload($movie);  // Nothing returned since we are just queueing it for later execution

// Define the name and type of the file we're uploading
$entry = new KalturaMediaEntry();
$entry->name = "Great Piano Finale";
$entry->mediaType = KalturaMediaType::VIDEO;

// Request #2 -- Create a Kaltura Entry from the uploaded file
$kclient->media->addFromUploadedFile($entry, '{1:result}');  // Nothing returned since we are just queueing it for later execution
 
$url = 'http://www.8notes.com/pictures/piano/piano2.jpg'; // Image for piano thumbnail
// Request #3 -- Make the thumbnail out of an image fetched via this URL
$kclient->media->updateThumbnailFromUrl('{2:result:id}', $url); // Nothing returned since we are just queueing it for later execution

// Here we cause the previous queueed requests to be executed and it will return
// a combined response from all three requests
$result = $kclient->doQueue();

echo "<h3>PHP Structures returned by multirequest</h3>';
echo '<pre>';
print_r($result);
echo '</pre>';

// Note: resets to non-multirequest automatically after completion

?>