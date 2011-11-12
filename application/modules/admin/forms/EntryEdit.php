<?php

class Admin_Form_EntryEdit extends Admin_Form_EntryAdd {

    public function init() {
        parent::init();
        $this->setAction('/admin/entry/edit');

        // What entry id are we editing?!
        $this->addElement('hidden', 'id', array(
            'decorators' => $this->_noElementDecorator,
            'validators' => array(
                'Digits'
                ),
            'required' => true
            ));
        $this->addElement('hidden', 'author', array(
            'decorators' => $this->_noElementDecorator,
            ));

        $this->getDisplayGroup('entrydata')->setLegend('Edit Entry');

    }

}