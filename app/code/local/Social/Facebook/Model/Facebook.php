<?php
class Social_Facebook_Model_Facebook  extends Social_Connectors_Model_Abstract implements Social_Connectors_Model_Interface
{

	public function login()
	{
		//FB.getLoginStatus();
		$facebook=$this->getFacebookObject();
		// See if there is a user from a cookie
		$user = $facebook->getUser();
		
		if ($user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $facebook->api('/me');
				
			} catch (FacebookApiException $e) {
				//echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
				$user = null;
				return FALSE;
			}
		}
	
		return $user_profile;
	}
	public function logout()
	{
		$facebook=$this->getFacebookObject();
		$facebook->destroySession();
		setcookie('fbsr_' . $api_key, $_COOKIE['fbsr_' . $api_key], time() - 3600, '/', '.'.$_SERVER['SERVER_NAME']);
	}
	public function setConnectorName()
	{
		$connector_name='Facebook';
		return $connector_name;
	}
	public function getFacebookObject()
	{
		$connector_name=$this->setConnectorName();
		$api_key=$this->getApiKey($connector_name);
		$api_secretkey=$this->getSecret($connector_name);
		
		require_once(Mage::getBaseDir('lib') . '/Connectors/facebook/facebook.php');
		$facebook = new Facebook(array(
		    	  'appId'  =>$api_key,
		    	  'secret' =>$api_secretkey,
				  'cookie' => true
		));
			return $facebook;
	}
	public function getFacebookaccesstoken()
	{					
		$facebook=$this->getFacebookObject();
		$access_token = $facebook->getAccessToken();
		return $access_token;
	}
	public function getFacebookProfileImage()
	{
		$facebook=$this->getFacebookObject();
		// See if there is a user from a cookie
		$user = $facebook->getUser();
		
		if ($user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $facebook->api('/me');
				$user_api_id=$user_profile['id'];
				$headers = get_headers('https://graph.facebook.com/'.$user_api_id.'/picture?type=large',1);
				$profileimage = $headers['Location'];
				
			} catch (FacebookApiException $e) {
				//echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
				$user = null;
				return null;
			}
		}
		return $profileimage;
	}
	
	// this facebook user friend with admin or not
	public function checkfacebookfriends($adminid)
	{
	  $admindata = file_get_contents('https://graph.facebook.com/'.$adminid);
	  $fblike = json_decode($admindata,true);
	  if($fblike['id']!='')
		{
				$oauth_token=$this->getFacebookaccesstoken();
				$frienddata = file_get_contents('https://graph.facebook.com/me/friends?access_token='.$oauth_token);
				$fbdata = json_decode($frienddata,true);
		        $i=0;
					while(isset($fbdata['data'][$i]['id']))
					{
						if($fbdata['data'][$i]['id']==$fblike['id'])
					      return true;
					$i++;
					}
			$admindata = file_get_contents('https://graph.facebook.com/me/feed?access_token='.$oauth_token);
			$admindata1 = json_decode($admindata,true);
			if($admindata1['data'][0]['from']['id']==$fblike['id'])
				return true;
		}
		return false;
	}
	
	// this facebook page(admin) like or not
	public function checkfacebookLike($adminid)
	{
	  $adminlike = file_get_contents('https://graph.facebook.com/'.$adminid);
	  $fblike = json_decode($adminlike,true);
      if($fblike['id']!='')
      {
      	$oauth_token=$this->getFacebookaccesstoken();
      	$frienddata = file_get_contents('https://graph.facebook.com/me/likes?access_token='.$oauth_token);
      	$fbdata = json_decode($frienddata,true);
	          $i=0;
				while(isset($fbdata['data'][$i]['id']))
				{
					if($fbdata['data'][$i]['id']==$fblike['id'])
						return true;
				    $i++;
				}
      }
	return false;
	}
}