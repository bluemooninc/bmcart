<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 10:29
 * To change this template use File | Settings | File Templates.
 */
require_once _MY_MODULE_PATH . 'app/Model/Order.php';
require_once _MY_MODULE_PATH . 'app/Model/item.php';
require_once _MY_MODULE_PATH . 'app/Model/PageNavi.class.php';
require_once _MY_MODULE_PATH . 'app/View/view.php';
class Controller_OrderList extends AbstractAction {
	protected $mListData;
	protected $itemNames;
	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Order::forge();
	}
	public function action_index(){
		$this->mListData = $this->mHandler->getOrderList();
		$this->template = 'orderList.html';
	}
	public function action_orderDetail(){
		$itemHandler = Model_Item::forge();
		if (isset($this->mParams[0])) $order_id = intval($this->mParams[0]);
		$objects = $this->mHandler->getOrderItems($order_id);
		foreach($objects as $object){
			$item = array();
			$itemId = $object->getVar('item_id');
			foreach($object->mVars as $key=>$val){
				$item[$key] = $val['value'];
			}
			$item['item_name'] = $itemHandler->getName($itemId);
			$item['amount'] = $item['price']*$item['qty'];
			$this->mListData[$itemId] = $item;
		}
		$this->template = 'orderItems.html';
	}
	public function action_view(){
		$view = new View($this->root);
		$view->setTemplate($this->template);
		$view->set('ticket_hidden',$this->mTicketHidden);
		$view->set('ListData', $this->mListData);
		$view->set('itemNames', $this->itemNames);
		$view->set('shipping_fee', $this->mHandler->isShippingFee() );
		$view->set('total_amount', $this->mHandler->isTotalAmount() );
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
	}
}