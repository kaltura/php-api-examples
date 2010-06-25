<?php

// Search for videos using an external video source
// BUG: Pager does not work
// BUG: Video URLs are not returned in results

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

$pager = New KalturaFilterPager;
$pager->pageSize = 10;
$pager->pageIndex = 1;

$search = New KalturaSearch;
$search->keyWords = "Dogs";
// Search for videos on archive.org
$search->searchSource = KalturaSearchProviderType::ARCHIVE_ORG;
$search->mediaType = KalturaMediaType::VIDEO;
$search->extraData = null;

$result = $kclient->search->search($search, $pager);

print_r($result);

?>