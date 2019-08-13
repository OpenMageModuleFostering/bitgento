<?php
/**
 * @author	ecloud solutions http://www.ecloudsolutions.com <info@ecloudsolutions.com>
 * @copyright Copyright (C) 2010 - 2014 ecloud solutions ®
 */
?><?php
class Ecloud_Bitgento_Model_Bitgento extends Mage_Core_Model_Abstract{

	public function insertBlock($observer){

		if(!Mage::getStoreConfig('bitgento_config/configuration/enabled',Mage::app()->getStore()))
			return;

		// Chequeo si estoy en la product page
		if(Mage::registry('current_product')){
			$_block = $observer->getBlock();
			$_type = $_block->getType();

			if ($_type == 'catalog/product_price'
				&& $_block->getTemplate() =='catalog/product/price.phtml'
				&& !($_block->getIdSuffix() == '_clone')) {

				$prodid = Mage::registry('current_product')->getId();
				$_product = Mage::getModel('catalog/product')->load($prodid);
				$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
				$jsonApi = file_get_contents('https://bitpay.com/api/rates/');		
				$jsonApi = json_decode($jsonApi, true);

				foreach ($jsonApi as $cd) {
					if($currency_code == $cd["code"]){
						$bitcoins = $_product->getPrice() / $cd["rate"];
						$_child = clone $_block;
						$_child->setType('test/block');
						$_block->setChild('child', $_child);
						$_block->setData('bitcoins',$bitcoins);
						$_block->setTemplate('bitgento/bitcoins.phtml');
					}
				}	
			}
		}
	}
}
