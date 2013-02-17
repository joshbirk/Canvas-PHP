<?php
//turn on reporting for all errors and display
error_reporting(E_ALL);
ini_set("display_errors", 1);

//In Canvas via SignedRequest/POST, the authentication should be passed via the signed_request header
//You can also use OAuth/GET based flows
$signedRequest = $_REQUEST['signed_request'];
$consumer_secret = $_ENV['secret'];

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

//decode the signed request object
$sr = base64_decode($encodedEnv);
?>

<script src='scripts/jquery-1.5.1.js'></script>
<script type="text/javascript" src="/sdk/js/canvas.js"></script>
<script type="text/javascript" src="/sdk/js/cookies.js"></script>
<script type="text/javascript" src="/sdk/js/oauth.js"></script>
<script type="text/javascript" src="/sdk/js/xd.js"></script>
<script type="text/javascript" src="/sdk/js/client.js"></script>
<script type="text/javascript" src="/scripts/json2.js"></script>
<script src='scripts/ICanHaz.js'></script>
<script>
        var url = "/services/data/v26.0/query?q=SELECT+ID,NAME+FROM+ACCOUNT";
		var sr = JSON.parse('<?=$sr?>');
		
        $(document).ready(function() {
		console.debug(sr);
		$('#user-name').html(sr.context.user.fullName);
		
		
		//within a Canvas iFrame, AJAX calls proxy via the window messaging
        Sfdc.canvas.client.ajax(url,
            {	client : sr.client,
                method: 'GET',
                contentType: "application/json",
                success : function(data) {
						console.debug('Got Data');
						console.debug(data);
						$('#accountTable').append(ich.accounts(data.payload));
                }
            }); 
		});
</script>

</script>
Hello, <span id='user-name'></span>
<table id="accountTable">
	<tr><td><strong>Recent Accounts</strong></td></tr>
    <script type="text/html" id="accounts">
			{{#records}}
            <tr><td>{{Name}}</td></tr> 
   			{{/records}}
    </script>
</table>
     
</apex:page>