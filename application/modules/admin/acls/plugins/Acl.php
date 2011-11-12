<?php
class Admin_Acl_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    protected $_auth = null;
    protected $_acl = null;

    public function __construct(Zend_Auth $auth, Zend_Acl $acl) {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // Before you lot start, this is the laziest possible
        // means of assigning roles. Hands up – I'm guilty!
        // Store to the Author table if you prefer.
        // Check if the user is not logged in
        if ('admin' == $request->getModuleName()
                && !$this->_auth->hasIdentity())
        {
            $role = 'guest';
            return $this->_redirect($request, 'author', 'login', 'admin');
        }

        // The user is logged in
        // Check if the authenticated user tries to access the users/login path
        if ('admin' == $request->getModuleName()
                && 'author' 		 == $request->getControllerName()
                && 'login'		 == $request->getActionName())
        {
            $role = 'author';
            return $this->_redirect($request, 'index', 'index', 'admin');
        }

    }

    protected function _redirect($request, $controller, $action, $module)
    {
        if ($request->getControllerName() == $controller
                && $request->getActionName()  == $action
                && $request->getModuleName()  == $module)
        {
                return TRUE;
        }

        $url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $url .= '/'   . $module
                 . '/' . $controller
                 . '/' . $action;


//       if (DEBUG)
//       {
//           debug_redirect($url);
//       }

       return $this->_response->setRedirect($url);
    }
}