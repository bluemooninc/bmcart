<?php
/**
 * Copyright(c): Bluemoon inc. 2013
 * Author: Y.SAKAI
 * Licence : GPL Ver.3
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 10:31
 * To change this template use File | Settings | File Templates.
 */
if (!defined('XOOPS_ROOT_PATH')) exit();

class Model_Checkout
{
	protected $myHandler;
	protected $myObjects;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->_module_names = $this->getModuleNames();
		$this->myHandler = xoops_getModuleHandler('order');
	}

	/**
	 * get Instance
	 * @param none
	 * @return object Instance
	 */
	public function &forge()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new Model_Checkout();
		}
		return $instance;
	}

	protected function getModuleNames($isactive = FALSE)
	{
		$criteria = new CriteriaCompo();
		if ($isactive) {
			$criteria->add(new Criteria('isactive', '1', '='));
		}
		$module_handler =& xoops_gethandler('module');
		$objs = $module_handler->getObjects($criteria);
		$ret = array();
		foreach ($objs as $obj) {
			$ret[$obj->getVar('mid')] = $obj->getVar('name');
		}
		return $ret;
	}

	/**
	 * @param null $order_id
	 */
	private function _getMyOrder($order_id = null)
	{
		$criteria = new CriteriaCompo();
		if (!is_null($order_id)) {
			// get fixed order
			$criteria->add(new Criteria('order_id', $order_id));
		} else {
			// get current order
			$criteria->add(new Criteria('order_date', null));
		}
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$this->myObjects = $this->myHandler->getObjects($criteria);
	}

	private function &_getMyLastOrder()
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$criteria->addSort('order_date', 'DESC');
		return $this->myHandler->getObjects($criteria);
	}

	public function getCurrentOrder()
	{
		$this->_getMyOrder();
		if (count($this->myObjects) > 0) {
			return $this->myObjects[0];
		} else {
			$newObject = $this->myHandler->create();
			$objects = $this->_getMyLastOrder();
			if (count($objects) > 0) {
				$newObject->set('first_name', $objects[0]->getVar('first_name'));
				$newObject->set('last_name', $objects[0]->getVar('last_name'));
				$newObject->set('zip_code', $objects[0]->getVar('zip_code'));
				$newObject->set('state', $objects[0]->getVar('state'));
				$newObject->set('address', $objects[0]->getVar('address'));
				$newObject->set('address2', $objects[0]->getVar('address2'));
				$newObject->set('phone', $objects[0]->getVar('phone'));
				$newObject->set('sub_total', $objects[0]->getVar('sub_total'));
				$newObject->set('tax', $objects[0]->getVar('tax'));
				$newObject->set('shipping_fee', $objects[0]->getVar('shipping_fee'));
				$newObject->set('amount', $objects[0]->getVar('amount'));
			}
			$this->myHandler->insert($newObject, true);
			return $newObject;
		}
		return null;
	}

	public function addNewAddress()
	{
		$object = $this->myHandler->create();
		$this->myHandler->insert($object, true);
	}

	public function update()
	{
		$this->_getMyOrder();
		if ($this->myObjects) {
			$object = $this->myObjects[0];
			$object->set('first_name', xoops_getrequest('first_name'));
			$object->set('last_name', xoops_getrequest('last_name'));
			$object->set('phone', xoops_getrequest('phone'));
			$object->set('zip_code', xoops_getrequest('zip_code'));
			$object->set('state', xoops_getrequest('state'));
			$object->set('address', xoops_getrequest('address'));
			$object->set('address2', xoops_getrequest('address2'));
			$this->myHandler->insert($object);
		}
	}

	public function setOrderStatus($order_id,$payment_type, $cardOrderId = null,$subTotal,$tax,$shipping_fee,$amount)
	{
		$this->_getMyOrder($order_id);
		$ret = false;
		if ($this->myObjects) {
			$object = $this->myObjects[0];
			$object->set('payment_type', $payment_type);
			$object->set('card_order_id', $cardOrderId);
			$object->set('sub_total', $subTotal);
			$object->set('tax', $tax);
			$object->set('shipping_fee', $shipping_fee);
			$object->set('amount', $amount);
			$object->set('order_date', time());
			$this->myHandler->insert($object);
			$ret = true;
		}
		return $ret;
	}
	public function &myObject(){
		return $this->myObjects[0];
	}

	public function moveCartToOrder($ListData, $order_id)
	{
		$itemHandler = xoops_getModuleHandler('orderItems');
		foreach ($ListData as $itemObject) {
			$addObject = $itemHandler->create();
			$addObject->set('order_id', $order_id);
			$addObject->set('item_id', $itemObject['item_id']);
			$addObject->set('price', $itemObject['price']);
			$addObject->set('qty', $itemObject['qty']);
			$itemHandler->insert($addObject);
		}
	}

}