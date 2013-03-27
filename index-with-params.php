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
$consumer_secret = $_ENV['consumer_secret'];
if ($signedRequest == null || $consumer_secret == null) {
   echo "Error: Signed Request or Consumer Secret not found";
}

//decode the signedRequest
$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);
$calcedSig = base64_encode(hash_hmac("sha256", $encodedEnv, $consumer_secret, true));	  
if ($calcedSig != $encodedSig) {
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

$invoiceID = $req->context->environment->parameters->invoiceId;
//define your URI based on the user's instance 
$uri = $instance_url."/services/data/v26.0/query?q=SELECT+ID,Merchandise__r.Name+FROM+Line_Item__c+WHERE+Invoice__c+=+'".$invoiceID."'";

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
	<script>
	console.log(<?=base64_decode($encodedEnv)?>);
	</script>
	<style>
	body { font-family: Verdana; }
	table { width: 80%; }
	h4 { border-bottom:thick dotted #00ff00; }
	td { border-bottom:1px solid black; }
	</style>
</head>
<body>
<H4>Warehouse Tracking</H4>
<table>
	<tr >
		<td><b>Item</b></td><td><b>Stock Row</b></td><td><b>Stock Column</b></td></tr>
	<?foreach($result->records as $rec) {
		echo "<tr><td>".$rec->Merchandise__r->Name."</td>";
		echo "<td>".rand(5, 15)."</td>";
		echo "<td>".rand(5, 15)."</td>";
		echo"</tr>";		
	}?>
	</table>
</body>
</html>