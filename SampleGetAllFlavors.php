<?php

// List the first 30 videos in your KMC and links to all available flavors.
//
// This uses several separate web services calls in order to gather all the
// data. It would be much more efficient to use the MultiRequest method to
// make several requests per web service call.
//

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
     Created on: '.date("D M j G:i:s T Y", $entry->createdAt).'&nbsp;&nbsp<br />Flavors: ';
	$r = $kclient->flavorAsset->getByEntryId($entry->id);
	foreach ($r as $e) {
		if ($e->status != KalturaFlavorAssetStatus::READY) continue;
		echo '<a href="'.$kclient->flavorAsset->getDownloadUrl($e->id).'">'.$e->containerFormat.'/'.$e->videoCodecId.'</a>&nbsp;&nbsp';
	}
	echo '</td></tr>';
}
echo "</table>";

?>