<?
include '../includes/config.php';

/**
 *
 * 
 * DATABASE UPDATE + CONSISTENCY
 *
 *
 *

***************************************************************************************************************************
*
* CHANGE [scroll_navigation_items] TABLE 
*
**
ALTER TABLE `scroll_navigation_items` ADD `image` TEXT NOT NULL ;

***************************************************************************************************************************
*
* CHANGE [ecommerce_product_attributes] TABLE 
*
**
ALTER TABLE `ecommerce_product_attributes` ADD `hidden` TINYINT( 1 ) NOT NULL DEFAULT '0';
 
***************************************************************************************************************************
*
* CHANGE [ecommerce_product_attributes] TABLE
*
**
ALTER TABLE `ecommerce_product_attributes` CHANGE `type` `type` ENUM( 'Textbox', 'Textarea', 'Select', 'Checkbox', 'Checkbox Group', 'Radio Group' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Textbox'  
 
***************************************************************************************************************************
*
* ADD COLUMN 	[color] to [ecommerce_product_colors] FOR ECOMMERCE
*
**
ALTER TABLE `ecommerce_product_colors` ADD `color` TEXT NOT NULL ; 
 
***************************************************************************************************************************
*
* ADD ROW 	`Ecommerce Droppable` to [settings] FOR ECOMMERCE
*
**
INSERT INTO `wes-shebeads`.`settings` (
`id` ,
`value` ,
`name` ,
`description` ,
`session_id` ,
`type` ,
`table` ,
`group_id` ,
`user_setting` ,
`active` ,
`user_friendly_name`
)
VALUES (
NULL , '1', 'Ecommerce Droppable', NULL , '', 'Boolean', '', '4', '0', '1', 'Droppable Shopping Cart'
);

***************************************************************************************************************************
*
* ADD COLUMN 	[draggable] to [eccomerce_products] FOR ECOMMERCE
*
**
ALTER TABLE `ecommerce_products` ADD `draggable` INT( 1 ) NOT NULL DEFAULT '0'; 
 
***************************************************************************************************************************
*
* ADD COLUMN 	[zoom] to [eccomerce_products] FOR ECOMMERCE
*
**
ALTER TABLE `ecommerce_products` ADD `zoom` INT( 1 ) NOT NULL DEFAULT '1';

***************************************************************************************************************************
*
* ADD COLUMN 	[hidden] to [eccomerce_products] FOR ECOMMERCE
*
**
ALTER TABLE `ecommerce_products` ADD `hidden` INT( 1 ) NOT NULL DEFAULT '0';

***************************************************************************************************************************
*
* ADD COLUMN 	[type] to [eccomerce_product_attributes] FOR ECOMMERCE
*
**
ALTER TABLE `ecommerce_product_attributes` ADD `type` ENUM( 'drop', 'check', 'radio' ) NOT NULL DEFAULT 'drop';
 
***************************************************************************************************************************
*
* ADD TABLE 	[eccomerce_product_attribute_values] FOR ECOMMERCE
*
**
CREATE TABLE IF NOT EXISTS `ecommerce_product_attribute_values` (
  `attribute_value_id` int(11) NOT NULL auto_increment,
  `attribute_id` int(11) NOT NULL,
  `sort_level` int(11) NOT NULL default '0',
  `name` text collate utf8_unicode_ci,
  `value` text collate utf8_unicode_ci,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL default '1',
  `session_id` text collate utf8_unicode_ci,
  PRIMARY KEY  (`attribute_value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1  

***************************************************************************************************************************
*
* ADD TABLE 	[eccomerce_product_attributes] FOR ECOMMERCE
*
**
CREATE TABLE IF NOT EXISTS `ecommerce_product_attributes` (
  `attribute_id` int(11) NOT NULL auto_increment,
  `sort_level` int(11) NOT NULL default '0',
  `name` text collate utf8_unicode_ci,
  `description` text collate utf8_unicode_ci,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL default '1',
  `session_id` text collate utf8_unicode_ci,
  PRIMARY KEY  (`attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

***************************************************************************************************************************
*
* ADD TABLE 	[portfolio_item_images] FOR PORTFOLIO
*
**
CREATE TABLE IF NOT EXISTS `portfolio_item_images` (
  `image_id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `name` text,
  `description` text,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `status` enum('Published','Pending','Draft') NOT NULL default 'Draft',
  `active` int(1) NOT NULL default '1',
  `date` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 

***************************************************************************************************************************
*
* INSERT ROW	ID 22 Account File Manager Into [wes_modules] 
*
**

***************************************************************************************************************************
*
* INSERT ROW	ID 21 Scroll Navigation Into [wes_modules] 
*
**
// 
//
// 
// ADD TABLE	[ecommerce_product_color_relational]
// ADD TABLE	[ecommerce_product_colors]
// ADD COLUMN 	[subtitle] to [pages]
// ADD COLUMN 	[image] to [pages]
// ADD COLUMN 	[location_id] to [locations]
// ADD COLUMN	[hidden] to [pages]
// ADD TABLE 	[ecommerce_thumbnail_sizes] FOR ECOMMERCE



*/ 













function addTable($table,$columns){
	// check for table
	$select = "CREATE TABLE IF NOT EXISTS `".$table."`";
	return true;
}