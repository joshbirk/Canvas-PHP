<?php
//turn on reporting for all errors and display
error_reporting(E_ALL);
ini_set("display_errors", 1);

//Httpful is a excellent curl-based wrapper for REST services
//See: https://github.com/nategood/httpful
//Heroku does not support phar's normally, so using bootstrap here
require('httpful/src/Httpful/Bootstrap.php');
\Httpful\Bootstrap::init();

//In Canvas via SignedRequest/POST, the authentication should be passed via the signed_request header
//You can also use OAuth/GET based flows
$signedRequest = $_REQUEST['signed_request'];
if ($signedRequest == null) {
   echo "Error: Signed Request Failed.  Is the app in Canvas?";
}

//decode the signedRequest
$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);

//decode the signed request object
$req = json_decode(base64_decode($encodedEnv));

//As of Spring '13: SignedRequest has a client object which holds the pertinent authentication info
$access_token = $req->client->oauthToken;
$instance_url = $req->client->instanceUrl;

//define your URI based on the user's instance 
$uri = $instance_url."/services/data/v26.0/query?q=SELECT+ID,NAME+FROM+ACCOUNT";

//create REST request and decode result
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
	</table>
</body>
</html>
