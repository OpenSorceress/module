<?php

class PunchoutCatalogs_FinancingWidget_FinancingwidgetController extends Mage_Core_Controller_Front_Action {

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch() {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Tab view
     */
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save action - save or delete a document
     */
    public function saveAction() {
        $customerId = Mage::getSingleton('customer/session')->getId();

        $data = $this->getRequest()->getPost();
        if($customerId && $data && isset($data['financingwidgetdoccounter'])){
            try {
                $filesCount = (int)$data['financingwidgetdoccounter'];
                $filesCount = min($filesCount, 20);

                $files = array();
                for($i = 0; $i <= $filesCount; $i++){
                    if(isset($_FILES['financingwidgetdoc_'.$i]) && !empty($_FILES['financingwidgetdoc_'.$i]['tmp_name'])){
                        $file['name'] = $_FILES['financingwidgetdoc_'.$i]['name'];
                        $file['tmp_name'] = $_FILES['financingwidgetdoc_'.$i]['tmp_name'];
                        $files[] = $file;
                    }
                }

                $deleteList = array();
                foreach($data as $key => $value){
                    if(strpos($key, 'delete-') === 0 && $value){
                        $id = str_replace('delete-', '', $key);
                        $deleteList[] = $id;
                    }
                }

                $customer = Mage::getModel('customer/customer')->load($customerId);

                $return = false;
                if(count($deleteList)){
                    $return = true;
                    Mage::helper('punchoutcatalogs_financingwidget')->removeAttachments($customer, $deleteList);
                }

                if(count($files)){
                    $return = true;
                    Mage::helper('punchoutcatalogs_financingwidget')->saveAttachments($customer, $files);
                }
                if($return){
                    Mage::getSingleton('core/session')->addSuccess(
                        Mage::helper('punchoutcatalogs_taxexemption')->__('Financing request is saved!')
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e,
                    Mage::helper('punchoutcatalogs_financingwidget')->__('Error: Financing Document is not saved!')
                );
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
                return;

            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
    }

}
