<?php
class Social_Facebook_IndexController extends Mage_Core_Controller_Front_Action{
	
const EXCEPTION_NOT_ACTIVE_BY_FBADMIN = 2;
	
public function IndexAction()
	{
		if(isset($_REQUEST['fbloginerror'])|| !isset($_REQUEST) || $_REQUEST['email']=='')
		{
			//Mage::getSingleton('core/session')->addError('Unfortunately, there is a problem with facebook. Please login with using your facebook username and the password sent to you at the time of registration. if you are not register/login before in this website then please registrer your account. If you forgot your password, you can have it reset by email.');
			Mage::getSingleton('core/session')->addError('Unfortunately, there is a problem with Facebook connect. Please login with username and password sent to you at the time of registration.');
			$this->_redirect('customer/account');
			return;
		}
		
		$user_info=$_REQUEST;
		$connector_name='Facebook';
		//$user_info=  Mage::getModel('facebook/facebook')->login();
		/*if($user_info==FALSE)
		//{
			Mage::getSingleton('core/session')->addError('Unfortunately, there is a problem with facebook. Please login with using your facebook username and the password sent to you at the time of registration. if you are not register/login before in this website then please registrer your account. If you forgot your password, you can have it reset by email.');
			$this->_redirect('customer/account');
			return;
		}*/
		$email = $user_info['email'];
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		$customer = Mage::getModel("customer/customer");
		$customer->website_id = $websiteId;
		$customer->setStore($store);
		$customer->loadByEmail($email);
		
		if($customer->getGroupId()==6)
		{
			$message = $this->__('<h3>Your account is disabled.</h3>');
			Mage::getSingleton('core/session')->addError($message);
			$this->_redirect('customer/account');
			return;
		}
		
		$firstname = $user_info['first_name'];
		$lastname = $user_info['last_name'];
		$birthday =$user_info['birthday'];
		$gender =$user_info['gender'];
		$user_api_id=$user_info['id'];
		$user_name=$user_info['name'];
	//	$user_city=$user_info['location'];
		//$city=$user_city['name'];
		$city=$user_info['location'];
		$oauth_token= Mage::getModel('facebook/facebook')->getFacebookaccesstoken();
		$oauth_token_secret='';
		$groupId='';
		if(!$customer->getId())
		{
			$permission = Mage::getModel('customeraccess/customeraccess')->getfacebookaccesscheck();
			if($permission['facebook_permission_check']=='ON')
			{
				if(Mage::getModel('facebook/facebook')->checkfacebookFriends($permission['facebook_admin_id']) || Mage::getModel('facebook/facebook')->checkfacebookLike($permission['facebook_page_id']))
				$groupId='';
				else
				$groupId=5;
			}
			$pfbimages = get_headers('https://graph.facebook.com/'.$user_info['id'].'/picture?type=large',1);
			$profileimage = $pfbimages['Location']; //facebook profile image
			$fbpass=$customer->generatePassword(9);
			$customer=Mage::getModel('connectors/connectors')->setCustomer($firstname,$lastname,$email,$birthday,$gender,5,$fbpass);
			Mage::getModel('facebook/facebook')->setUser($email,0,$connector_name,$user_api_id,$user_name,$oauth_token,$oauth_token_secret,$profileimage,$gender,$city);
			
			Mage::getModel('connectors/connectors')->setfbpimage($email,$profileimage);
			
			$from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
			$from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
			if($from_email !='' && $from_name !='')
			{		
			$mailTemplate = Mage::getModel('core/email_template');
			/* @var $mailTemplate Mage_Core_Model_Email_Template */
			$translate  = Mage::getSingleton('core/translate');
			
			$templateId = 2; //template for sending customer data
			$template_collection =  $mailTemplate->load($templateId);
			$template_data = $template_collection->getData();
			if(!empty($template_data))
			{
			$templateId = $template_data['template_id'];
			$mailSubject = $template_data['template_subject'];
			
			//fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact
			 $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
			 $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
			$sender = array('name'  => $from_name,
			'email' => $from_email);
								
			$vars = array('customer'=>$customer); //for replacing the variables in email with data
			/*This is optional*/
			$storeId = Mage::app()->getStore()->getId();
			$model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
			$email = $customer->getEmail();
			$name = $customer->getName();
			
			$model->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);

			/*
			 * send mail check getSentSuccess => WHEN live this site then remove this comment.
			 * 
			 echo $storeId."hiiiiiiii";
			 var_dump($sender);
		
			 if (!$mailTemplate->getSentSuccess()) {
                 echo "no";die;
			 	throw new Exception();
			}
		         	$translate->setTranslateInline(true);
			*/
			}
			//echo $fbpass;die;
			
			//Make a "login" of new customer
			}
			if($groupId==5)
			{ 	
				$fri='';
				if($permission['facebook_admin_label']!='')
			    	$fri=', please friend <a href="http://www.facebook.com/'.$permission['facebook_admin_id'].'" target="_blank">'.$permission['facebook_admin_label'].'</a> on facebook';
				else if($permission['facebook_admin_id']!='')
			    	$fri=', please friend <a href="http://www.facebook.com/'.$permission['facebook_admin_id'].'" target="_blank">'.$permission['facebook_admin_id'].'</a> on facebook';
				
				if($permission['facebook_page_label']!='')
				    $fri=$fri.' or like us on <a href="http://www.facebook.com/'.$permission['facebook_page_id'].'" target="_blank">'.$permission['facebook_page_label'].'</a> page.';
				else if($permission['facebook_page_id']!='')
				    $fri=$fri.' or like us on <a href="http://www.facebook.com/'.$permission['facebook_page_id'].'" target="_blank">'.$permission['facebook_page_id'].'</a> page.';
				else
				   $fri=$fri.'.';
				$message = $this->__('<h3>Thanks! We are reviewing your application. We will review your request and invite you soon. For quicker turnaround'.$fri.'</h3>');
				Mage::getSingleton('core/session')->addSuccess($message);
			    $this->_redirect('customer/account');
			    return;
			 }
			else
			{
			Mage::getSingleton('customer/session')->loginById($customer->getId());
			$message = $this->__('<h3>Thank you for registering with <b>SOHYPER</b>.</h3>');
			Mage::getSingleton('core/session')->addSuccess($message);
			$this->_redirect('customer/account');
			return;
			}
		}
		else
		{
			if($customer->getGroupId()!=5 && $customer->getGroupId()!=4)
			{	
				$pfbimages = get_headers('https://graph.facebook.com/'.$user_info['id'].'/picture?type=large',1);
			    $profileimage = $pfbimages['Location']; //facebook profile image
			    Mage::getModel('facebook/facebook')->setUser($email,0,$connector_name,$user_api_id,$user_name,$oauth_token,$oauth_token_secret,$profileimage,$gender,$city);
				//Make a "login" of  customer
			    Mage::getModel('connectors/connectors')->setfbpimage($email,$profileimage);
			   Mage::getSingleton('customer/session')->loginById($customer->getId());
			   
			   $message = $this->__('<h3>Thank you for your continued support with SOHYPER.</h3>');
			   Mage::getSingleton('core/session')->addSuccess($message);
				$this->_redirect('customer/account');
				return;
			}
			else 
			{
				$groupId='';
				$permission = Mage::getModel('customeraccess/customeraccess')->getfacebookaccesscheck();
				if($permission['facebook_permission_check']=='ON')
				{
					if(Mage::getModel('facebook/facebook')->checkfacebookFriends($permission['facebook_admin_id']) || Mage::getModel('facebook/facebook')->checkfacebookLike($permission['facebook_page_id']))
					$groupId='';
					else
					$groupId=5;
				}
				 
			if($groupId!=5)
			{ 	
				$pfbimages = get_headers('https://graph.facebook.com/'.$user_info['id'].'/picture?type=large',1);
			    $profileimage = $pfbimages['Location']; //facebook profile image
				Mage::getModel('facebook/facebook')->setUser($email,0,$connector_name,$user_api_id,$user_name,$oauth_token,$oauth_token_secret,$profileimage,$gender,$city);
				//Make a "login" of  customer
				Mage::getModel('connectors/connectors')->setfbpimage($email,$profileimage);
				
				Mage::getSingleton('customer/session')->loginById($customer->getId());
				
				 $message = $this->__('<h3>Thank you for your continued support with SOHYPER.</h3>');
				Mage::getSingleton('core/session')->addSuccess($message);
				$this->_redirect('customer/account');
				return;
			}
			else
			{	
				$fri='';
			    if($permission['facebook_admin_label']!='')
			    	$fri=', please friend <a href="http://www.facebook.com/'.$permission['facebook_admin_id'].'" target="_blank">'.$permission['facebook_admin_label'].'</a> on facebook';
				else if($permission['facebook_admin_id']!='')
			    	$fri=', please friend <a href="http://www.facebook.com/'.$permission['facebook_admin_id'].'" target="_blank">'.$permission['facebook_admin_id'].'</a> on facebook';
				
				if($permission['facebook_page_label']!='')
				    $fri=$fri.' or like us on <a href="http://www.facebook.com/'.$permission['facebook_page_id'].'" target="_blank">'.$permission['facebook_page_label'].'</a> page.';
				else if($permission['facebook_page_id']!='')
				    $fri=$fri.' or like us on <a href="http://www.facebook.com/'.$permission['facebook_page_id'].'" target="_blank">'.$permission['facebook_page_id'].'</a> page.';
				else
				$fri=$fri.'.';
					
				$message = $this->__('<h3>We will review your request and invite you soon. For quicker turnaround'.$fri.'</h3>');
				Mage::getSingleton('core/session')->addSuccess($message);
				$this->_redirect('customer/account');
				return;
			}
			}
				
		}
	}
	
	public function disconnectAction()
	{
		$identityid = $_REQUEST['mid'];
		$email=Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		$connector_name='Facebook';
		Mage::getModel('facebook/facebook')->setDisconnectConnector($email,$connector_name,$identityid);
		Mage::getModel('facebook/facebook')->logout();
		$this->_redirect('customer/social');
	}
	public function connectAction()
	{
		if(isset($_REQUEST['fbloginerror'])|| !isset($_REQUEST))
		{
			Mage::getSingleton('core/session')->addError('Unfortunately, there is a problem with Facebook connect. Please login with username and password sent to you at the time of registration.');
			$this->_redirect('customer/account');
			return;
		}
		$mid = $_REQUEST['first_name'];
		$user_info=$_REQUEST;
		
		$user_name=$user_info['name'];
		$gender =$user_info['gender'];
		$user_api_id=$user_info['id'];
		$user_city=$user_info['location'];
		$city=$user_city['name'];
		
		$oauth_token= Mage::getModel('facebook/facebook')->getFacebookaccesstoken();
		$oauth_token_secret='';
		
		$email=Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		$connector_name='Facebook';
		$user_api_id=$user_info['id'];
		
		$pfbimages = get_headers('https://graph.facebook.com/'.$user_info['id'].'/picture?type=large',1);
		$profileimage = $pfbimages['Location']; //facebook profile image
		
		$getdata= Mage::getModel('facebook/facebook')->checkConnected($user_api_id,$connector_name,$mid);
		
		if($getdata)
		{
			$user = $getdata['user_screen_name'];
			$identity = $getdata['identity_id'];
			$identity1 = Mage::getModel('facebook/facebook')->getidentityname($identity);
			
			$message='Your connection is already being used by &lt;'.$user.'&gt; [with identity &lt;'.$identity1['f_name'].'&gt;]';
			Mage::getSingleton('core/session')->addError($message);
			$this->_redirect('customer/social');
			return;		
		}
		else 
		{
		Mage::getModel('facebook/facebook')->setUser($email,$mid,$connector_name,$user_api_id,$user_name,$oauth_token,$oauth_token_secret,$profileimage,$gender,$city);
		$this->_redirect('customer/social');
		}
	}
}