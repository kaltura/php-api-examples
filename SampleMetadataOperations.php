<?php

// SampleMetadataOperations.php
// Adds a custom metadata field to your KMC (normally done at Settings -> Custom Data)
// And then sets a value for that field for the first video retrieved.
// And then changes the value of the field.
//
//

require_once 'KalturaClient.php';

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

// The metadata field we'll add/update
$metaDataFieldName = 'SubtitleFormat';
$fieldValue = 'VobSub';

// The Schema file for the field
// Currently, you must build the xsd yourself. There is no utility provided.
$xsdFile = './MetadataSchema.xsd';

// Setup a pager and search to use
$pager = new KalturaFilterPager;
$search = new KalturaMediaEntryFilter;
$search->mediaTypeEqual=1; // Video only
$pager->pageSize = 10;
$pager->pageIndex = 1;

echo "<h3>START</h3>";

// Get 10 video entries, but we'll just use the first one returned
$entries = $kclient->media->listaction($search, $pager)->objects;

// Check if there are any custom fields defined in the KMC (Settings -> Custom Data)
// for the first item returned by the previous listaction
$filter = new KalturaMetadataProfileFilter();
$metadata = $kclient->metadataProfile->listAction($filter, $pager)->objects;
$profileId = $metadata[0]->id;
$name = (string)$entries[0]->name;
$id = (string)$entries[0]->id;

if (isset($metadata[0]->xsd)) {
	echo "<h3>1. There are custom fields for video: $name, entryid: $id </h3>";
} else {
	echo "<h3>1. There are no custom fields for video: $name, entryid: $id </h3>";
}

// Add a custom data entry in the KMC  (Settings -> Custom Data)
$profile = new KalturaMetadataProfile();
$profile->metadataObjectType = KalturaMetadataObjectType::ENTRY;
$viewsData = "";
$xsd = file_get_contents($xsdFile);
$metadata = $kclient->metadataProfile->update($profileId, $profile, $xsd, $viewsData);

if (isset($metadata->xsd)) {
	echo "<h3>2. Successfully created the custom data field $metaDataFieldName. </h3>";
} else {
	echo "<h3>2. Failed to create the custom data field. </h3>";
}

// Add the custom metadata value to the first video
$filter = new KalturaMetadataFilter();
$filter->objectIdEqual = $entries[0]->id;
$xmlData = '<metadata><SubtitleFormat>'.$fieldValue.'</SubtitleFormat></metadata>';
$metadata = $kclient->metadata->add($profileId, $profile, $entries[0]->id, $xmlData);

if (isset($metadata->xml)) {
	echo "<h3>3. Successfully added the custom data field for video: $name, entryid: $id </h3>";
	$xml = htmlspecialchars($metadata->xml, ENT_QUOTES);
	echo "<h3> XML used: $xml </h3>";
} else {
	echo "<h3>3. Failed to add the custom data field. </h3>";
}

// Now lets change the value (update) of the custom field

// Get the metadata for the video
$filter = new KalturaMetadataFilter();
$filter->objectIdEqual = $entries[0]->id;
$metadata = $kclient->metadata->listAction($filter)->objects;

if (isset($metadata[0]->xml)) {
	echo "<h3>4. Current metadata for video: $name, entryid: $id </h3>";
	$xmlquoted = htmlspecialchars($metadata[0]->xml, ENT_QUOTES);
	echo "<h3> XML: $xmlquoted </h3>";
	$xml = $metadata[0]->xml;
	// Make sure we find the old value in the current metadata
	$pos = strpos($xml, '<'.$metaDataFieldName.'>'.$fieldValue.'</'.$metaDataFieldName.'>');
	if ($pos === false) {
		echo "<h3>4. Failed to find metadata STRING for video: $name, entryid: $id </h3>";
	} else {
		$pattern = "@<".$metaDataFieldName.">(.+)</".$metaDataFieldName.">@";
		$xml = preg_replace($pattern, '<'.$metaDataFieldName.'>Ogg Writ</'.$metaDataFieldName.'>', $xml);
		$rc = $kclient->metadata->update($metadata[0]->id, $xml);
		echo "<h3>5. Updated metadata for video: $name, entryid: $id </h3>";
		$xmlquoted = htmlspecialchars($rc->xml, ENT_QUOTES);
		echo "<h3> XML: $xmlquoted </h3>";
	}
} else {
	echo "<h3>4. Failed to find metadata for video: $name, entryid: $id </h3>";
}

echo "<h3>FINISH</h3>";
?>