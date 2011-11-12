<?php
class Application_Form_Form extends Zend_Form {

    protected $_standardElementDecorator = array(
        'ViewHelper',
        array('HtmlTag', array('tag'=>'dd')),
        array('LabelError', array('escape'=>false, 'tag'=>'dt')),
        );

    protected $_captchaElementDecorator = array(
        'ViewHelper',
        array('HtmlTag', array('tag'=>'dd')),
        array('LabelError', array('escape'=>false, 'tag'=>'dt')),
        );

    public function __construct($options = null) {
        // Path setting for custom decorations MUST ALWAYS be first!
        $this->addElementPrefixPath('Application_Form_Decorator', APPLICATION_PATH.'/forms/decorator/', 'decorator');
        $this->addElementPrefixPath('Admin_Filter', APPLICATION_PATH.'/modules/admin/filters/', 'filter');
        $this->_setupTranslation();
        parent::__construct($options);

        $this->setAttrib('accept-charset', 'UTF-8');

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

