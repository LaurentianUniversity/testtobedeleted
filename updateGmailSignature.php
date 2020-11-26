<?php

require_once '/srv/www/htdocs/www3/gmailSig/vendor/autoload.php'; //google-api-php-lib

////Set up service account credentials for Google Client////
$user_to_impersonate = 'apiadmin@laurentian.ca';
$client_email = '106554062551-s1o3nmasi5h23lugsnorojg9h18istdj@developer.gserviceaccount.com';
$private_key = file_get_contents('/srv/www/htdocs/www3/gmailSig/myEmailSignature-e98fb9c6503d.p12');
$scopes = array('https://apps-apis.google.com/a/feeds/emailsettings/2.0/');

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

////Call Email Settings API with access_token to update user signature////
$username = $_REQUEST['username']; //Get username of currently logged in LUNET user
$signatureValue = $_REQUEST['signature']; //Signature retrieved from preview HTML
$signatureValue = str_replace('"',"'", $signatureValue); //Replace all " with '

//XML PUT data containing signature
$XMLData = "<?xml version='1.0' encoding='utf-8'?>".
    "<atom:entry xmlns:atom='http://www.w3.org/2005/Atom' xmlns:apps='http://schemas.google.com/apps/2006'>".
    "<apps:property name='signature' value='".$signatureValue."'/>".
    "</atom:entry>";

//Make curl PUT request to API
$headers = array(
    "Content-type: application/atom+xml",
    "Content-length: " . strlen($XMLData),
    "Authorization: Bearer ".$access_token
);

$ch = curl_init("https://apps-apis.google.com/a/feeds/emailsettings/2.0/laurentian.ca/".$username."/signature");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $XMLData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

//echo $response;
file_put_contents('/srv/www/htdocs/www3/gmailSig/signature.log', $response);
?>