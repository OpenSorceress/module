<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('punchoutcatalogs_financingwidget')};
CREATE TABLE {$this->getTable('punchoutcatalogs_financingwidget')} (
  `financingwidget_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` smallint(6) NOT NULL default '0',

  `customer_email` varchar(255) NOT NULL default '',
  `customer_firstname` varchar(255) NOT NULL default '',
  `customer_lastname` varchar(255) NOT NULL default '',

  `filename` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`financingwidget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
