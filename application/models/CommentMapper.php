<?php

class Application_Model_CommentMapper extends Application_Model_Mapper
{

    protected $_tableName = 'comments';
    protected $_entityClass = 'Application_Model_Comment';

    public function save(Application_Model_Comment $comment)
    {
        if (!$comment->id) {
            $data = array(
                'username' => $comment->username,
                'email' => $comment->email,
                'url' => $comment->url,
                'comment' => $comment->comment,
                'entry' => $comment->entry,
                'published_date' => $comment->published_date,
                'hide' => $comment->hide,
            );
            $comment->id = $this->_getGateway()->insert($data);
            $this->_setIdentity($comment->id, $comment);
        } else {
            $data = array(
                'id' => $comment->id,
                'username' => $comment->username,
                'email' => $comment->email,
                'url' => $comment->url,
                'comment' => $comment->comment,
                'entry' => $comment->entry,
                'published_date' => $comment->published_date,
                'hide' => $comment->hide,
            );
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $comment->id);
            $this->_getGateway()->update($data, $where);
        }
    }

    public function find($id) {
        if ($this->_getIdentity($id)) {
            return $this->_getIdentity($id);
        }
        $result = $this->_getGateway()->find($id)->current();
        $comment = new $this->_entityClass(array(
            'id' => $result->id,
            'username' => $result->username,
            'email' => $result->email,
            'url' => $result->url,
            'comment' => $result->comment,
            'entry' => $result->entry,
            'published_date' => $result->published_date,
            'hide' => $result->hide,
        ));
        $this->_setIdentity($id, $comment);
        return $comment;
    }

    public function findAll($eid, $include_hidden=true) {
        $where = $include_hidden? 'hide=0 or hide=1' : 'hide=0';
        $select = $this->_getGateway()->select()
                ->from($this->_getGateway(), array('id','username',
                     'email','url', 'comment', 'entry', 'published_date', 'hide'))
                ->where('entry = '.$eid)
                ->where($where)
                ->order('published_date ASC');
        $raw = $this->_getGateway()->fetchAll($select)->toArray();
        return new Application_Model_CommentCollection($raw, $this);
    }

    public function delete($comment) {
        if ($comment instanceof Application_Model_Comment) {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $comment->id);
        } else {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $comment);
        }
        $this->_getGateway()->delete($where);
    }



}