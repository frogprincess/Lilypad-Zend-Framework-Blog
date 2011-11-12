<?php
class Admin_Form_Form extends Zend_Form {

    protected $_standardElementDecorator = array(
        'ViewHelper',
        array('LabelError', array('escape'=>false)),
        array('HtmlTag', array('tag'=>'li'))
        );

    protected $_buttonElementDecorator = array(
        'ViewHelper'
        );

    protected $_standardGroupDecorator = array(
        'FormElements',
        array('HtmlTag', array('tag'=>'ul')),
        'Fieldset'
        );

    protected $_buttonGroupDecorator = array(
        'FormElements',
        'Fieldset'
        );

    protected $_noElementDecorator = array(
        'ViewHelper'
    );

    public function __construct($options = null) {
        // Path setting for custom decorations MUST ALWAYS be first!
        $this->addElementPrefixPath('Admin_Form_Decorator', APPLICATION_PATH.'/modules/admin/forms/decorator/', 'decorator');
        $this->addElementPrefixPath('Admin_Filter', APPLICATION_PATH.'/modules/admin/filters/', 'filter');
        $this->_setupTranslation();
        parent::__construct($options);

        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setDecorators(array(
            'FormElements',
            'Form'
            ));
    }

    protected function _setupTranslation() {
        if (self::getDefaultTranslator()) {
            return;
        }
        
        $path = dirname(dirname(dirname(__FILE__)))
        . '/translate/forms.php';
        $translate = new Zend_Translate('array', $path, 'en');
        Zend_Form::setDefaultTranslator($translate);
    }
}
