<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 22:24
 * To change this template use File | Settings | File Templates.
 */
require_once _MY_MODULE_PATH . 'app/Model/Item.php';
require_once _MY_MODULE_PATH . 'app/Model/Cart.php';
require_once _MY_MODULE_PATH . 'app/Model/Checkout.php';
require_once _MY_MODULE_PATH . 'app/Model/MailBuilder.php';
require_once _MY_MODULE_PATH . 'app/Model/PageNavi.class.php';
require_once _MY_MODULE_PATH . 'app/View/view.php';

class Controller_Checkout extends AbstractAction {
	protected $myObject;
	protected $cartHandler;
	protected $cardList;
	protected $message;
	protected $stateOptions;
	protected $cashOnDeliveryFee;
	protected $myConfig;
	protected $rate;

	public function __construct(){
		parent::__construct();
		$this->mModel = Model_Checkout::forge();
		$this->cartHandler = Model_Cart::forge();
		$this->myConfig = $this->root->mContext->mModuleConfig;
	}
	private function _getCashOnDeliveryFee($total){
		$cods = explode(",",$this->root->mContext->mModuleConfig['cash_on_delivery']);
		foreach($cods as $cod){
			$keyVal = explode(">",$cod);
			if ($total<$keyVal[0]) return $keyVal[1];
		}
		return NULL;
	}

	/**
	 * PayPal On/Off
	 *
	 * @return bool
	 */
	private function fromPayPal(){
		$filename = XOOPS_MODULE_PATH . '/bmpaypal/xoops_version.php';
		if (file_exists($filename)) {
			return TRUE;
		}
		return FALSE;
	}

	public function action_index(){
		if(!$this->root->mContext->mXoopsUser){
			redirect_header(XOOPS_URL."/user.php",3,_MD_BMCART_NEED_LOGIN);
		}
		$this->mListData = $this->cartHandler->getCartList();
		$this->cashOnDeliveryFee = $this->_getCashOnDeliveryFee($this->cartHandler->isTotalAmount());
		$itemHandler = Model_Item::forge();
		if (!$itemHandler->checkStock($this->mListData)){
			$this->message = $itemHandler->getMessage() . _MD_BMCART_NO_STOCK;
		}
		$this->myObject = $this->mModel->getCurrentOrder();
		$creditService = $this->root->mServiceManager->getService('gmoPayment');
		if ($creditService != NULL) {
			$client = $this->root->mServiceManager->createClient($creditService);
			$this->cardList = $client->call('getRegisteredCardList',NULL);
		}
		$this->template = 'checkout.html';
	}
	public function action_editAddress(){
		$this->myObject = $this->mModel->getCurrentOrder();
		$this->template = 'editAddress.html';
		$this->stateOptions = $this->mModel->getStateOptions();
	}
	public function action_addNewAddress(){
		$this->mModel->addNewAddress();
		$this->action_editAddress();
	}
	public function action_updateAddress(){
		$this->mModel->update();
		$this->action_index();
	}
	public function action_selectPayment(){
		$this->myObject = $this->mModel->getCurrentOrder();
		$this->template = 'selectPayment.html';
	}

