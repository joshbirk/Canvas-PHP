<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$consumer_secret = $_ENV['secret'];
$signedRequest = $_REQUEST['signed_request'];

$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);

$calcedSig = base64_encode(hash_hmac("sha256", $encodedEnv, $consumer_secret, true));

if ($calcedSig != $encodedSig) {
   echo "Signed request authentication failed, this application must be used via Canvas";
}

$sr = base64_decode($encodedEnv);
$req = json_decode($sr);
$access_token = $req->oauthToken;
$instance_url = $req->instanceUrl;
$uri = $instance_url."/services/data/v26.0/query?q=SELECT+ID,NAME+FROM+ACCOUNT"
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
        var url = "<?=$uri?>";
		var sr = JSON.parse('<?=$sr?>');
		
        $(document).ready(function() {
		console.debug(sr);
		$('#user-name').html(sr.context.user.fullName);
        Sfdc.canvas.client.ajax(url,
            {	token : sr.oauthToken,
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
            <tr><td><a href="<?=$instance_url?>/{{Id}}" target="_new">{{Name}}</a></td></tr> 
   			{{/records}}
    </script>
</table>
     
</apex:page>