<?php

//include_once Mage::getBaseDir().DS.'app'.DS.'code'.DS.'core'.
//    DS.'Mage'.DS.'Adminhtml'.DS.'controllers'.DS.'CustomerController.php';
include_once Mage::getBaseDir().DS.'app'.DS.'code'.DS.'local'.
    DS.'PunchoutCatalogs'.DS.'CustomerBalance'.DS.'controllers'.DS.'Adminhtml'.DS.'CustomerController.php';
//class PunchoutCatalogs_FinancingWidget_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController {
class PunchoutCatalogs_FinancingWidget_Adminhtml_CustomerController extends PunchoutCatalogs_CustomerBalance_Adminhtml_CustomerController {
    /**
     * Customer financingwidget grid
     */
    public function financingwidgetAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save customer action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        if($data){
            try {
                $customerId = $data['customer_id'];

                $filesCount = 20;

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
                    if(strpos($key, 'delete_financingwidgetdoc_') === 0 && $value){
                        $id = str_replace('delete_financingwidgetdoc_', '', $key);
                        $deleteList[] = $id;
                    }
                }

                $customer = Mage::getModel('customer/customer')->load($customerId);
                if(isset($data['show_financing_payment'])){
                    $customer->setData('show_financing_payment', $data['show_financing_payment'])->save();
                }

                if(count($deleteList)){
                    Mage::helper('punchoutcatalogs_financingwidget')->removeAttachments($customer, $deleteList);
                }

                if(count($files)){
                    Mage::helper('punchoutcatalogs_financingwidget')->saveAttachments($customer, $files);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $data['customer_id'])));
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=> $data['customer_id'])));
                return $this;

            }
        }

        return parent::saveAction();
    }



}
