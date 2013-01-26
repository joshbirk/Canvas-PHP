<?php
error_reporting(-1);

$consumer_secret = $_ENV['secret'];
$signedRequest = $_REQUEST['signed_request'];

$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);

$calcedSig = base64_encode(hash_hmac("sha256", $encodedEnv, $consumer_secret, true));
if ($calcedSig != $encodedSig) {
   echo "Error: Signed Request Failed.  Is the app in Canvas?";
}

$req = json_decode(base64_decode($encodedEnv));
$access_token = $req->oauthToken;
$instance_url = $req->instanceUrl;

$ch = curl_init($instance_url.'/services/data/v26.0/query?q=SELECT+ID,NAME+FROM+ACCOUNT');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization:OAuth '.$access_token,
    'Content-Type:application/json'
    ));
$result = json_decode(curl_exec($ch));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Account List</title>
	<meta name="generator" content="TextMate http://macromates.com/">
	<meta name="author" content="Joshua Birk">
	<!-- Date: 2013-01-26 -->
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
