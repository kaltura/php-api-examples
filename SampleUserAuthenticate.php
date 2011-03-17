<?php

// This sample illustrates how to do web services from an application without
// requiring the user secret. This is suitable for mobile apps or desktop apps
// where you want to let any Kaltura user authenticate and use web services.
//
// This version assumes that the user ALREADY EXISTS in the KMC
// First step: You need to go to your KMC account and create a new user.
// Go to the "Administration" tab and click on "Add User". If you don't have
// an "Administration" tab in your KMC, then you need to contact support.
// Only the Dragonfly release (March 2011 or later) of the KMC has this capability.

// In this example, we list the first 30 videos in your KMC account

// Set the partner ID, username, email, and password for the user
define("PARTNER_ID", "123456");
define("USERNAME", "fredcurl"); // If no username is assigned, the username will be the email addy
define("EMAIL", 'freddycurlzzzz@gmail.com');
define("PASSWORD",  '99$99kal');

require_once "KalturaClient.php";

// The user you created in your KMC
$user = new KalturaUser();
$user->id = USERNAME; // If there is no username, then this should be the email addy
$user->email = EMAIL;
$user->password = PASSWORD;

$kconf = new KalturaConfiguration(PARTNER_ID);
// $kconf->serviceUrl = "http://myKalturaSite.com";
$kclient = new KalturaClient($kconf);

$ksession = $kclient->user->login(PARTNER_ID, $user->id, $user->password);

if (!isset($ksession)) {
	die("Could not establish Kaltura session. Please verify that you are using valid Kaltura partner credentials.");
}

$kclient->setKs($ksession);

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$kconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;

$kfilter = new KalturaMediaEntryFilter();
$kfilter->mediaTypeEqual = KalturaMediaType::VIDEO;
$kfilter->status = KalturaEntryStatus::READY;

$result = $kclient->media->listAction($kfilter);

echo "<h1>My Videos</h1>";
echo "<table>";
foreach ($result->objects as $entry) {
	echo '<tr><td><img src="'.$entry->thumbnailUrl.'">&nbsp;&nbsp;
    Title: '.$entry->name.'&nbsp;&nbsp;
     <a href="'.$entry->downloadUrl.'">download</a>&nbsp;&nbsp;
     Created on: '.date("D M j G:i:s T Y", $entry->createdAt).'</td></tr>';
}
echo "</table>";

?>