<?php

class PunchoutCatalogs_FinancingWidget_Model_Resource_Files extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        //path to table in config, table id
        $this->_init('punchoutcatalogs_financingwidget/punchoutcatalogs_financingwidget', 'financingwidget_id');
    }
}
