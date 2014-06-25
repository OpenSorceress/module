<?php

class PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Report_Customer_Financingwidget_Renderer_Financingwidgetdoc
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        return '<a target="_blank" href="'.
            Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getData('customer_id')))
        .'">'.
            $value
        .'</a>';
    }
}
