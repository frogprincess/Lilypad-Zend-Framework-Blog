<?php

class Application_Form_CommentAdd extends Application_Form_Form {

    public function init() {
        $this->setMethod('post');
        $this->setAction('#comment_form');
        $this->setAttrib('id', 'commentform');
        
        $this->addElement('hidden', 'entry');
        $entryElement = $this->getElement('entry');
        $entryElement->setValue($this->getAttrib('entry'));

        $this->addElement('hidden', 'published_date');
        $dateElement = $this->getElement('published_date');
        $dateElement->setValue(Zend_Date::now()->getTimestamp());

        $this->addElement('text', 'username');
        $usernameElement = $this->getElement('username');
        $usernameElement->setLabel('Username');
        $usernameElement->setOrder(1)->setRequired('true');
        $usernameElement->setDecorators($this->_standardElementDecorator);
        // Add validator
        $usernameElement->addValidator(new Zend_Validate_Alpha('true'));
        // Add filter
        $usernameElement->addFilter(new Zend_Filter_StripTags());

        $this->addElement('text', 'email');
        $emailElement = $this->getElement('email');
        $emailElement->setLabel('Email');
        $emailElement->setOrder(2)->setRequired('true');
        $emailElement->setDecorators($this->_standardElementDecorator);
        // Add validator
        $emailElement->addValidator(new Zend_Validate_EmailAddress());
        // Add filter
        $emailElement->addFilter(new Zend_Filter_StripTags());

        $this->addElement('text', 'url');
        $urlElement = $this->getElement('url');
        $urlElement->setLabel('Website');
        $urlElement->setOrder(3);
        // Add validator
        //$urlElement->addValidator(new Zend_Validate_Alnum());
        // Add filter
        $urlElement->addFilter(new Zend_Filter_StripTags());

        $this->addElement('textarea', 'comment');
        $commentElement = $this->getElement('comment');
        $commentElement->setLabel('Comment');
        $commentElement->setAttribs(array('rows' => 10,));
        
        $commentElement->setOrder(4)->setRequired('true');
        $commentElement->setDecorators($this->_standardElementDecorator);
        // Add validator
        //$commentElement->addValidator(new Zend_Validate_Alnum('true'));
        // Add filter
        $commentElement->addFilter(new Zend_Filter_StripTags());
        $commentElement->addFilter(new Admin_Filter_HtmlBody());

        $this->addElement('submit', 'submit');
        $submitButton = $this->getElement('submit');
        $submitButton->setLabel('Comment');
        $submitButton->setOrder(6);

        // Captcha - uncomment this if you want to use captcha
        // You will need to add captcha keys to application.ini
//        $recaptchaKeys = Zend_Registry::get('config.recaptcha');
//        $recaptcha = new Zend_Service_ReCaptcha($recaptchaKeys['publickey'], $recaptchaKeys['privatekey'],
//                NULL, array('theme' => 'clean'));
//
//        $captcha = new Zend_Form_Element_Captcha('captcha',
//            array(
//                'label' => 'Type the characters you see in the picture below.',
//                'captcha' =>  'ReCaptcha',
//                'captchaOptions'        => array(
//                    'captcha'   => 'ReCaptcha',
//                    'service' => $recaptcha
//                )
//            )
//        );

//        $captcha->setOrder(5);
//        $captcha->setDecorators($this->_captchaElementDecorator);
//        $this->addElement($captcha);

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => '/blog/commentform.phtml'))
        ));

        return $this;
    }
}