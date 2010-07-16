<?php

// BUG: There seems to be no way to get a particular feed by feed type. All
// syndication feeds are returned. Is there a way to just
// get the Yahoo format feeds? 

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

// Get all syndication feeds in the KMC
$search = new KalturaBaseSyndicationFeedFilter;
$result = $kclient->syndicationFeed->listAction($search, $pager = null);

// List by Feed Type
$size = sizeof($result->objects);
echo "<h3>Your Feeds</h3>";
if ($size == 0) {
	echo "You have no syndication feeds in your KMC. Go create some.";
} else {
	for ($i = 0; $i < $size; $i++) {
		echo "Name: ".$result->objects[$i]->name."</br>";
		echo "Feed URL: ".$result->objects[$i]->feedUrl."</br>";
  	    echo "Created At: ".date(DATE_RFC822, $result->objects[$i]->createdAt)."</br></br>";
	}
}
?>