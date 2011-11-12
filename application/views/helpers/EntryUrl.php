<?php

class Application_View_Helper_EntryUrl
{

    public $view = null;

    public function entryUrl($entry)
    {
        if (is_array($entry)) {
            $entry = new ArrayObject($entry, ArrayObject::ARRAY_AS_PROPS);
        }
        if (!is_object($entry) || !isset($entry->title) || !isset($entry->id)
                || empty($entry->title) || empty($entry->id)) {
            throw new Zend_View_Helper_Exception(
                'Some or all of entry\'s id and title is empty'
                . 'or entry parameter is not a valid object/array'
            );
        }
        if (!ctype_digit($entry->id)) {
            throw new Zend_View_Helper_Exception(
                'Entry id must be comprised only of digits'
            );
        }
        $front = Zend_Controller_Front::getInstance();
        $url = rtrim($front->getBaseUrl(), '/') . '/';
        $title = $this->_filterTitle($entry->title);
        $url .= $title . '-' . $entry->id;
        return $url;
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    protected function _filterTitle($title)
    {
        $title = preg_replace(
            array("/[^[:alnum:]\s\._!;,\+\-%]/", "/[\s]+/"),
            array('', '-'), $title
        );
        return $title;
    }


}
