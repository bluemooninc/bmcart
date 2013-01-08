<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 10:31
 * To change this template use File | Settings | File Templates.
 */
if (!defined('XOOPS_ROOT_PATH')) exit();

class Model_Order extends AbstractModel {
	protected $myHandler;
	protected $myObjects;
	protected $shipping_fee=0;
	protected $sub_total=0;
	protected $total_amount=0;

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
			$instance = new Model_Order();
		}
		return $instance;
	}


	public function &getOrderItems($order_id)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('order_id', $order_id));
		$this->myHandler = xoops_getModuleHandler('orderItems');
		$this->myObjects = $this->myHandler->getObjects($criteria);
		return $this->myObjects;
	}

	public function &getOrderList()
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('uid', Legacy_Utils::getUid()));
		$criteria->addSort('order_date', 'DESC');
		$this->myHandler = xoops_getModuleHandler('order');
		$this->myObjects = $this->myHandler->getObjects($criteria);
		return $this->myObjects;
	}
	public function &isSubTotal(){
		return $this->sub_total;
	}
	public function &isTotalAmount(){
		return $this->total_amount;
	}
	public function &isShippingFee(){
		return $this->shipping_fee;
	}
	public function update()
	{
		$this->_getMyOrderItems();
		foreach ($this->myObjects as $object){
			$order_id = $object->getVar('order_id');
			if (isset($_POST['qty_'.$order_id])){
				$qty = intval(xoops_getrequest('qty_'.$order_id));
				$object->set('qty',$qty);
				$object->set('last_update',time());
				$this->myHandler->insert($object);
			}
		}
	}
	public function clearMyOrder(){
		$this->myHandler->deleteAll(new Criteria('uid', Legacy_Utils::getUid()));
	}
}