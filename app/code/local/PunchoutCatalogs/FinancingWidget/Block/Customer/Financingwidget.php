<?php

class PunchoutCatalogs_FinancingWidget_Block_Customer_Financingwidget extends Mage_Customer_Block_Account_Dashboard
{
    public function getFiles(){
        $collection = Mage::getModel('punchoutcatalogs_financingwidget/files')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getCustomer()->getId())
            ->setOrder('filename', 'ASC')
        ;
        $helper = Mage::helper('punchoutcatalogs_financingwidget');

        $return = array();
        foreach($collection as $item){
            $fileName = $item->getData('filename');
            $retItem['link'] = $helper->getFileLink($this->getCustomer()->getId(), $fileName, true);
            $retItem['name'] = $item->getData('filename');
            $retItem['id_name'] = $item->getData('financingwidget_id');
            $return[] = $retItem;
        }
        return $return;
    }
}
