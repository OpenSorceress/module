<?php

class PunchoutCatalogs_FinancingWidget_Model_Method_Financingcredit extends Mage_Payment_Model_Method_Checkmo {
    protected $_code  = 'financingcredit';

    public function isAvailable($quote = null)
    {
        $return = parent::isAvailable($quote);
        if($return){
            $bqCheck = Mage::helper('punchoutcatalogs_financingwidget')->isEnabledFinancingPayment();
            if(!$bqCheck){
                $return = false;
            }
        }
        return $return;
    }
}
