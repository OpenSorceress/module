<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->addAttribute('customer', 'show_financing_payment', array(
    'label'        => 'Enable Financing payment',
    'visible'      => 1,
    'required'     => 0,
    'position'     => 10,
));

$installer->endSetup();