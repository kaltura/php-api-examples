<?php

// Upload a file from a URL to the KMC

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

$movieURL = "http://upload.wikimedia.org/wikipedia/commons/c/c3/Nachtfalter_2.ogv";

$entry = new KalturaMediaEntry();

$entry->name = "Random Video TEST";
$entry->mediaType = KalturaMediaType::VIDEO;

$result = $kclient->media->addFromUrl($entry, $movieURL);

echo '<h3>Media entry structure returned</h3>';
echo '<pre>';
print_r($result);
echo '</pre>';

?>