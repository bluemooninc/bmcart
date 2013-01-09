<?php
/**
 * @package bmcart
 * @version $Id: OrderEditAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/bmcart/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/bmcart/admin/forms/OrderAdminEditForm.class.php";

class bmcart_OrderEditAction extends bmcart_AbstractEditAction
{
	function _getId()
	{
		return xoops_getrequest('order_id');
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('order');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new bmcart_OrderAdminEditForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$render)
	{
		$render->setTemplateName("order_edit.html");
		$render->setAttribute("actionForm", $this->mActionForm);
	}

	function executeViewSuccess(&$controller, &$render)
	{
		$controller->executeForward("index.php?action=OrderList");
	}

	function executeViewError(&$controller, &$render)
	{
		$controller->executeRedirect("index.php?action=OrderList", 5, _MD_BMCART_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$render)
	{
		$controller->executeForward("index.php?action=OrderList");
	}
}
