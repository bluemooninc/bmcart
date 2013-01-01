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
		$this->myHandler =& xoops_getModuleHandler('order');
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

	public function &getMyOrderItems( )
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$criteria->addSort('last_update','DESC');
		$this->myHandler = xoops_getModuleHandler('order');
		$this->myObjects = $this->myHandler->getObjects($criteria);
		return $this->myObjects;
	}
	public function update(&$object){
		$this->myHandler->insert($object);
	}
}