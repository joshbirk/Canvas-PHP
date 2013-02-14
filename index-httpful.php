<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('httpful/src/Httpful/Bootstrap.php');
\Httpful\Bootstrap::init();

$consumer_secret = $_ENV['secret'];
$signedRequest = $_REQUEST['signed_request'];
if ($signedRequest == null) {
   echo "Error: Signed Request Failed.  Is the app in Canvas?";
}

$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);

$req = json_decode(base64_decode($encodedEnv));
$access_token = $req->client->oauthToken;
$instance_url = $req->client->instanceUrl;

$uri = $instance_url."/services/data/v26.0/query?q=SELECT+ID,NAME+FROM+ACCOUNT";
$result = \Httpful\Request::get($uri)
    ->Authorization("OAuth ".$access_token)                
    ->addHeader("Content-Type","application/json") 
    ->send();
$result = json_decode($result); //auto-parsing doesn't seem to work

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Account List</title>

</head>
<body>
<H1>Account List</H1>
<table>
	<tr><td>Account Name</td></tr>
	<?foreach($result->records as $account) {
		echo "<tr><td>".$account->Name."</td></tr>";		
	}?>
</body>
</html>
