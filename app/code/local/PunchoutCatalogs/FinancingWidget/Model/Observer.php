<?php

class PunchoutCatalogs_FinancingWidget_Model_Observer extends Mage_Core_Model_Abstract {
    public function customerTabs($observer){
        $observer->getObject()->addTab('financingwidget', array(
            'label'     => Mage::helper('punchoutcatalogs_financingwidget')->__('Financing'),
            'class'     => 'ajax',
            'url'       => str_replace('placeholder', 'financingwidget', $observer->getUrl()),
        ));
    }
}
