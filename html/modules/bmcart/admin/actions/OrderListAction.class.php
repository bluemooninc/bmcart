<?php
/**
 * Created by JetBrains PhpStorm.
 * Item: bluemooninc
 * Date: 2012/12/08
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/bmcart/class/AbstractListAction.class.php";

class bmcart_StockListAction extends bmcart_AbstractListAction
{
	var $mItemObjects = array();
	var $mActionForm = NULL;

	function executeViewIndex(&$controller, &$xoopsItem, &$render)
	{
		$render->setTemplateName("item_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('filterForm', $this->mFilter);
		$render->setAttribute('pageArr', $this->mpageArr);
	}

	function execute(&$controller, &$xoopsItem)
	{
		$form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return USER_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return $this->_processConfirm($controller, $xoopsItem);
		}
		else {
			return $this->_processSave($controller, $xoopsItem);
		}
	}

	function executeViewSuccess(&$controller, &$renderer)
	{
		$controller->executeForward('./index.php?action=ItemList');
	}

	function executeViewError(&$controller, &$renderer)
	{
		$controller->executeRedirect('./index.php?action=ItemList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$renderer)
	{
		$controller->executeForward('./index.php?action=ItemList');
	}


}