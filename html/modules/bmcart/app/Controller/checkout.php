<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 22:24
 * To change this template use File | Settings | File Templates.
 */
require_once _MY_MODULE_PATH . 'app/Model/Cart.php';
require_once _MY_MODULE_PATH . 'app/Model/Checkout.php';
require_once _MY_MODULE_PATH . 'app/Model/PageNavi.class.php';
require_once _MY_MODULE_PATH . 'app/View/view.php';

class Controller_Checkout extends AbstractAction {
	protected $myObject;
	protected $cartHandler;
	protected $cardList;

	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Checkout::forge();
		$this->cartHandler = Model_Cart::forge();
	}
	public function action_view(){
		$view = new View($this->root);
		if(isset($this->myObject)){
			$view->set('CurrentOrder', $this->myObject);
		}
		$view->set('CardList', $this->cardList);
		$view->set('ListData', $this->mListData);
		$view->set('shipping_fee', $this->cartHandler->isShippingFee() );
		$view->set('total_amount', $this->cartHandler->isTotalAmount() );
		$view->set('ticket_hidden',$this->mTicketHidden);
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
		$view->setTemplate($this->template);
	}
	public function action_index(){
		$this->mListData = $this->cartHandler->getCartList();
		$this->myObject = $this->mHandler->getCurrentOrder();
		$creditService = $this->root->mServiceManager->getService('gmoPayment');
		if ($creditService != null) {
			$client = $this->root->mServiceManager->createClient($creditService);
			$this->cardList = $client->call('getRegisteredCardList',null);
		}
		$this->template = 'checkout.html';
	}
	public function action_editAddress(){
		$this->myObject = $this->mHandler->getCurrentOrder();
		$this->template = 'editAddress.html';
	}
	public function action_addNewAddress(){
		$this->mHandler->addNewAddress();
		$this->action_editAddress();
	}
	public function action_updateAddress(){
		$this->mHandler->update();
		$this->action_index();
	}
	public function action_selectPayment(){
		$this->myObject = $this->mHandler->getCurrentOrder();
		$this->template = 'selectPayment.html';
	}
	private function _payByCreditCard ($cardOrderId,$amount,$tax,$cardSeq){
		$ret = false;
		$params = array('order_id' => $cardOrderId,'cardSeq'=>$cardSeq,'amount'=>$amount,'tax'=>$tax);
		$creditService = $this->root->mServiceManager->getService('gmoPayment');
		if ($creditService != null) {
			$client = $this->root->mServiceManager->createClient($creditService);
			$ret = $client->call('entryTransit',$params);
		}
		return $ret;
	}
	public function action_orderFixed(){
		$order_id = intval(xoops_getrequest("order_id"));
		$cardOrderId = null;
		$payBy = intval(xoops_getrequest("payBy"));
		$cardSeq = intval(xoops_getrequest("CardSeq"));
		$amount  = intval(xoops_getrequest("amount"));
		$ret = false;
		// TODO : Tax Should be set on xoopsConfig
		$tax  = $amount - intval($amount / 1.05);
/*		switch($payBy){
			case 1: // Wire transfer
			case 2: // Pay by Card
				$cardOrderId = sprintf("%s%08d",date("ymd"),$order_id);
				$ret = $this->_payByCreditCard($cardOrderId,$amount,$tax,$cardSeq);
				break;
		}
		if($ret==true){*/
			$this->mListData = $this->cartHandler->getCartList();
			$this->mHandler->moveCartToOrder($this->mListData,$order_id);
			$this->cartHandler->clearMyCart();
			$this->mHandler->setOrderStatus($payBy,$cardOrderId);
			$this->executeRedirect(XOOPS_URL, 3, 'Done');
		//}
	}
}