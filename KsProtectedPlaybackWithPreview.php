<html>
<head>
</head>
<body>
<?php
/**
* Access Control of KS protected with preview video playback
* This sample code shows a simple setup of a session protected playbacl with preview time as defined in the KMC Access Control Profile.
**/

// Your Kaltura credentials
define("PARTNER_ID", "--Set your partner id here--");
define("USER_SECRET", "--Set your user secret here--");
define("ENTRY_ID", "--Set the entry id of a video with relevant KS access control applied that belong to the above partner id--");
define("UICONF_ID", "--Set the uiconf id of the player you'd like to use. You can find this id in the players list on the Studio tab in KMC--");
$user = "SomeoneWeKnow";  // Whatever user id you want to set (This is according to your system deployment)

require_once "client/KalturaClient.php";

//Connect to your authorization system here, this is just a mockup for the example...
$approved = false;
$user_name = $_POST['username'];
$password = $_POST['password'];

if ($user_name == 'demo' && $password == 'pass') 
{
    $approved = true;
} else {
    $approved = false;
}

//Create a session
$conf = new KalturaConfiguration(PARTNER_ID);
$client = new KalturaClient($conf);
//This is the session start function signature: start($secret, $userId = "", $type = 0, $partnerId = -1, $expiry = 86400, $privileges = "")
//It is important that we pass the special permission "sview:ENTRY_ID" to provide access for the specific entry we want to play.
//This generated KS (Kaltura Session) will then be rendered to the page if the user has permissions to access the full video.
//If the user doesn't have permissions to access the full video, we'll not render a KS in the flashvars, 
//and Kaltura will only stream the preview part of the video as defined in the access control profile.
$session = $client->session->start(USER_SECRET, $user, KalturaSessionType::USER, PARTNER_ID, 86400, 'sview:'.ENTRY_ID);

if (!isset($session)) {
	die("Could not establish Kaltura session with OLD session credentials. Please verify that you are using valid Kaltura partner credentials.");
}

$client->setKs($session);

echo '<h1>Status: '.($approved ? 'Approved' : 'Denied').'</h1>';
$flashvars = '';
if ($approved) {
	$flashvars .= 'ks=' . $session;
}

if ($approved == false)
	echo '<form method="POST" action="">
			UserName: <input value="demo" type="text" name="username">
			Password: <input value="pass" type="password" name="password">
			<input type="submit" value="Login">
		</form>';
?>

<object id="kaltura_player" name="kaltura_player" type="application/x-shockwave-flash" allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" height="333" width="400" xmlns:dc="http://purl.org/dc/terms/" xmlns:media="http://search.yahoo.com/searchmonkey/media/" rel="media:video" resource="http://www.kaltura.com/index.php/kwidget/wid/_<?php echo PARTNER_ID; ?>/uiconf_id/<?php echo UICONF_ID; ?>/entry_id/<?php echo ENTRY_ID; ?>" data="http://www.kaltura.com/index.php/kwidget/wid/_<?php echo PARTNER_ID; ?>/uiconf_id/<?php echo UICONF_ID; ?>/entry_id/<?php echo ENTRY_ID; ?>">
	<param name="allowFullScreen" value="true" />
	<param name="allowNetworking" value="all" />
	<param name="allowScriptAccess" value="always" />
	<param name="bgcolor" value="#000000" />
	<param name="flashVars" value="<?php echo $flashvars; ?>" />
	<param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/_<?php echo PARTNER_ID; ?>/uiconf_id/<?php echo UICONF_ID; ?>/entry_id/<?php echo ENTRY_ID; ?>" />
</object>

</body>
</html>