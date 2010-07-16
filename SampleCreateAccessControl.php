<?php

// Create an Access Control profile
// BUG: This script bombs -- what's wrong?

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

$kclient->setKs($ksession);

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$kconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;

$acl = new KalturaAccessControl;
$acl->name = "VariousRestrictions";
$acl->description = "Restrict North Korea and Madagascar and some weird sites from access";

$countryRestrictions = new KalturaCountryRestriction;
$countryRestrictions->countryRestrictionType = KalturaCountryRestrictionType::RESTRICT_COUNTRY_LIST;
$countryRestrictions->countryList = "KP,MG"; 

$siteRestrictions = new KalturaSiteRestriction;
$siteRestrictions->siteRestrictionType = KalturaSiteRestrictionType::RESTRICT_SITE_LIST;
$siteRestrictions->siteList = "www.someweirdhackersitezzxxx.com,www.xhdgfjslehdlsjd.com";

// Array of restriction objects
$acl->restrictions[0] = $countryRestrictions;
$acl->restrictions[1] = $siteRestrictions;

$result = $kclient->accessControl->add($acl);

print_r($result);

?>