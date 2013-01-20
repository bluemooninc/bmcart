<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/12/28
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */
require_once _MY_MODULE_PATH . 'app/Model/Item.php';
require_once _MY_MODULE_PATH . 'app/Model/PageNavi.class.php';
require_once _MY_MODULE_PATH . 'app/View/view.php';

class Controller_ItemList extends AbstractAction {
	protected $imageObjects;
	protected $image_id;
	/**
	 * constructor
	 */
	public function __construct(){
		parent::__construct();
		$this->mHandler = Model_Item::forge();
	}
	public function action_index(){
		$category_id = $_SESSION['bmcart']['category_id'] ? $_SESSION['bmcart']['category_id'] : NULL;
		$this->mListData = $this->mHandler->getItems($category_id);
		$this->template = 'itemList.html';
	}
	public function action_itemDetail(){
		if (isset($this->mParams[0])) $item_id = intval($this->mParams[0]);
		if (isset($this->mParams[1])) $this->image_id = intval($this->mParams[1]);
		$this->mListData = $this->mHandler->getItemDetail($item_id);
		$_SESSION['bmcart']['category_id'] = $this->mListData['category_id'];
		$imageHandler = xoops_getmodulehandler('itemImages');
		$this->imageObjects = $imageHandler->getImages($item_id);
		if (!$this->image_id && $this->imageObjects){
			$this->image_id = $this->imageObjects[0]->getVar('image_id');
		}
		$this->template = 'itemDetail.html';
	}
	public function action_addtocart(){
		if (isset($this->mParams[0])) $item_id = intval($this->mParams[0]);
		$this->mListData = $this->mHandler->getItemDetail($item_id);
		$cartHandler = xoops_getModuleHandler('cart');
		$cartHandler->addToCart($item_id);
		$this->executeRedirect("../../cartList", 0, 'Add to Cart');
	}
	public function action_category(){
		if (isset($this->mParams[0])) $category_id = intval($this->mParams[0]);
		$this->mListData = $this->mHandler->getItems($category_id);
		$_SESSION['bmcart']['category_id'] = $category_id;
		$this->template = 'itemList.html';
	}
	public function action_view(){
		$view = new View($this->root);
		$view->setTemplate($this->template);
		$view->set('ListData', $this->mListData);
		$view->set('imageObjects', $this->imageObjects);
		$view->set('current_image', $this->image_id);
		$view->set('ticket_hidden',$this->mTicketHidden);
		if (is_object($this->mPagenavi)) {
			$view->set('pageNavi', $this->mPagenavi->getNavi());
		}
	}

}
