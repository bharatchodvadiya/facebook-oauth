<?php 
require_once ("../app/Mage.php");
$app = Mage::app('default');
$basepath=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
$email = $_REQUEST['emailid'];
$websiteId = Mage::app()->getWebsite()->getId();
$store = Mage::app()->getStore();
$customer = Mage::getModel("customer/customer");
$customer->website_id = $websiteId;
$customer->setStore($store);
$customer->loadByEmail($email);
if(!$customer->getId())
  echo '0';
else
  echo '1';
?>
