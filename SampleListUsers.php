<?php

// List all users

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("USER_SECRET",  "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");

require_once "KalturaClient.php";

$user = "SomeoneWeKnow";  
$kconf = new KalturaConfiguration(PARTNER_ID);
$kclient = new KalturaClient($kconf);
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

$kfilter = null;
$pager = null;

$result = $kclient->user->listAction($kfilter, $pager);

echo "<h3>Your Users</h3>";
foreach ($result->objects as $entry) {
	echo "User ID: ".((empty($entry->id))? "unknown" : $entry->id)."</br>";
	// Status: 0=BLOCKED, 1=ACTIVE, 2=DELETED. See KalturaUserStatus object
    echo "Status: ".((empty($entry->status))? "unknown" : $entry->status)."</br>";
    echo "PartnerID: ".((empty($entry->partnerId))? "unknown" : $entry->partnerId)."</br></br>";
}
	
?>