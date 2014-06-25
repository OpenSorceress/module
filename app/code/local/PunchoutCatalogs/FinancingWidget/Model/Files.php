<?php

class PunchoutCatalogs_FinancingWidget_Model_Files extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        //path to resource model
        $this->_init('punchoutcatalogs_financingwidget/files');
    }
}
