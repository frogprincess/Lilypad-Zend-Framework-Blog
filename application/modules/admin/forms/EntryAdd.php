<?php
class Admin_Form_EntryAdd extends Admin_Form_Form {

    public function init() {
        $this->setAction('/admin/entry/add');

        // Display Group #1 : Entry Data

        $this->addElement('text', 'title', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Title:',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 63
                ),
            'validators' => array(
                array('StringLength', false, array(3,200))
                ),
            'filters' => array('StringTrim', 'StripSlashes'),
            'required' => true
            ));

        $this->addElement('text', 'description', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Description:',
            'attribs' => array(
                'maxlength' => 500,
                'size' => 63
                ),
            'validators' => array(
                array('StringLength', false, array(3,500))
                ),
            'filters' => array('StringTrim', 'StripSlashes'),
            'required' => true
            ));

        $this->addElement('text', 'date', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Date:',
            'attribs' => array(
                'maxlength' => 16,
                'size' => 16
                ),
            'value' => date('d-m-Y H:i:s', Zend_Date::now()->getTimestamp()),
            'validators' => array(
                array('Date', false, array('d-m-Y H:i:s', 'en'))
                ),
                'required' => true
                ));

        $this->addElement('textarea', 'body', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Entry Body:',
            'filters' => array('StripSlashes'),
            'required' => true
            ));

        $this->addElement('textarea', 'extended_body', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Extended Body:',
            'filters' => array('StripSlashes'),
            ));

        $this->addElement('checkbox', 'hide', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Hide:',
            ));

        $this->addElement('text', 'tags', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Tags:',
            'attribs' => array(
                'maxlength' => 500,
                'size' => 63
                ),
            'filters' => array('StripSlashes'),
            ));

        $this->addDisplayGroup(
            array('title', 'description', 'date','body','extended_body', 'tags', 'hide'), 'entrydata',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => $this->_standardGroupDecorator,
                'legend' => 'Entry'
                )
            );

        // Display Group #2 : Submit

        $this->addElement('submit', 'submit', array(
            'decorators' => $this->_buttonElementDecorator,
            'label' => 'Save'
            ));

        $this->addDisplayGroup(
            array('submit'), 'entrydatasubmit',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => $this->_buttonGroupDecorator,
                'class' => 'submit'
                )
            );
    }
}