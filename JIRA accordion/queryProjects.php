<?php

$ch = curl_init('http://jira.laurentian.ca:8080/rest/api/2/project?expand=lead');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "apiadmin:apiadmin1!");
curl_setopt($ch, CURLOPT_HEADER, "Content-Type: application/json" );

echo curl_exec($ch);

curl_close($ch);

?>