<?php
/**
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

	private function _getMyOrder()
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$criteria->addSort('order_date','DESC');
		$this->myObjects = $this->myHandler->getObjects($criteria);
	}
	public function update(){
		$this->_getMyOrder();
		if ($this->myObjects){
			$object = $this->myObjects[0];
		}else{
			$object = $this->myHandler->create();
		}
		$object->set('first_name',xoops_getrequest('first_name'));
		$object->set('last_name',xoops_getrequest('last_name'));
		$object->set('phone',xoops_getrequest('phone'));
		$object->set('zip_code',xoops_getrequest('zip_code'));
		$object->set('state',xoops_getrequest('state'));
		$object->set('address',xoops_getrequest('address'));
		$object->set('address2',xoops_getrequest('address2'));
		$this->myHandler->insert($object);
	}
}