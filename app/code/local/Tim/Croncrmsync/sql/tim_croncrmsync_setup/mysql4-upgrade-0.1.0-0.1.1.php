<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->run("
CREATE TABLE IF NOT EXISTS `tim_fv` (
  `fv_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `fv_number` varchar (128) NULL,
  `customer_id` int (20) NOT NULL,
  `order_number` varchar(128) NOT NULL,
  `paid` int(11) NOT NULL,
  `nett_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gross_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_date` TIMESTAMP NULL,
  `date_of_issue` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `link_fv` text,
  PRIMARY KEY (`fv_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");