<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
 
	require 'facebook/facebook.php';
 
	$facebook = new Facebook(array(
		'appId'  => "193194090693725",
		'secret' => "4ed3e5207d1868ab2952bde614e951eb",
		"cookie" => true,
		'fileUpload' => true
	));
	$user_id = $facebook->getUser();
	echo "<pre>";
	echo "<img src='https://graph.facebook.com/$user_id/picture'>";
	
	//$user_profile = $facebook->api('/me/photos');
	//print_r($user_profile);
	//exit;
	//	$user_id='100003816078683';
	$file_name='images.jpg';	
	$facebook->setFileUploadSupport(true);
	$args = array('message' => 'Profile Photo');
	$args['image'] = '@' . realpath($file_name);
	
	$data = $facebook->api('/me/photos', 'post', $args);
	
	$pictue = $facebook->api('/'.$data['id']);
	$fb_image_link = "http://www.facebook.com/photo.php?fbid=".$data['id']."&set=a.101544459988377.1671.100003984693577&type=3&theater&makeprofile=1";
	
 	//redirect to uploaded photo url and change profile picture
	echo "<script type='text/javascript'>top.location.href = '$fb_image_link';</script>";
?>