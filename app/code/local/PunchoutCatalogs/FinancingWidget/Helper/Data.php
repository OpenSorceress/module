<?php

class PunchoutCatalogs_FinancingWidget_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Get path to file, absolute or url
     *
     * @param $customerId
     * @param $fileName
     * @param bool $link
     * @return string
     */
    public function getFileLink($customerId, $fileName, $link = false){
        if($link){
            $destPath = Mage::getBaseUrl('media') .
                $this->_getPathToFile($customerId, $fileName) .
                $this->getStorageFileName($customerId, $fileName);
        }else{
            $destPath = Mage::getBaseDir('media') . DS .
                $this->_getPathToFile($customerId, $fileName) .
                $this->getStorageFileName($customerId, $fileName);
        }

        return $destPath;
    }

    public function removeFile($customerId, $fileName){
        $destPath = $this->getFileLink($customerId, $fileName);
        if(is_file($destPath)){
            unlink($destPath);
        }
    }

    /**
     * Get path to files starting with Media folder
     *
     * @param $customerId
     * @param $fileName
     * @return string
     */
    protected function _getPathToFile($customerId, $fileName){
        // TODO extend logic
        $return = 'financing'.DS.$customerId.DS;
        if(!is_dir(Mage::getBaseDir('media') . DS . $return)){
            mkdir(Mage::getBaseDir('media') . DS . $return, 0777);
        }
        return $return;
    }

    /**
     * Get exact name of end-file
     *
     * @param $customerId
     * @param $fileName
     * @return string
     */
    public function getStorageFileName($customerId, $fileName){
        return $fileName;
    }

    public function removeAttachments($customer, $ids){
        $model = Mage::getModel('punchoutcatalogs_financingwidget/files');
        foreach($ids as $id){
            $model->load($id);
            if($model->getId()){
                $fileName = $model->getData('filename');
                $this->removeFile($customer->getId(), $fileName);
                $model->delete();
            }
        }
    }


    public function saveAttachments($customer, $files){
        $emailFiles = array();
        foreach($files as $key => $file){
            if(is_file($file['tmp_name'])){
                $model = Mage::getModel('punchoutcatalogs_financingwidget/files');
                $fileName = $this->checkFileName($customer->getId(), $file['name']);
                $time = Mage::getModel('core/date')->timestamp();
                $model->setData('customer_id', $customer->getId());
                $model->setData('customer_email', $customer->getData('email'));
                $model->setData('customer_firstname', $customer->getData('firstname'));
                $model->setData('customer_lastname', $customer->getData('lastname'));
                $model->setData('filename', $fileName);
                $model->setData('created_time', $time);
                $model->save();
                if(move_uploaded_file($file['tmp_name'], $this->getFileLink($customer->getId(), $fileName))){
                    $emailFiles[$fileName] = $file['name'];
                }
            }
        }

        if(count($emailFiles)){
            $emailTemplateVariables = array();
            $emailTemplateVariables['customer_name'] = $customer->getData('firstname').' '.$customer->getData('lastname');
            $emailTemplateVariables['customer_email'] = $customer->getData('email');

            // Place Email TO's here
            $emailTargets = array();
            $emailTargets[] = $customer->getData('email');

            foreach($emailTargets as $emailTo){
                $emailTemplate  = Mage::getModel('core/email_template')
                    ->loadDefault('financingwidget_email_template');
                foreach($emailFiles as $fileName => $fileUserName){
                    $filePath = $this->getFileLink($customer->getId(), $fileName);
                    if(is_file($filePath)){
                        $this->addEmailAttachment($emailTemplate, $filePath, $fileUserName);
                    }
                }
                $emailTemplate->send($emailTo,'Steelcase', $emailTemplateVariables);
            }
        }
    }

    private function addEmailAttachment($emailTemplate, $filePath, $fileName)
    {
        $file = file_get_contents($filePath);
        $attachment = $emailTemplate->getMail()->createAttachment($file);
        if(!is_object($attachment)) return $this;
//        $attachment->type = 'application/pdf';
        $attachment->filename = $fileName;
        return $this;
    }

    /**
     * Process file with duplicating names
     *
     * @param $customerId
     * @param $fileName
     * @param int $counter
     * @return string
     */
    public function checkFileName($customerId, $fileName, $counter = 0){
        if($counter){
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $endFileName = str_replace('.'.$ext, '', $fileName) . '_' . $counter . '.' . $ext;
        }else{
            $endFileName = $fileName;
        }
        $counter++;
        if(is_file($this->getFileLink($customerId, $endFileName))){
            return $this->checkFileName($customerId, $fileName, $counter);
        }else{
            return $endFileName;
        }
    }

    /**
     * Is customer enabled for using Financing payment?
     *
     * @param $customerId
     * @return bool
     */
    public function isEnabledFinancingPayment($customerId = null){
        $return = false;

        if($customerId){
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }elseif(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer->getId();
        }

        if($customerId && !empty($customer)){
            $return = $customer->getData('show_financing_payment');
            if($return){
                $return = $return > 1 ? true : false;
            }else{
                $customerGroupId = $customer->getData('group_id');
                $customerGroup = Mage::getModel('customer/group')->load($customerGroupId);
                $return = $customerGroup->getData('show_financing_payment')  ? true : false;
            }
        }
        return $return;
    }

    /**
     * Get active payment methods
     *
     * @param bool $keyValue
     * @return array
     */
    public function getPaymentMethods($keyValue = false){
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        $array = array();
        foreach ($payments as $paymentCode=>$paymentModel) {
            if($paymentCode == 'financingcredit'){
                continue; // have a separate setting
            }
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $method['value'] = $paymentCode;
            $method['label'] = $paymentTitle;
            $methods[] = $method;

            $array[$paymentCode] = $paymentTitle;
        }
        if($keyValue){
            return $array;
        }else{
            return $methods;
        }
    }
}
