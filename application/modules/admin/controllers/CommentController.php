<?php
class Admin_CommentController extends Zend_Controller_Action {

    public function init() {

    }

    public function listAction() {
        $comment_mapper = new Application_Model_CommentMapper();
        $this->view->entries = $comment_mapper->findAll(false);
    }

    public function hideAction() {
        $comment_mapper = new Application_Model_CommentMapper();
        $comment = $comment_mapper->find($this->_getParam('id'));
        $comment->hide = 1;
        $comment_mapper->save($comment);
        return $this->_response->setRedirect('/admin/entry/list');
    }

    public function showAction() {
        $comment_mapper = new Application_Model_CommentMapper();
        $comment = $comment_mapper->find($this->_getParam('id'));
        $comment->hide = 0;
        $comment_mapper->save($comment);
        return $this->_response->setRedirect('/admin/entry/list');
    }


}