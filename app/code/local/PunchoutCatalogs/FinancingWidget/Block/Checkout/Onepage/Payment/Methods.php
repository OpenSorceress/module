<?php

class PunchoutCatalogs_FinancingWidget_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{

    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract|null
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        $id = Mage::getSingleton('checkout/session')->getQuote()->getCustomerGroupId();
        $expulsion = Mage::getModel('customer/group')->load($id)->getData('payment_group_expulsion');
        $expulsion = explode(',', $expulsion);
        $return =  $method && $method->canUseCheckout() && parent::_canUseMethod($method);

        if($return && in_array($method->getCode(), $expulsion)){
            $return = false;
        }

        return $return;
    }
}
