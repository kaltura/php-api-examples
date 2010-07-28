<?php

// Create an Access Control profile
// BUG: Does not list site restrictions in results

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
$acl->partnerId = PARTNER_ID;
$acl->createdAt = time();
$countryRestriction = new KalturaCountryRestriction();
$countryRestriction->countryRestrictionType = KalturaCountryRestrictionType::RESTRICT_COUNTRY_LIST;
$countryRestriction->countryList = 'KP,MG';

$siteRestriction = new KalturaSiteRestriction();
$siteRestriction->siteRestrictionType = KalturaSiteRestrictionType::RESTRICT_SITE_LIST;
$siteRestrictions->siteList = "www.someweirdhackersitezzxxx.com,www.xhdgfjslehdlsjd.com";

$restrictionArray = array();
$restrictionArray[] =$countryRestriction;
$restrictionArray[] =$siteRestriction;

$acl->restrictions = $restrictionArray;

$result = $kclient->accessControl->add($acl);

print_r($result);

?>