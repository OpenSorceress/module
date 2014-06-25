<?php

class PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Report_Customer_Financingwidget_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('report/punchoutcatalogs_financingwidget_grid.phtml');
        $this->setId('gridFinancingwidgets');
    }

    protected function _prepareCollection()
    {
        // Retrieve collection
        $collection = Mage::getModel('punchoutcatalogs_financingwidget/files')
            ->getCollection();

        // Work with filters
        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            if (!isset($data['report_from'])) {
                // getting all reports from 2001 year
                $date = new Zend_Date(mktime(0,0,0,1,1,2001));
                $data['report_from'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            if (!isset($data['report_to'])) {
                // getting all reports from 2001 year
                $date = new Zend_Date();
                $data['report_to'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        if($this->getFilter('report_from')){
            $date = date('Y-m-d H:i:s', strtotime($this->getFilter('report_from')));
            $collection->addFieldToFilter('created_time', array('gteq' => $date));
        }
        if($this->getFilter('report_to')){
            $date = date('Y-m-d H:i:s', strtotime($this->getFilter('report_to')));
            $collection->addFieldToFilter('created_time', array('lteq' => $date));
        }

        $this->setCollection($collection);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('punchoutcatalogs_financingwidget')->__('Customer Email'),
            'index'     => 'customer_email'
        ));

        $this->addColumn('customer_firstname', array(
            'header'    => Mage::helper('punchoutcatalogs_financingwidget')->__('First Name'),
            'index'     => 'customer_firstname'
        ));

        $this->addColumn('customer_lastname', array(
            'header'    => Mage::helper('punchoutcatalogs_financingwidget')->__('Last Name'),
            'index'     => 'customer_lastname'
        ));

        $this->addColumn('show_financing_payment', array(
            'header'    => Mage::helper('punchoutcatalogs_financingwidget')->__('Can use Financing Payment now'),
            'renderer'  => new PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Report_Customer_Financingwidget_Renderer_Financingpayment,
            'index'     => 'show_financing_payment'
        ));

        $this->addColumn('filename', array(
            'header'    => Mage::helper('punchoutcatalogs_financingwidget')->__('Document'),
            'renderer'  => new PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Report_Customer_Financingwidget_Renderer_Financingwidgetdoc,
            'index'     => 'filename'
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('punchoutcatalogs_financingwidget')->__('Created At'),
            'index'     => 'created_time'
        ));

        $this->addExportType('*/*/exportFinancingwidgetsCsv', Mage::helper('punchoutcatalogs_financingwidget')->__('CSV'));
        $this->addExportType('*/*/exportFinancingwidgetsExcel', Mage::helper('punchoutcatalogs_financingwidget')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid as CSV
     *
     * @return unknown
     */
    public function getCsv()
    {
        $csv = '';
        $this->_prepareGrid();

        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach($this->getCollection() as $item){
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(
                            array('"', '\\'),
                            array('""', '\\\\'),
                            $column->getRowField($item)
                        ) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";

        }

        if ($this->getCountTotals() && $this->getSubtotalVisibility())
        {
            $data = array();
            $j = 0;
            foreach ($this->_columns as $column) {
                $j++;
                if (!$column->getIsSystem()) {
                    $data[] = ($j == 1) ?
                        '"' . $this->__('Subtotal') . '"' :
                        '"'.str_replace('"', '""', $column->getRowField($this->getTotals())).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array('"'.$this->__('Total').'"');
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"'.str_replace('"', '""', $column->getRowField($this->getGrandTotals())).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

    /**
     * Retrieve grid as Excel Xml
     *
     * @return unknown
     */
    public function getExcel($filename = '')
    {
        $this->_prepareGrid();

        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getHeader();
            }
        }
        $data[] = $row;

        foreach($this->getCollection() as $item){
            $row = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($item);
                }
            }
            $data[] = $row;
        }
        if ($this->getCountTotals() && $this->getSubtotalVisibility())
        {
            $row = array();
            $j = 0;
            foreach ($this->_columns as $column) {
                $j++;
                if (!$column->getIsSystem()) {
                    $row[] = ($j==1)?$this->__('Subtotal'):$column->getRowField($this->getTotals());
                }
            }
            $data[] = $row;
        }

        if ($this->getCountTotals())
        {
            $row = array($this->__('Total'));
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($this->getGrandTotals());
                }
            }
            $data[] = $row;
        }

        $xmlObj = new Varien_Convert_Parser_Xml_Excel();
        $xmlObj->setVar('single_sheet', $filename);
        $xmlObj->setData($data);
        $xmlObj->unparse();

        return $xmlObj->getData();
    }
}
