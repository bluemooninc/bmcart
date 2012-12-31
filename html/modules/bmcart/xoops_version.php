<?php
/*
* GMO-PG - Payment Module as XOOPS Cube Module
* Copyright (c) Yoshi Sakai at Bluemoon inc. (http://bluemooninc.jp)
* GPL V2 licence
 */
if (!defined('XOOPS_ROOT_PATH')) exit();
if ( !isset($root) ) {
	$root = XCube_Root::getSingleton();
}
//$mydirpath = basename( dirname( dirname( __FILE__ ) ) ) ;
$modversion["name"] =  _MI_BMCART_TITLE;
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

/*
 * View
 */
// for shopping
$modversion['templates'][] = array( 'file' => "categoryList.html" );
$modversion['templates'][] = array( 'file' => "itemList.html" );
$modversion['templates'][] = array( 'file' => "itemDetail.html" );
$modversion['templates'][] = array( 'file' => "cartList.html" );
$modversion['templates'][] = array( 'file' => "checkout.html" );

/*
 * Model
 */
$modversion['cube_style'] = true;
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][] = '{prefix}_{dirname}_category';
$modversion['tables'][] = '{prefix}_{dirname}_item';
$modversion['tables'][] = '{prefix}_{dirname}_cart';
$modversion['tables'][] = '{prefix}_{dirname}_order';
/*
 * Config
 */

