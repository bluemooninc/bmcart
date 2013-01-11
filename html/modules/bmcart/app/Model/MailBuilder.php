<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class Model_Mail
{
	protected $mXoopsUser;
	protected $mXoopsConfig;
	protected $mMailer;
	protected $root;

	public function __construct()
	{
		$this->root = XCube_Root::getSingleton();
		$this->mXoopsConfig = $this->root->mContext->mXoopsConfig;
		$this->mXoopsUser = $this->root->mContext->mXoopsUser;
		$this->mMailer =& getMailer();
		$this->mMailer->useMail();
		$language = $this->root->mContext->getXoopsConfig('language');
		$this->mMailer->setTemplateDir(
			XOOPS_ROOT_PATH . '/modules/bmcart/language/' . $language . '/mail_template/'
		);
	}

	private function _setBody($orderObject,$listData)
	{
		$this->mMailer->assign("SITENAME", $this->mXoopsConfig['sitename']);
		$this->mMailer->assign("ADMINMAIL", $this->mXoopsConfig['adminmail']);
		foreach($orderObject->mVars as $key => $val){
			$this->mMailer->assign(strtoupper($key), $val['value']);
		}
		$desc = "";
		foreach($listData as $item){
			$desc .= sprintf("%s %0,3d %d¥r¥n",$item['item_name'],$item['price'],$item['qty']);
		}
		$this->mMailer->assign("LIST_DATA", $desc);
		$this->mMailer->assign("SITEURL", XOOPS_URL . "/");
		$this->mMailer->assign("URL_BILL", XOOPS_URL . "/modules/bmcart/orderList/" . $orderObject->getVar('order_id'));
		switch ($orderObject->getVar('payment_type')){
			case 1: // Wire
				$this->mMailer->assign("PAYMENT_DESC", _MD_BMCART_PAYMENT_DESC_WIRE);
				break;
			case 2: // Credi Card
				$this->mMailer->assign("PAYMENT_DESC", _MD_BMCART_PAYMENT_DESC_CARD);
				break;
		}
		$this->mMailer->assign('SHIPPING_DATE',$orderObject->getVar('shipping_date'));
		$this->mMailer->assign('SHIPPING_CARRIER',$orderObject->getVar('shipping_carrier'));
		$this->mMailer->assign('SHIPPING_NUMBER',$orderObject->getVar('shipping_number'));
	}

	/**
	 * @param $tpl_name
	 * @param $paymentInfo
	 * @param $listData
	 */
	public function sendMail($tpl_name,$orderObject,$listData){
		$this->mMailer->setFromEmail($this->mXoopsConfig['adminmail']);
		$this->mMailer->setFromName($this->mXoopsConfig['sitename']);
		$this->mMailer->setToUsers($this->mXoopsUser);
		$this->mMailer->setSubject(_MD_BMCART_ORDER_MAIL);
		$this->mMailer->setTemplate($tpl_name);
		$this->_setBody($orderObject,$listData);
		if ($this->mMailer->send()) {
			echo $this->mMailer->getSuccess();
		} else {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $this->mMailer->getErrors();
			exit;
		}
	}
}
