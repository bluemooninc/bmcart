<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

$adminmenu[]=array(
	'title' => _MI_BMCART_CATEGORY_LIST,
	'link' => "admin/index.php?action=categoryList",
	'keywords' => _MI_BMCART_KEYWORD_CATEGORY_LIST,
	'show' => true
);

$adminmenu[]=array(
	'title' => _MI_BMCART_ITEM_LIST,
	'link' => "admin/index.php?action=itemList",
	'keywords' => _MI_BMCART_KEYWORD_ITEM_LIST,
	'show' => true
);

$adminmenu[]=array(
	'title' => _MI_BMCART_ORDER_LIST,
	'keywords' => _MI_BMCART_KEYWORD_ORDER_LIST,
	'link' => "admin/index.php?action=orderList",
	'show' => true
);

$adminmenu[]=array(
	'title' => _MI_BMCART_SALES_LIST,
	'keywords' => _MI_BMCART_KEYWORD_SALES_LIST,
	'link' => "admin/index.php?action=orderList",
	'show' => true
);
