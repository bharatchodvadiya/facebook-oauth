<?php

class Social_Facebook_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getConnectUrl()
	{
		return Mage::getUrl('facebook/index');
	}
}
	 