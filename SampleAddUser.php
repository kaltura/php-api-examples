<?php

// Add a new user to an account
// Run the SampleListUsers.php script to verify the new user is in
// the account

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("USER_SECRET",  "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");

require_once "KalturaClient.php";

$user = "SomeoneWeKnow";  
$kconf = new KalturaConfiguration(PARTNER_ID);
// If you want to use the API against your self-hosted CE,
// go to your KMC and look at Settings -> Integration Settings to find your partner credentials
// and add them above. Then insert the domain name of your CE below.
// $kconf->serviceUrl = "http://www.mySelfHostedCEsite.com/";
$ksession = $kclient->session->start(ADMIN_SECRET, $user, KalturaSessionType::ADMIN);

if (!isset($ksession)) {
	die("Could not establish Kaltura session. Please verify that you are using valid Kaltura partner credentials.");
}

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$kconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;

$kclient->setKs($ksession);

$kuser = New KalturaUser;
$kuser->id = "MadeUpId888";
//$kuser->partnerId 
$kuser->screenName = "FredJones";
$kuser->fullName = "Fred Q. Jones";
$kuser->email = "FredQQQJones@somezzzzmail.com";
//$kuser->dateOfBirth 
//$kuser->country 
//$kuser->state
//$kuser->city
//$kuser->zip 
//$kuser->thumbnailUrl 
$kuser->description = "A friendly fellow who likes quality video.";
//$kuser->tags
//$kuser->adminTags 
//$kuser->gender 
$kuser->status = KalturaUserStatus::ACTIVE;
//$kuser->createdAt 
//$kuser->updatedAt 
//$kuser->partnerData
//$kuser->indexedPartnerDataInt 
//$kuser->indexedPartnerDataString 
//$kuser->storageSize 

$result = $kclient->user->add($kuser);

echo "<h3>The New User</h3>";
print_r($result);
	
?>