##
## Group for item
##
CREATE TABLE {prefix}_{dirname}_category (
  `category_id` int(8) unsigned NOT NULL auto_increment,
  `category_name` varchar(255) NOT NULL,
  `parent_id` int(8) unsigned NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE = MYISAM;
##
## Stock
##
CREATE TABLE {prefix}_{dirname}_item (
  `item_id` int(8) unsigned NOT NULL auto_increment,
  `category_id` int(8) unsigned NOT NULL,
  `uid` int(8) unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_desc` text,
  `price` decimal(13,2),
  `shipping_fee` decimal(13,2),
  `stock_qty` int(1) unsigned NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  `publish_date` int(10) unsigned NOT NULL,
  `expire_date` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`item_id`),
  KEY category_id (`category_id`),
  KEY uid (`uid`)
) ENGINE = MYISAM;
##
## Cart
## item_status :
##
CREATE TABLE {prefix}_{dirname}_cart (
  `cart_id` int(8) unsigned NOT NULL auto_increment,
  `item_id` int(8) unsigned NOT NULL,
  `uid` int(8) unsigned NOT NULL,
  `qty` int(1) unsigned NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cart_id`),
  KEY uid (`uid`)
) ENGINE = MYISAM;

##
## ORDER
## order_date = 注文日 / paid_date=入金日 / shipping_date=発送日
## status: 0=yet fixed, 1=fixed(and waiting by wire), 2=entry transit(by card)
## 3=paid 4=shipped
##
CREATE TABLE {prefix}_{dirname}_order (
  `order_id` int(8) unsigned NOT NULL auto_increment,
  `uid` int(8) unsigned NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `state` varchar(32) NOT NULL,
  `address` varchar(80) NOT NULL,
  `address2` varchar(80) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `price` decimal(13,2),
  `shipping_fee` decimal(13,2),
  `paid_date` int(10) unsigned NOT NULL,
  `shipping_date` int(10) unsigned NOT NULL,
  `order_date` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`order_id`),
  KEY uid (`uid`)
) ENGINE = MYISAM;
##
## Order Items
##
CREATE TABLE {prefix}_{dirname}_order_items (
  `orderItem_id` int(8) unsigned NOT NULL auto_increment,
  `order_id` int(8) unsigned NOT NULL,
  `uid` int(8) unsigned NOT NULL,
  `item_id` int(8) unsigned NOT NULL,
  `price` decimal(13,2),
  `shipping_fee` decimal(13,2),
  `qty` int(1) unsigned NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`orderItem_id`),
  KEY uid (`uid`)
) ENGINE = MYISAM;
