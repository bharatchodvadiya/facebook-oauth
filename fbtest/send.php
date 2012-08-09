<?php
	
   require_once 'facebook/facebook.php';
   $app_id = "193194090693725";
   $app_secret = "4ed3e5207d1868ab2952bde614e951eb";
	
	$facebook = new Facebook(array(
		'appId' => $app_id,
		'secret' => $app_secret,
		'cookie' => true
	));
	
	
	$post =  array(
   
    'message' => 'This message is posted with access token of nirav - ' . date('Y-m-d H:i:s')
);

//and make the request
$res = $facebook->api('/100003818200590/feed', 'POST', $post);

	?>