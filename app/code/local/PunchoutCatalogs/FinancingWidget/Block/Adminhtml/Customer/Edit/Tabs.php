<?php

class PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml(){
        if(!strpos($_SERVER['REQUEST_URI'], 'customer/new')){
            Mage::dispatchEvent('adminhtml_customer_edit_tabs_beforetohtml', array(
                'object' => $this,
                'url' => $this->getUrl('*/*/placeholder', array('_current' => true))
            ));
        }
        return parent::_beforeToHtml();
    }
}
