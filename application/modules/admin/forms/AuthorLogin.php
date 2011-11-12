<?php
class Admin_Form_AuthorLogin extends Admin_Form_Form {

    public function init() {
        $this->setAction('/admin/author/login');

        // Display Group #1 : Credentials

        $this->addElement('text', 'name', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Name:',
            'validators' => array(
                array('StringLength', false, array(5,20))
                ),
            'required' => true
            ));

        $this->addElement('password', 'password', array(
            'decorators' => $this->_standardElementDecorator,
            'label' => 'Password:',
            'required' => true
            ));

        $this->addDisplayGroup(
        array('name', 'password'), 'authorlogin',
            array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => $this->_standardGroupDecorator,
            'legend' => 'Credentials'
            )
        );

        // Display Group #2 : Submit

        $this->addElement('submit', 'submit', array(
            'decorators' => $this->_buttonElementDecorator,
            'label' => 'Submit'
            ));

        $this->addDisplayGroup(
        array('submit'), 'authorloginsubmit',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => $this->_buttonGroupDecorator,
                'class' => 'submit' // fieldset class attribute for some later styling
                )
        );
    }

}
