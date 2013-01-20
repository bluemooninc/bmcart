<?php
/*
 * B.M.Cart - Cart Module on XOOPS Cube v2.2
 * Copyright (c) Bluemoon inc. All rights reserved.
 * Author : Yoshi Sakai (http://bluemooninc.jp)
 * Licence : GPL V3 licence
 */

function b_bmcart_newitem_show()
{
	$handler = xoops_getmodulehandler("item","bmcart");
	$imageHandler = xoops_getmodulehandler("itemImages","bmcart");
	$criteria = new CriteriaCompo();
	$criteria->addsort('last_update', 'desc');
	$objects = $handler->getObjects($criteria,0,10);
	$mListData = array();
	foreach( $objects as $object ){
		$imageCriteria = new Criteria('item_id',$object->getVar('item_id'));
		$imageObjects = $imageHandler->getObjects($imageCriteria);
		$images = array();
		foreach($imageObjects as $imageObject){
			$images[] = $imageObject->getVar("image_filename");
		}
		$myRow = array(
			"item_id" => $object->getVar("item_id"),
			"item_name" => $object->getVar("item_name"),
			"images" => $images
		);
		$mListData[] = $myRow;
	}
	$block = array();
	$block['newitemList'] = $mListData;
    return $block;
}
