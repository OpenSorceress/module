<?php

class PunchoutCatalogs_FinancingWidget_Block_Adminhtml_Customer_Edit_Tab_Financingwidget extends
    Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));

        /** @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset('punchoutcatalogs_financingwidget_fieldset',
            array('legend' => Mage::helper('punchoutcatalogs_financingwidget')->__('Check Financing Documents'))
        );

        $collection = Mage::getModel('punchoutcatalogs_financingwidget/files')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('eq' => $customer->getId()))
            ->setOrder('filename', 'ASC')
        ;
        $recordsCount = $collection->getSize();
        $files = array();
        foreach($collection as $item){
            $it['filename'] = $item->getData('filename');
            $it['id'] = $item->getId();
            $files[] = $it;
        }

        $fieldset->addField('show_financing_payment', 'select', array(
                'label'     => Mage::helper('punchoutcatalogs_financingwidget')->__('Enable Financing payment'),
                'required'  => false,
                'disabled' => false,
                'name' => 'show_financing_payment',
                'onclick'   => 'this.value = this.checked.value;',
                'options'   => array(
                    '0' => Mage::helper('punchoutcatalogs_financingwidget')->__('Use Group Setting'),
                    '1' => Mage::helper('punchoutcatalogs_financingwidget')->__('No'),
                    '2' => Mage::helper('punchoutcatalogs_financingwidget')->__('Yes')

                )
            ));

        $label = 'Upload Files';
        for($i = 0; $i <= 19; ++$i){
            if($i < $recordsCount){
                $customer->setData('link_'.$i, $files[$i]['filename']);
                $fileName = $files[$i]['filename'];
                $fieldset->addField('link_'.$i, 'link', array(
                    'label'     => Mage::helper('punchoutcatalogs_financingwidget')->__('Link'),
                    'style'   => "",
                    'target' => '_blank',
                    'after_element_html' => '
                        <input type="checkbox" onclick="this.value = this.checked ? 1 : 0;" name="delete_financingwidgetdoc_'.$files[$i]['id'].'">
                        <span style="color:red;">&lt;- Delete?</span>' ,
                    'href' => $destPath = Mage::helper('punchoutcatalogs_financingwidget')->getFileLink(
                            $customer->getId(), $fileName, true
                        )
                ));
            }else{
                $fieldset->addField('financingwidgetdoc_'.$i, 'file', array(
                        'label'     => Mage::helper('punchoutcatalogs_financingwidget')->__($label),
                        'required'  => false,
                        'disabled' => false,
                        'readonly' => true,
                        'name'      => 'financingwidgetdoc_'.$i
                    ));
                $label = '';
            }
        }

        $form->setValues($customer->getData());
        $this->setForm($form);

        return $this;
    }
}

