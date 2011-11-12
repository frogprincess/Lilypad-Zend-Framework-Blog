<?php

class Application_Model_AuthorMapper extends Application_Model_Mapper
{

    protected $_tableName = 'authors';
    protected $_entityClass = 'Application_Model_Author';

    public function save(Application_Model_Author $author)
    {
        if (!$author->id) {
            $data = array(
                'fullname' => $author->fullname,
                'username' => $author->username,
                'email' => $author->email,
                'url' => $author->url
            );
            $author->id = $this->_getGateway()->insert($data);
            $this->_setIdentity($author->id, $author);
        } else {
            $data = array(
                'id' => $author->id,
                'fullname' => $author->fullname,
                'username' => $author->username,
                'email' => $author->email,
                'url' => $author->url
            );
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $author->id);
            $this->_getGateway()->update($data, $where);
        }
    }

    public function findById($id)
    {
        if ($this->_getIdentity($id)) {
            return $this->_getIdentity($id);
        }
        $result = $this->_getGateway()->find($id)->current();
        $author = new $this->_entityClass(array(
            'id' => $result->id,
            'fullname' => $result->fullname,
            'username' => $result->username,
            'email' => $result->email,
            'url' => $result->url
        ));
        $this->_setIdentity($id, $author);
        return $author;
    }
    
    public function find($username)
    {
        $where = $this->_getGateway()->getAdapter()->quoteInto('username = ?', $username);
        $result = $this->_getGateway()->fetchRow($where, 'username');
        $author = new $this->_entityClass(array(
            'id' => $result->id,
            'fullname' => $result->fullname,
            'username' => $result->username,
            'email' => $result->email,
            'url' => $result->url
        ));
        $this->_setIdentity($result->id, $author);
        return $author;
    }

    public function delete($author)
    {
        if ($author instanceof Application_Model_Author) {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $author->id);
        } else {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $author);
        }
        $this->_getGateway()->delete($where);
    }

}
