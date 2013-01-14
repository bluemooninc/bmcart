<?php
/*
 * B.M.Cart - Cart Module on XOOPS Cube v2.2
 * Copyright (c) Bluemoon inc. All rights reserved.
 * Author : Yoshi Sakai (http://bluemooninc.jp)
 * Licence : GPL V3 licence
 */
if (!defined('XOOPS_ROOT_PATH')) exit();
if (!isset($root)) {
	$root = XCube_Root::getSingleton();
}
//$mydirpath = basename( dirname( dirname( __FILE__ ) ) ) ;
$modversion["name"] = _MI_BMCART_TITLE;
$modversion["dirname"] = basename(dirname(__FILE__));
$modversion['hasMain'] = 1;
$modversion['version'] = 0.1;
$modversion['image'] = 'images/bmcart.png';
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";
$modversion['sub'][] = array('name' => _MI_BMCART_CATEGORY_LIST, 'url' => 'bmcart');
$modversion['sub'][] = array('name' => _MI_BMCART_ITEM_LIST, 'url' => 'itemList');
$modversion['sub'][] = array('name' => _MI_BMCART_CART_LIST, 'url' => 'cartList');
$modversion['sub'][] = array('name' => _MI_BMCART_ORDER_LIST, 'url' => 'orderList');

/*
 * View
 */
// for shopping
$modversion['templates'][] = array('file' => "categoryList.html");
$modversion['templates'][] = array('file' => "itemList.html");
$modversion['templates'][] = array('file' => "itemDetail.html");
$modversion['templates'][] = array('file' => "cartList.html");
$modversion['templates'][] = array('file' => "checkout.html");
$modversion['templates'][] = array('file' => "editAddress.html");
$modversion['templates'][] = array('file' => "orderList.html");
$modversion['templates'][] = array('file' => "orderItems.html");

/*
 * Model
 */
$modversion['cube_style'] = true;
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][] = '{prefix}_{dirname}_category';
$modversion['tables'][] = '{prefix}_{dirname}_item';
$modversion['tables'][] = '{prefix}_{dirname}_cart';
$modversion['tables'][] = '{prefix}_{dirname}_order';
$modversion['tables'][] = '{prefix}_{dirname}_orderItems';
/*
 * Config
 */

/*
 * Block
 */
$modversion['blocks'][1] = array(
	'file' => "bmcart_category.php",
	'name' => _MI_BMCART_BLOCK_CATEGORY,
	'description' => _MI_BMCART_BLOCK_CATEGORY_DESC,
	'show_func' => "b_bmcart_category_show",
	'template' => 'bmcart_block_category.html',
	'visible_any' => true,
	'show_all_module' => false
);
