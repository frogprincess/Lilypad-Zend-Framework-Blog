<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->bodyId = 'home';

        $this->description = array(

        );

        $this->keywords = array(

        );

        $this->view->headMeta()->appendName('keywords', implode(",", $this->keywords));
    }

    public function indexAction()
    {
        // action body
    }


}

