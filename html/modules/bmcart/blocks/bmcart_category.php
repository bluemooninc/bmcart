<?php
/*
 * B.M.Cart - Cart Module on XOOPS Cube v2.2
 * Copyright (c) Bluemoon inc. All rights reserved.
 * Author : Yoshi Sakai (http://bluemooninc.jp)
 * Licence : GPL V3 licence
 */

function b_bmcart_category_show()
{
	$handler = xoops_getmodulehandler("category","bmcart");
	$objects = $handler->getObjects();
	$mListData = null;
	foreach($objects as $object){
		if ($object->getVar("parent_id")==0){
			$mListData[$object->getVar("category_id")][0] = array(
				"parent_id" => $object->getVar("parent_id"),
				"category_id" => $object->getVar("category_id"),
				"category_name" => $object->getVar("category_name")
			);
		}else{
			$mListData[$object->getVar("category_id")][0]['hasChild']=true;
			$mListData[$object->getVar("parent_id")][$object->getVar("category_id")] = array(
				"parent_id" => $object->getVar("parent_id"),
				"category_id" => $object->getVar("category_id"),
				"category_name" => $object->getVar("category_name")
			);
		}
	}
	$block = array();
	$block['categoryList'] = $mListData;
    return $block;
}
