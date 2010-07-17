<?php

// Utility program used to transfer all assets from one 
// Kaltura account to another

// Your old Kaltura CE credentials
define("PARTNER_ID", "xxxxxx");
define("ADMIN_SECRET", "nnnnnnnnnnnnnnnnnnnnnnnnnnnnn");
define("USER_SECRET", "nnnnnnnnnnnnnnnnnnnnnnnnnnnn");

// Your new Kaltura CE credentials
define("NEW_PARTNER_ID", "xxxxxx");
define("NEW_ADMIN_SECRET", "nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn");
define("NEW_USER_SECRET", "nnnnnnnnnnnnnnnnnnnnnnnnnnnnnn");

define("BULKFILENAME", "transfer.csv");

require_once "KalturaClient.php";

// Return media type as string
function getMediaType($type) {
   if ($type == 5) {
       $media = 'Audio';
     } elseif ($type == 2) {
       $media = 'Image';
     } else {
       $media = "Video";
     }
return $media;
}

// Get session for old stuff  ********************
$user = "SomeoneWeKnow";  // If this user does not exist in your KMC, then it will be created.
$OLDkconf = new KalturaConfiguration(PARTNER_ID);
// If you want to use the API against your self-hosted CE,
// go to your KMC and look at Settings -> Integration Settings to find your partner credentials
// and add them above. Then insert the domain name of your CE below.
// $OLDkconf->serviceUrl = "http://www.mySelfHostedCEsite.com/";
$OLDkclient = new KalturaClient($OLDkconf);
$OLDksession = $OLDkclient->session->start(ADMIN_SECRET, $user, KalturaSessionType::ADMIN);

if (!isset($OLDksession)) {
	die("Could not establish Kaltura session with OLD session credentials. Please verify that you are using valid Kaltura partner credentials.");
}

$OLDkclient->setKs($OLDksession);

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$OLDkconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;
// ***********************************************

// Get session for new stuff  ********************
$user = "SomeoneWeKnow";  // If this user does not exist in your KMC, then it will be created.
$NEWkconf = new KalturaConfiguration(NEW_PARTNER_ID);
// If you want to use the API against your self-hosted CE,
// go to your KMC and look at Settings -> Integration Settings to find your partner credentials
// and add them above. Then insert the domain name of your CE below.
// $NEWkconf->serviceUrl = "http://www.mySelfHostedCEsite.com/";
$NEWkclient = new KalturaClient($NEWkconf);
$NEWksession = $NEWkclient->session->start(NEW_ADMIN_SECRET, $user, KalturaSessionType::ADMIN);

if (!isset($NEWksession)) {
	die("Could not establish Kaltura session with NEW session credentials. Please verify that you are using valid Kaltura partner credentials.");
}

$NEWkclient->setKs($NEWksession);

// Set the response format
// KALTURA_SERVICE_FORMAT_JSON  json
// KALTURA_SERVICE_FORMAT_XML   xml
// KALTURA_SERVICE_FORMAT_PHP   php
$NEWkconf->format = KalturaClientBase::KALTURA_SERVICE_FORMAT_PHP;
// ***********************************************

// Get list of files
// $kfilter = new KalturaMediaEntryFilter();
// $kfilter->mediaTypeEqual = KalturaMediaType::VIDEO;
// $kfilter->mediaTypeEqual = KalturaMediaType::IMAGE;
// $kfilter->mediaTypeEqual = KalturaMediaType::AUDIO;
// Use $kfilter as the argument if you only want to transfer a certain file type. 
// Using null as the argument for listAction returns all file types in the CE

echo "Fetching your old media.....</br>";
$result = $OLDkclient->media->listAction(null);

// CSV fields: Name, Description, Tag, URL, Media Type, Transcoding Profile ID,	 
// Access Control Profile ID, Category, Scheduling Start Date, Scheduling End Date,	 
// Thumbnail URL, Partner Data
// The CSV file for bulk uploads must have either 5 fields (V1) or 12 fields (V2).
// I use 12 fields below (empty fields have the empty string "")

// Use dataUrl or downloadUrl ?
$bigstr = "";
foreach ($result->objects as $entry) {
    $media = getMediaType($entry->mediaType);
	$bigstr .= '"'.$entry->name.'","'.$entry->description.'","'.$entry->tags.'","'.$entry->dataUrl.'","'.$media.'","","","'.$entry->categories.'","","","'.$entry->thumbnailUrl.'",""'."\n";
}

echo "Writing your list of old media to the CSV file: ".BULKFILENAME."......</br>";
file_put_contents(BULKFILENAME,$bigstr);

// Is there a "no profile conversion" default?
$conversionProfileID = -1;  //use default

$CSVfile = realpath("./".BULKFILENAME);

echo "Initiating bulk upload......</br>";
$result = $NEWkclient->bulkUpload->add($conversionProfileID, $CSVfile);

echo '<h3>PHP Structure returned by bulk file upload</h3>';
echo '<pre>';
print_r($result);
echo '</pre>';

echo "Fetching your old user list.....</br>";
$result = $OLDkclient->user->listAction(null, null);
echo "Transferring your old users to your new site....</br>";
foreach ($result->objects as $entry) {
   try {
       $result = $NEWkclient->user->add($entry);
    } catch (Exception $e) {
         if (strpos($e,'already exists')) {
            echo "Skipping ".$e->getMessage()."</br>";
            continue;
         } else {
            // We should not be getting any errors besides 'already exists'
            die ($e->getMessage());
         }
    }  
    echo "User ID ".$entry->id." transferred. </br>";
}

?>