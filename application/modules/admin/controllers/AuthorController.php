<?php
class Admin_AuthorController extends Zend_Controller_Action {

    public function loginAction() {
        $form = new Admin_Form_AuthorLogin;
        if (!$this->getRequest()->isPost()) {
            $this->view->loginForm = $form;
            return;
        } elseif (!$form->isValid($_POST)) {
            $this->view->failedValidation = true;
            $this->view->loginForm = $form;
        return;
        }

        $values = $form->getValues();

        // Setup DbTable adapter
        $adapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table::getDefaultAdapter() // set earlier in Bootstrap
            );
        $adapter->setTableName('authors');
        $adapter->setIdentityColumn('username');
        $adapter->setCredentialColumn('password');
        $adapter->setIdentity($values['name']);
        $adapter->setCredential(
            hash('SHA256', $values['password'])
            );

        // authentication attempt
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);

        // authentication succeeded
        if ($result->isValid()) {
            $auth->getStorage()
            ->write($adapter->getResultRowObject(null, 'password'));
            $this->view->passedAuthentication = true;
            $this->_forward('list', 'entry', 'admin');
        } else { // or not! Back to the login page!
            $this->view->failedAuthentication = true;
            $this->view->loginForm = $form;
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index', 'index');
    }
}

