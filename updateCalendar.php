<?php

require_once '/srv/www/htdocs/www3/gmailSig/vendor/autoload.php'; //google-api-php-lib

////Set up service account credentials for Google Client////
$user_to_impersonate = 'iy_singh@laurentian.ca';
$client_email = '888016315731-bq4o36a9vs5q8klndouo06cdf577a8mj@developer.gserviceaccount.com';
$private_key = file_get_contents('/srv/www/htdocs/www3/gmailSig/GW-Gmail Sync-139d0d75acdb.p12');
$scopes = array('https://www.googleapis.com/auth/calendar');

$credentials = new Google_Auth_AssertionCredentials(
    $client_email,
    $scopes,
    $private_key,
    'notasecret',                                 // Default P12 password
    'http://oauth.net/grant_type/jwt/1.0/bearer', // Default grant type
    $user_to_impersonate
);

////Get access_token from app service account impersonating user////
$client = new Google_Client();
$client->setAssertionCredentials($credentials);
if ($client->getAuth()->isAccessTokenExpired()) {
    $client->getAuth()->refreshTokenWithAssertion();
}
$access_token = json_decode($client->getAccessToken())->access_token;

echo $access_token;

///Call Email Settings API with access_token to update user signature////
//$username = $_REQUEST['username']; //Get username of currently logged in LUNET user
//$signatureValue = $_REQUEST['signature']; //Signature retrieved from preview HTML
//$signatureValue = str_replace('"',"'", $signatureValue); //Replace all " with '

//XML PUT data containing signature


//$client->setAccessToken($access_token);
	print "<a class='logout' href='http://www.daimto.com/Tutorials/PHP/GCOAuth.php?logout=1'>LogOut</a><br>";	
	
	$service = new Google_Service_Calendar($client);    
	
	$calendarList  = $service->calendarList->listCalendarList();;


	print_r($calendarList);



	while(true) {
		foreach ($calendarList->getItems() as $calendarListEntry) {

			


			echo $calendarListEntry->getSummary()."\n";


			// get events 
			$events = $service->events->listEvents($calendarListEntry->id);


			foreach ($events->getItems() as $event) {
			    echo "-----".$event->getSummary()."\n";
			}
		}
		$pageToken = $calendarList->getNextPageToken();
		if ($pageToken) {
			$optParams = array('pageToken' => $pageToken);
			$calendarList = $service->calendarList->listCalendarList($optParams);
		} else {
			break;
		}
	}


$event = new Google_Service_Calendar_Event(array(
  'summary' => 'Google I/O 2020',
  'location' => '800 Howard St., San Francisco, CA 94103',
  'description' => 'A chance to hear more about Google\'s developer products.',
  'start' => array(
    'dateTime' => '2015-12-28T09:00:00-07:00',
    'timeZone' => 'America/Los_Angeles',
  ),
  'end' => array(
    'dateTime' => '2015-12-30T17:00:00-07:00',
    'timeZone' => 'America/Los_Angeles',
  ),
  'recurrence' => array(
    'RRULE:FREQ=DAILY;COUNT=2'
  ),
  'attendees' => array(
    array('email' => 'mblais@laurentian.ca'),
    array('email' => 'ml_laferriere@laurentian.ca'),
  ),
  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'email', 'minutes' => 24 * 60),
      array('method' => 'popup', 'minutes' => 10),
    ),
  ),
));

$calendarId = 'iy_singh@laurentian.ca';
$event = $service->events->insert($calendarId, $event);
printf('Event created: %s\n', $event->htmlLink);



//file_put_contents('/srv/www/htdocs/www3/gmailSig/signature.log', $response);
?>
