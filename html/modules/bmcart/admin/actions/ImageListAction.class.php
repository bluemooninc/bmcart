<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/bmcart/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/bmcart/admin/forms/ImageFilterForm.class.php";

class bmcart_ImageListAction extends bmcart_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('itemImages');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new bmcart_ImageFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=ImageList";
	}

	function executeViewIndex(&$controller, &$render)
	{
		$render->setTemplateName("image_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$categoryHandler = xoops_getmodulehandler('category');
		$render->setAttribute("categoryList", $categoryHandler->getCategoryOptions());
	}
}

