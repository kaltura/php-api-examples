<?php

// This sample illustrates how to create a new user in your KMC and then
// login that user and do some web service calls. This is suitable for 
// some types of mobile apps or desktop apps
// where you want to let a user register as a Kaltura user and then have them
// authenticate and use web services.
//
// This version creates the user and then authenticates the user.
// and then we list the first 30 videos in your KMC account

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");

// The username, email, and password for the user you want to create
define("USERNAME", "fredcurl"); // If no username is assigned, the username will be the email addy
define("EMAIL", 'freddycurlzzzz@gmail.com');
define("PASSWORD",  '99$99kal');

require_once "KalturaClient.php";

// This is the user you want to create. There are many possible fields here.
$user = new KalturaUser();
$user->id = USERNAME; // If there is no username, then this should be the email addy
$user->email = EMAIL;
$user->password = PASSWORD;
$user->country = "USA";
$user->city = "Dime Box";
// Role IDs are unique to each account. The role IDs are not listed in the KMC.
// You have to do a web service call to find them: $kclient->userRole->listAction()
// $user->roleIds = "";

$kconf = new KalturaConfiguration(PARTNER_ID);
// $kconf->serviceUrl = "http://myKalturaSite.com";
$kclient = new KalturaClient($kconf);

$ksession = $kclient->session->start(ADMIN_SECRET, $userId = "admin", KalturaSessionType::ADMIN, PARTNER_ID);

if (!isset($ksession)) {
	die("Could not establish Kaltura session. Please verify that you are using valid Kaltura partner credentials.");
}

$kclient->setKs($ksession);

// Add the new user
$addedUser = $kclient->user->add($user);

// Now remove KS from client
$kclient->setKs(null);

// Start a new session with the newly created user
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