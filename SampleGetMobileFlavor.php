<?php
require_once 'mobiled.php';
require_once "KalturaClient.php";

// Shows how to get a mobile flavor.
// This will only work if you have already transcoded your video into
// mobile flavors. Otherwise, you'll just end up downloading the source video.
// Also remember to grab the file mobiled.php from SVN to use with this.
//
// TODO:  Use http://code.google.com/p/mobileesp instead of mobiled.php
//
// You need to provide the entry ID to one of the videos in your account.
// It should be added here:
$myEntryID = '';
if (empty($myEntryID)) {
	die("<strong>You must provide an entry ID for one of the videos in your account</strong>");
}

// Your Kaltura partner credentials
define("PARTNER_ID", "nnnnnn");
define("ADMIN_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("USER_SECRET",  "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"); 

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

try {
	$kclient->startMultiRequest();
	$kclient->media->get($myEntryID);
	$kclient->flavorAsset->getByEntryId($myEntryID);
	$entry = $kclient->doQueue();
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
	exit(0);
}

$m = new mobiled();
// notmobile will also be the value for unknown handsets. This needs to be improved, obviously.
$client_type = ($m->detect()) ? trim(strtolower($m->getVersion())) : 'notmobile';

// Following flavor tags represent flavors that are mobile-playable:
// 'iphone' - h264 for iphone3/android and on via progressive download
// 'ipad' - h264 for ipad and iphone4 and on via progressive download
// 'iphonenew' - h264 for iphone3 and on via progressive download and Akamai HD Network
// 'ipadnew' - h264 for ipad and iphone4 and on via progressive download and Akamai HD Network
// 'mobile,mpeg4' - visual mpeg4 for the older video capable cellphones, via progressive download
// Older entries may have just "iphone" tag.
// Newer entries with iphonenew also have "iphone"

switch ($client_type) {
case 'ipod':
case 'iphone':
case 'android':
	foreach ($entry[1] as $flavor) {
		if ( strpos( $flavor->tags, 'iphonenew' ) !== false ) {
			$theurl = $kclient->flavorAsset->getDownloadUrl($flavor->id);
			$size = round($flavor->size/1024,1);
			break 2;
		}
	}
	// If iphonenew search failes, look again and see if an entry has just the iphone tag (not optimal)
	foreach ($entry[1] as $flavor) {
		if ( strpos( $flavor->tags, 'iphone' ) !== false ) {
			$theurl = $kclient->flavorAsset->getDownloadUrl($flavor->id);
			$size = round($flavor->size/1024,1);
			break 2;
		}
	}
case 'ipad':
	foreach ($entry[1] as $flavor) {
		if ( strpos( $flavor->tags, 'ipadnew' ) !== false ) {
			$theurl = $kclient->flavorAsset->getDownloadUrl($flavor->id);
			$size = round($flavor->size/1024,1);
			break 2;
		}
	}
case 'blackberry':
default:
	foreach ($entry[1] as $flavor) {
		if ( strpos($flavor->tags, 'mobile') !== false and strpos($flavor->tags, 'mpeg4') !== false  ) {
			$theurl = $kclient->flavorAsset->getDownloadUrl($flavor->id);
			$size = round($flavor->size/1024,1);
			break 2;
		}
	}
	// If nothing matches, use the original source
	$theurl = $kclient->flavorAsset->getDownloadUrl($entry[1][0]->id);
	$size = round($entry[1][0]->size/1024,1);
}

// Display duration
if ($entry[0]->duration < 60) {
	$runtime = $entry[0]->duration;
	$scale = 'seconds';
} elseif ($entry[0]->duration < 600) {
	$runtime = round($entry[0]->duration/60, 1); // show fractions for less than 10 mins
	$scale = 'minutes';
} else {
	$runtime = round($entry[0]->duration/60);
	$scale = 'minutes';
}

$description = (empty($entry[0]->description)) ? '[empty]' : $entry[0]->description;

echo '<strong>Thumbnail:</strong><img src="'.$entry[0]->thumbnailUrl.'" /><br />
             <strong>Title: </strong>'.$entry[0]->name.'<br />
             <strong>Description: </strong>'.$description.'<br />
             <strong>Length: </strong>'.$runtime.' '.$scale.'<br />
             <strong>Size: </strong>'.$size.' mbytes<br />
            <strong>Uploaded on: </strong>'.date("D M j G:i:s T Y", $entry[0]->createdAt).'<br />
             	<a href="'.$theurl.'">VIEW/DOWNLOAD MOBILE VIDEO</a>';

?>