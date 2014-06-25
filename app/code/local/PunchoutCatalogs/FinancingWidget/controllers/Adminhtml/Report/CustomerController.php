<?php

include_once Mage::getBaseDir().DS.'app'.DS.'code'.DS.'core'.
    DS.'Mage'.DS.'Adminhtml'.DS.'controllers'.DS.'Report'.DS.'CustomerController.php';
class PunchoutCatalogs_FinancingWidget_Adminhtml_Report_CustomerController extends Mage_Adminhtml_Report_CustomerController
{
    public function financingwidgetsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Financing Requests'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/financingwidgets')
            ->_addBreadcrumb(
                Mage::helper('punchoutcatalogs_financingwidget')->__('Financing Requests'),
                Mage::helper('punchoutcatalogs_financingwidget')->__('Financing Requests')
            )
            ->_addContent($this->getLayout()->createBlock('punchoutcatalogs_financingwidget/adminhtml_report_customer_financingwidget'))
            ->renderLayout();
    }

    /**
     * Export new accounts report grid to CSV format
     */
    public function exportFinancingwidgetsCsvAction()
    {
        $fileName   = 'financing_requests.csv';
        $content    = $this->getLayout()->createBlock('punchoutcatalogs_financingwidget/adminhtml_report_customer_financingwidget_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export new accounts report grid to Excel XML format
     */
    public function exportFinancingwidgetsExcelAction()
    {
        $fileName   = 'financing_requests.xml';
        $content    = $this->getLayout()->createBlock('punchoutcatalogs_financingwidget/adminhtml_report_customer_financingwidget_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        if($this->getRequest()->getActionName() == 'financingwidgets'){
            return Mage::getSingleton('admin/session')->isAllowed('report/customers/financingwidgets');
        }else{
            return parent::_isAllowed();
        }
    }
}
