<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 10:29
 * To change this template use File | Settings | File Templates.
 */
require_once _MY_MODULE_PATH . 'app/Model/Cart.php';
require_once _MY_MODULE_PATH . 'app/Model/PageNavi.class.php';
require_once _MY_MODULE_PATH . 'app/View/view.php';
class Controller_CartList extends AbstractAction {
	protected $myObjects;
	protected $shipping_fee=0;
	protected $sub_total=0;
	protected $total_amount=0;
	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Cart::forge();
	}
	public function action_index(){
		$this->myObjects = $this->mHandler->getMyCartItems();
		$itemHandler = xoops_getmodulehandler('item');
		foreach ($this->myObjects as $object){
			$itemObject = $itemHandler->get($object->getVar('item_id'));
			$amount = $itemObject->getVar('price') * $object->getVar('qty');
			$this->mListData[$object->getVar('cart_id')] = array(
				'cart_id' => $object->getVar('cart_id'),
				'item_id' => $itemObject->getVar('item_id'),
				'item_name' => $itemObject->getVar('item_name'),
				'price' => $itemObject->getVar('price'),
				'qty' => $object->getVar('qty'),
				'amount' => $amount
			);
			if ($this->shipping_fee < $itemObject->getVar('shipping_fee')){
				$this->shipping_fee = $itemObject->getVar('shipping_fee');
			}
			$this->sub_total += $amount;
		}
		$this->total_amount = $this->sub_total + $this->shipping_fee;
		$this->template = 'cartList.html';
	}
	public function action_removeItem(){
		if (isset($this->mParams[0])) $cart_id = intval($this->mParams[0]);
		$cartHandler = xoops_getmodulehandler('cart');
		if ($object = $cartHandler->get($cart_id)){
			if ( $object->getVar('uid')==Legacy_Utils::getUid() ){
				$cartHandler->delete($object,true);
			}
		}
		$this->action_index();
	}
	public function action_update(){
		$this->myObjects = $this->mHandler->getMyCartItems();
		foreach ($this->myObjects as $object){
				$cart_id = $object->getVar('cart_id');
				if (isset($_POST['qty_'.$cart_id])){
					$qty = intval(xoops_getrequest('qty_'.$cart_id));
					$object->set('qty',$qty);
					$object->set('last_update',time());
					$this->mHandler->update($object);
				}
		}
		if (isset($_POST['checkout'])){
			$this->executeRedirect("../checkout", 3, 'Checkout');
		}
		$this->action_index();
	}
	public function action_view(){
		$view = new View($this->root);
		$view->setTemplate($this->template);
		$view->set('ticket_hidden',$this->mTicketHidden);
		$view->set('ListData', $this->mListData);
		$view->set('shipping_fee', $this->shipping_fee);
		$view->set('total_amount', $this->total_amount);
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
	}
}