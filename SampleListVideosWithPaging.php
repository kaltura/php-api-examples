<?php

// List all the videos in the KMC, a "page" at a time
//
// BUGS: Used $entry->dataUrl (original uploaded video) because $entry->downloadUrl doesn't work
// for the default samples in the SaaS accounts.....!
// Where are the downloadUrls for the default samples in the account?

// Your Kaltura partner credentials
define("PARTNER_ID", "xxxxxx");
define("ADMIN_SECRET", "yyyyyyyyyyyyyyyyyyyyyyyyyyy");
define("USER_SECRET",  "zzzzzzzzzzzzzzzzzzzzzzzzzz");

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

// Make sure video is done transcoding or whatever
$kfilter->status = KalturaEntryStatus::READY;

// List in descending order
$kfilter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_DESC;
// $kfilter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;

// Create pager
$pager = new KalturaFilterPager();

// choose the pageSize -- number of items per call
// choose the pageIndex -- which page we're on now (page "1" is the first page)
$pager->pageSize = 5;
$pager->pageIndex = 1;

echo "<h1>My Videos</h1>";

$result = $kclient->media->listAction($kfilter, $pager);
$count = $result->totalCount; // total number of items in the account
echo "<h1>Total: $result->totalCount Videos </h1>";

while (!empty($result->objects)) {
	echo "<h2>Page $pager->pageIndex</h2>";
	echo "<table>";
	foreach ($result->objects as $entry) {
		echo '<tr><td><img src="'.$entry->thumbnailUrl.'">&nbsp;&nbsp;
    Title: '.$entry->name.'&nbsp;&nbsp;
     <a href="'.$entry->dataUrl.'">download</a>&nbsp;&nbsp;
     Created on: '.date("D M j G:i:s T Y", $entry->createdAt).'</td></tr>';
	}
	echo "</table>";
	$pager->pageIndex++;
	$result = $kclient->media->listAction($kfilter, $pager);
}

?>