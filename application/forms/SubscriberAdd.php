<?php

class Application_Form_SubscriberAdd extends Application_Form_Form {

    public function init() {
        $this->setMethod('post');
        $this->setAction('/blog');
        $this->setAttrib('id', 'subscriberform');

        $this->addElement('text', 'subscribeemail');
        $emailElement = $this->getElement('subscribeemail');
        $emailElement->setLabel('Just enter your email address to subscribe to my blog and receive notifications of new posts by email.');
        $emailElement->setOrder(2)->setRequired('true');
        $emailElement->setDecorators($this->_standardElementDecorator);
        // Add validator
        $emailElement->addValidator(new Zend_Validate_EmailAddress());
        // Add filter
        $emailElement->addFilter(new Zend_Filter_StripTags());

        $this->addElement('submit', 'submit');
        $submitButton = $this->getElement('submit');
        $submitButton->setLabel('Subscribe');
        $submitButton->setOrder(6);

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => '/blog/subscriberform.phtml'))
        ));

        return $this;
    }
}