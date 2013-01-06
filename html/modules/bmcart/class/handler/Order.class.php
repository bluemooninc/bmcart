<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
/*
 * {Dirname}_{Filename} : Naming convention for Model
 */
class bmcart_orderObject extends XoopsSimpleObject
{
    public function __construct()
    {
        $this->initVar('order_id', XOBJ_DTYPE_INT, 0);
        $this->initVar('uid', XOBJ_DTYPE_INT, Legacy_Utils::getUid() , true);
	    $this->initVar('zip_code', XOBJ_DTYPE_STRING, '', true, 10);
	    $this->initVar('state', XOBJ_DTYPE_STRING, '', true, 32);
	    $this->initVar('address', XOBJ_DTYPE_STRING, '', true, 80);
	    $this->initVar('address2', XOBJ_DTYPE_STRING, '', true, 80);
	    $this->initVar('phone', XOBJ_DTYPE_STRING, '', true, 12);
	    $this->initVar('first_name', XOBJ_DTYPE_STRING, '', true, 80);
	    $this->initVar('last_name', XOBJ_DTYPE_STRING, '', true, 80);
	    $this->initVar('price', XOBJ_DTYPE_INT, 0, true);
	    $this->initVar('shipping_fee', XOBJ_DTYPE_INT, 0, true);
	    $this->initVar('payment_type', XOBJ_DTYPE_INT, 0);
	    $this->initVar('card_order_id', XOBJ_DTYPE_STRING, null, true, 10);
	    $this->initVar('order_date', XOBJ_DTYPE_INT, null);
	    $this->initVar('paid_date', XOBJ_DTYPE_INT, null);
	    $this->initVar('shipping_date', XOBJ_DTYPE_INT, null);
    }
}

class bmcart_orderHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bmcart_order';
    public $mPrimary = 'order_id';
    public $mClass = 'bmcart_orderObject';
    public $id;
	private $myHandler;

    public function __construct(&$db)
    {
        parent::XoopsObjectGenericHandler($db);
    }
}
