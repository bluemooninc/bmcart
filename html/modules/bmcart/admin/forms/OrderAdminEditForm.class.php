<?php
/**
 * @package user
 * @version $Id: OrderAdminEditForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class bmcart_OrderAdminEditForm extends XCube_ActionForm
{
	var $mOldFileName = null;
	var $_mIsNew = false;
	var $mFormFile = null;

	function getTokenName()
	{
		return "module.bmcart.OrderAdminEditForm.TOKEN" . $this->get('order_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['order_id'] =new XCube_IntProperty('order_id');
		$this->mFormProperties['category_id'] =new XCube_IntProperty('category_id');
		$this->mFormProperties['first_name'] =new XCube_StringProperty('first_name');
		$this->mFormProperties['last_name'] =new XCube_StringProperty('last_name');
		$this->mFormProperties['zip_code'] =new XCube_StringProperty('zip_code');
		$this->mFormProperties['state'] =new XCube_StringProperty('state');
		$this->mFormProperties['address'] =new XCube_StringProperty('address');
		$this->mFormProperties['address2'] =new XCube_StringProperty('address2');
		$this->mFormProperties['payment_type'] =new XCube_IntProperty('payment_type');
		$this->mFormProperties['sub_total'] =new XCube_IntProperty('sub_total');
		$this->mFormProperties['tax'] =new XCube_IntProperty('tax');
		$this->mFormProperties['shipping_fee'] =new XCube_IntProperty('shipping_fee');
		$this->mFormProperties['amount'] =new XCube_IntProperty('amount');

		//
		// Set field properties
		//
		$this->mFieldProperties['order_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['order_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['order_id']->addMessage('required', _MD_BMCART_ERROR_REQUIRED, _MD_BMCART_ITEMID);
	
		$this->mFieldProperties['first_name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['first_name']->setDependsByArray(array('required'));
		$this->mFieldProperties['first_name']->addMessage('required', _MD_BMCART_ERROR_REQUIRED, _AD_BMCART_ITEM_NAME, '255');

		$this->mFieldProperties['last_name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['last_name']->setDependsByArray(array('required'));
		$this->mFieldProperties['last_name']->addMessage('required', _MD_BMCART_ERROR_REQUIRED, _AD_BMCART_ITEM_NAME, '255');

	}

	function load(&$obj)
	{
		$this->set('order_id', $obj->get('order_id'));
		$this->set('first_name', $obj->get('first_name'));
		$this->set('last_name', $obj->get('last_name'));
		$this->set('zip_code', $obj->get('zip_code'));
		$this->set('state', $obj->get('state'));
		$this->set('address', $obj->get('address'));
		$this->set('address2', $obj->get('address2'));
		$this->set('payment_type', $obj->get('payment_type'));
		$this->set('order_date', $obj->get('order_date'));
		$this->set('paid_date', $obj->get('paid_date'));
		$this->set('shipping_date', $obj->get('shipping_date'));
	}

	function update(&$obj)
	{
		$obj->set('order_id', $this->get('order_id'));
		$obj->set('first_name', $this->get('first_name'));
		$obj->set('last_name', $this->get('last_name'));
		$obj->set('zip_code', $this->get('zip_code'));
		$obj->set('state', $this->get('state'));
		$obj->set('address', $this->get('address'));
		$obj->set('address2', $this->get('address2'));
		$obj->set('payment_type', $this->get('payment_type'));
		$obj->set('order_date', $this->get('order_date'));
		$obj->set('paid_date', $this->get('paid_date'));
		$obj->set('shipping_date', $this->get('shipping_date'));
	}
}

?>
