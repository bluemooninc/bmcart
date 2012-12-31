<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/31
 * Time: 22:24
 * To change this template use File | Settings | File Templates.
 */
class Controller_Checkout extends AbstractAction {
	protected $myObjects;
	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Cart::forge();
	}
	public function action_index(){
		$this->template = 'checkout.html';
	}
	public function action_view(){
		$view = new View($this->root);
		$view->setTemplate($this->template);
		$view->set('ListData', $this->mListData);
		$view->set('ticket_hidden',$this->mTicketHidden);
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
	}

}