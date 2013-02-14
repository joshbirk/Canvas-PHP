<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$consumer_secret = $_ENV['secret'];
$signedRequest = $_REQUEST['signed_request'];
if ($signedRequest == null) {
   echo "Error: Signed Request Failed.  Is the app in Canvas?";
}

$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);

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