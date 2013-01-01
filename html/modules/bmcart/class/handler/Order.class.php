<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
/*
 * {Dirname}_{Filename} : Naming convention for Model
 */
class bmorder_orderObject extends XoopsSimpleObject
{
    public function __construct()
    {
        $this->initVar('order_id', XOBJ_DTYPE_INT, 0);
	    $this->initVar('shipping_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true);
	    $this->initVar('item_id', XOBJ_DTYPE_INT, 0);
	    $this->initVar('price', XOBJ_DTYPE_INT, 0, true);
	    $this->initVar('shipping_fee', XOBJ_DTYPE_INT, 0, true);
	    $this->initVar('qty', XOBJ_DTYPE_INT, 0, true);
	    $this->initVar('last_update', XOBJ_DTYPE_INT, 0);
    }
}

class bmorder_orderHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bmorder_order';
    public $mPrimary = 'order_id';
    public $mClass = 'bmorder_orderObject';
    public $id;
	private $myHandler;

    public function __construct(&$db)
    {
        parent::XoopsObjectGenericHandler($db);
    }
	public function &getItems(){
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$this->myHandler = xoops_getModuleHandler('order');
		$objects = $this->myHandler->getObjects($criteria);
		return $objects;
	}

	public function &getByItemId($item_id)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('item_id', $item_id));
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$this->myHandler = xoops_getModuleHandler('order');
		$objects = $this->myHandler->getObjects($criteria);
		if (!is_array($objects)){
			return null;
		}
		return $objects[0];
	}

	public function addToCart(&$dataList)
	{
		$orderObject = $this->getByItemId($dataList['item_id']);
		if(is_null($orderObject)){
			$orderObject = $this->myHandler->create();
			$orderObject->set('item_id',$dataList['item_id']);
			$orderObject->set('uid',Legacy_Utils::getUid());
			$orderObject->set('qty',1);
		}else{
			$orderObject->set('qty',$orderObject->getVar('qty')+1);
		}
		$orderObject->set('last_update',time());
		$this->myHandler->insert($orderObject,true);
	}

}
