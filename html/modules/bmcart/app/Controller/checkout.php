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
	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Checkout::forge();
		$this->cartHandler = Model_Cart::forge();
	}
	public function action_view(){
		$view = new View($this->root);
		$view->setTemplate($this->template);
		$view->set('ListData', $this->mListData);
		$view->set('shipping_fee', $this->cartHandler->isShippingFee() );
		$view->set('total_amount', $this->cartHandler->isTotalAmount() );
		$view->set('ticket_hidden',$this->mTicketHidden);
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
	}
	public function action_index(){
		$this->mListData = $this->cartHandler->getCartList();
		$this->template = 'checkout.html';
	}
	public function action_addNewAddress(){
		$this->template = 'editAddress.html';
	}
	public function action_editAddress(){
		$this->mHandler->update();    die;
		$this->action_index();
	}
}