<?php

class PunchoutCatalogs_FinancingWidget_Model_Resource_Files_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        //path to model
        $this->_init('punchoutcatalogs_financingwidget/files');
    }
}
