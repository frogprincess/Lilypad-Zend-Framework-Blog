<?php

class Application_View_Helper_TagUrl
{

    public $view = null;


    public function tagUrl($tag)
    {
        $front = Zend_Controller_Front::getInstance();
        $url = rtrim($front->getBaseUrl(), '/') . '/';
        $url .= 'tag/';
        $url .= str_replace(' ', '_', $tag);
        return $url;
    }


}