	/**
	 * Credit Card API
	 *
	 * TODO: Paypal and other transit in the future
	 * @param $cardOrderId
	 * @param $amount
	 * @param $tax
	 * @param $cardSeq
	 * @return bool
	 */
	private function _payByCreditCard ($cardOrderId,$amount,$tax,$cardSeq){
		$ret = FALSE;
		$params = array('order_id' => $cardOrderId,'cardSeq'=>$cardSeq,'amount'=>$amount,'tax'=>$tax);
		$creditService = $this->root->mServiceManager->getService('gmoPayment');
		if ($creditService != NULL) {
			$client = $this->root->mServiceManager->createClient($creditService);
			$ret = $client->call('entryTransit',$params);
		}
		return $ret;
	}
	private function _order_notifications($order_id){
		$tags = array();
		$tags['ORDER_URL']  = XOOPS_URL.'/modules/bmcart/admin/index.php?action=orderList&order_id=' . $order_id;
		$notification_handler =& xoops_gethandler('notification');
		$notification_handler->triggerEvent('global', 0, 'order_submit', $tags );
	}
	private function getRatefromGoogle($to,$from){
		$exchangeEndpoint = sprintf("http://rate-exchange.appspot.com/currency?from=%s&to=%s",$from,$to);
		$json = file_get_contents($exchangeEndpoint);
		$data = json_decode($json, TRUE);
		if($data){
			return $data['rate'];
		}
	}
	private function exchangeToUSD($amount,$currency="USD"){
		if ($currency!="USD"){
			$this->rate = $this->getRatefromGoogle($currency,"USD");
			$amount_usd = round($amount / $this->rate, 2);
		}else{
			$amount_usd = $amount;
		}
		return $amount_usd;
	}
	/**
	 * Check out method
	 */
	public function action_orderFixed(){
		global $xoopsModuleConfig;
		$this->myObject = $this->mModel->getCurrentOrder();
		if (!$this->mModel->checkCurrentOrder($this->myObject)){
			redirect_header( "index", 3, $this->mModel->getMessage());
		}
		$order_id = intval(xoops_getrequest("order_id"));
		$cardOrderId = NULL;
		$payment_type = intval(xoops_getrequest("payment_type"));
		$cardSeq = intval(xoops_getrequest("CardSeq"));
		$this->mListData = $this->cartHandler->getCartList();
		$shipping_fee = $this->cartHandler->isShippingFee();
		$amount = $this->cartHandler->isTotalAmount();
		$sub_total = $this->cartHandler->isSubTotal();
		$tax  = intval($sub_total * ($xoopsModuleConfig['sales_tax']/100));

		$ret = FALSE;
		$itemHandler = Model_Item::forge();
		if (!$itemHandler->checkStock($this->mListData)){
			redirect_header( XOOPS_URL."/modules/bmcart/cartList", 3, $this->mModel->getMessage()._MD_BMCART_NO_STOCK);
		}
		switch($payment_type){
			case 1: // Wire transfer
                $ret = TRUE;
				break;
			case 2: // Pay by Card
				$cardOrderId = $order_id;
				$ret = $this->_payByCreditCard($cardOrderId,$amount,$tax,$cardSeq);
				break;
			case 3: // Cash on Delivery
				$codFee = $this->_getCashOnDeliveryFee($sub_total);
				$shipping_fee += $codFee;
				$amount += $codFee;
				$ret = TRUE;
				break;
			case 4: // From PayPal
				$ret = TRUE;
				break;
		}
		if( $ret==TRUE ){
			$this->mModel->moveCartToOrder($this->mListData,$order_id);
			$this->cartHandler->clearMyCart();
			if($payment_type==4){
				// Move to PayPal module currently USD only on PayPal REST api
				$title = 'To Paypal';
				$sec = 1;
				$currency = $this->root->mContext->mModuleConfig['currency'];
				$amount_usd = $this->exchangeToUSD($amount,$currency);
				$rate = $this->rate;
				$param = "/modules/bmpaypal/bmpaypal/index?" . sprintf("order_id=%d&amount=%f&currency=%s",$order_id,$amount_usd,"USD");
			}else{
				$title = 'Done';
				$sec = 5;
				$rate = $amount_usd = 0;
				$param = "/modules/bmcart/orderList/index";
			}
			$mail = new Model_Mail();
			$orderObject = $this->mModel->setOrderStatus($order_id,$payment_type,$cardOrderId,$sub_total,$tax,$shipping_fee,$amount,$rate,$amount_usd);
			$mail->sendMail("ThankYouForOrder.tpl",$orderObject,$this->mListData,_MD_BMCART_ORDER_MAIL);
			$this->_order_notifications($order_id);
			$this->executeRedirect(XOOPS_URL.$param, $sec, $title);
		}
	}
	public function action_view(){
		// Get data
		$shipping_fee =$this->cartHandler->isShippingFee();
		$amount = $this->cartHandler->isTotalAmount();
		$currency = $this->myConfig['currency'];
		$amount_usd = $this->exchangeToUSD($amount,$currency);
		// set to template
		$view = new View($this->root);
		if(isset($this->myObject)){
			$view->set('CurrentOrder', $this->myObject);
		}
		$view->set('stateOptions',$this->stateOptions);
		$view->set('Message', $this->message);
		$view->set('CardList', $this->cardList);
		$view->set('ListData', $this->mListData);
		$view->set('shipping_fee', $shipping_fee);
		$view->set('total_amount', $amount );
		$view->set('cashOnDeliveryFee', $this->cashOnDeliveryFee);
		$view->set('fromPayPal', $this->fromPayPal());
		$view->set('ticket_hidden',$this->mTicketHidden);
		$view->set('currency',$currency);
		$view->set('amount_usd',$amount_usd);
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
		$view->setTemplate($this->template);
	}

}