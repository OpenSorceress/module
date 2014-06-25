<?php

class PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Report_Customer_Financingwidget_Renderer_Financingpayment
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $customerId = $row->getData('customer_id');
        $flag = Mage::helper('punchoutcatalogs_financingwidget')->isEnabledFinancingPayment($customerId);
        switch($flag){
            case true:
                $return = Mage::helper('punchoutcatalogs_financingwidget')->__('Yes'); break;
            default:
                $return = Mage::helper('punchoutcatalogs_financingwidget')->__('No');
        }
        return $return;
    }
}
