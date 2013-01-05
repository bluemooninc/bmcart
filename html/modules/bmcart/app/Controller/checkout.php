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
	protected $myObjects;
	protected $cartHandler;
	protected $cardList;

	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Checkout::forge();
		$this->cartHandler = Model_Cart::forge();
	}
	public function action_view(){
		$view = new View($this->root);
		if(isset($this->myObjects)){
			$view->set('CurrentOrder', $this->myObjects[0]);
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
		$this->myObjects = $this->mHandler->getCurrentOrder();
		$creditService = $this->root->mServiceManager->getService('gmoPayment');
		if ($creditService != null) {
			$client = $this->root->mServiceManager->createClient($creditService);
			$this->cardList = $client->call('getRegisteredCardList',null);
		}
		$this->template = 'checkout.html';
	}
	public function action_addNewAddress(){
		$this->template = 'editAddress.html';
	}
	public function action_editAddress(){
		$this->myObjects = $this->mHandler->getCurrentOrder();
		$this->template = 'editAddress.html';
	}
	public function action_updateAddress(){
		$this->mHandler->update();
		$this->action_index();
	}
	public function action_selectPayment(){
		$this->myObjects = $this->mHandler->getCurrentOrder();
		$this->template = 'selectPayment.html';
	}
	public function action_orderFixed(){
		$order_id = intval(xoops_getrequest("order_id"));
		$cardSeq = intval(xoops_getrequest("CardSeq"));
		$amount  = intval(xoops_getrequest("amount"));

 	    // TODO : Should be set on xoopsConfig
		$tax  = $amount - intval($amount / 1.05);
		$params = array('order_id' => $order_id,'cardSeq'=>$cardSeq,'amount'=>$amount,'tax'=>$tax);
		$creditService = $this->root->mServiceManager->getService('gmoPayment');
		if ($creditService != null) {
			$client = $this->root->mServiceManager->createClient($creditService);
			$ret = $client->call('entryTransit',$params);
			if($ret==true){
				$this->executeRedirect(XOOPS_URL, 3, 'Done');
			}
		}

	}
}