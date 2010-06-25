<?php

// Get report on top content
// BUG: This returns an empty array. It's not clear how to just get
//       the total stats on "top content". 

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

$reportType = KalturaReportType::TOP_CONTENT;
$kfilter = new KalturaReportInputFilter; 
//$kfilter->fromDate =
//$kfilter->toDate =
//$kfilter->keywords = 
//$kfilter->searchInTags = 
//$kfilter->searchInAdminTags = 
//$kfilter->categories = 
$dimension = null;
$objectIds = null;

$result = $kclient->report->getGraphs($reportType, $kfilter, $dimension, $objectIds);

print_r($result);
	
?>