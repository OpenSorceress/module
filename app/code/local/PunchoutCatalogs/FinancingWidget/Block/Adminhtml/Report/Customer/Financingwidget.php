<?php

class PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Report_Customer_Financingwidget extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'punchoutcatalogs_financingwidget';
        $this->_controller = 'adminhtml_report_customer_financingwidget';
        $this->_headerText = Mage::helper('punchoutcatalogs_financingwidget')->__('Financing Requests');
        parent::__construct();
        $this->_removeButton('add');
    }
}
