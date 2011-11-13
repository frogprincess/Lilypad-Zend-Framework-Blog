<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    }

    protected function _initAutoload()
    {
        $this->options = $this->getOptions();
        Zend_Registry::set('config.recaptcha', $this->options['recaptcha']);
        Zend_Registry::set('config.message', $this->options['message']);

        $db = Zend_Db::factory('PDO_MYSQL', $this->options['db']);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);

        $resourceLoader = new Zend_Application_Module_Autoloader (array(
          'basePath'  => APPLICATION_PATH . '/modules/admin',
          'namespace' => 'Admin',
        ));

        $resourceLoader->addResourceType('acl', 'acls/', 'Acl');
        $resourceLoader->addResourceType('acl_plugin', 'acls/plugins/', 'Acl_Plugin');
        $resourceLoader->addResourceType('filter', 'filters/', 'Filter');
        return $resourceLoader;
    }

    protected function _initSiteModules() {
        //Don't forget to bootstrap the front controller as the resource may not been created yet...
        $this->bootstrap("frontController");
        $front = $this->getResource("frontController");

        //Add modules dirs to the controllers for default routes...
        $front->addModuleDirectory(APPLICATION_PATH . '/modules');    

    }


    protected function _initRouter(){
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $tag_route = new Zend_Controller_Router_Route_Regex(
            'tag\/(.*)',
             array(
                 'module' => 'application',
                 'controller' => 'blog',
                 'action'     => 'tag'
             ),
             array(
                 'tag' => 1 // maps first subpattern "(*)" to "tag" parameter
             )
        );

        $router->addRoute('tag', $tag_route);

        $blog_route = new Zend_Controller_Router_Route_Regex(
            '[0-9a-z\._!;,\+\-%]+-(\d+)', // all possible single entry URLs
             array(
                 'module' => 'application',
                 'controller' => 'blog',
                 'action'     => 'view'
             ),
             array(
                 'id' => 1 // maps first subpattern "(\d+)" to "id" parameter
             )
        );

        $router->addRoute('entry', $blog_route);
    }

    protected function _initFooterLinks() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $tablegateway = new Zend_Db_Table('entries');
        $blog_mapper = new Application_Model_EntryMapper($tablegateway);
        $view->footer_blogs = $blog_mapper->findAll('4');
    }

     protected function _initModuleLangArray()
     {
         //Now let's define our language array to the registry so that we can use it...
         //Notice I have added "Application_" prefix to the registry to avoid any conflicts with other modules...
         Zend_Registry::set("Application_language", $this->_options[$this->options["language"]["selected"]]);
     }
}



