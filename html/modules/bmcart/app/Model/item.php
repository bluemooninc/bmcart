<?php
/* $Id: $ */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * Utility
 */
class Model_Item extends AbstractModel {
	protected $_item_types = array();
	protected $_item_names = array();
	protected $myHandler;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->_module_names = $this->getModuleNames();
		$this->myHandler =& xoops_getModuleHandler('item');
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
			$instance = new Model_Item();
		}
		return $instance;
	}

	public function getName($id)
	{
		$obj = $this->myHandler->get($id);
		$ret = isset($obj) ? $obj->getVar("item_name") : NULL;
		return $ret;
	}

	public function getItems($limit=0, $offset=0, $whereArray=null)
	{
		$this->myHandler =& xoops_getModuleHandler('item');
		$this->_item_names = $this->myHandler->getItem($limit, $offset, $whereArray,
			array(
				array("name" => "name", "sort" => "ASC")
			)
		);
		return $this->_item_names;
	}
	public function &getItemDetail($item_id)
	{
		$this->myHandler =& xoops_getModuleHandler('item');
		$object = $this->myHandler->get($item_id);
		$ret=array();
		foreach($object->mVars as $key=>$val){
			$ret[$key]=$object->getVar($key);
		}
		return $ret;
	}

	public function getFindInSet($limit, $offset, $whereArray, $orderArray)
	{
		$this->myHandler =& xoops_getModuleHandler('item');
		$this->_item_names = $this->myHandler->FindInSet($limit, $offset, $whereArray, $orderArray);
		return $this->_item_names;
	}

	/**
	 * get XoopsItem Count
	 * @param none
	 * @return int Count
	 */
	public function getXoopsItemCount()
	{
		$item_handler =& xoops_gethandler('item');
		$objs = $item_handler->getObjects();

		return count($objs);
	}

	public function update($field_name, $value, $whereArray = NULL)
	{
		$criteria = new CriteriaCompo();
		if ($whereArray) {
			$criteria->add(new Criteria($whereArray['key'], $whereArray['val'], $whereArray['operator']));
		}
		$item_handler = xoops_gethandler('item');
		$objects = $item_handler->getObjects($criteria);
		foreach ($objects as $obj) {
			$obj->set($field_name, $value);
		}
		$ret = $item_handler->insert($obj, true);
		return $ret;
	}

	/**
	 * get ItemNames
	 * @param none
	 * @return array ( key: item_id, value: name )
	 */
	public function getItemNames()
	{
		return $this->_item_names;
	}


	/**
	 * get ItemName By item_id
	 * @param int $id
	 * @return string item_name
	 */
	public function getItemName($id)
	{
		$id = intval($id);
		if (isset($this->_item_names[$id])) {
			return $this->_item_names[$id]['name'];
		}
		return FALSE;
	}

	/**
	 * get My ModuleId
	 * @param none
	 * @return int module_id
	 */
	public function getMyModuleDirName()
	{
		global $xoopsModule;
		if (is_object($xoopsModule)) {
			return $xoopsModule->getVar('dirname');
		}
		return FALSE;
	}


	/**
	 * is XoopsAdmin
	 * @param none
	 * @return boolean XoopsAdmin
	 */
	public function isXoopsAdmin()
	{
		global $xoopsUser;
		if (is_object($xoopsUser)) {
			return $xoopsUser->isAdmin();
		}
		return FALSE;
	}

//-----------------
// protected
//-----------------
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

}

?>