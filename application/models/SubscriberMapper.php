<?php

class Application_Model_SubscriberMapper extends Application_Model_Mapper
{

    protected $_tableName = 'subscribers';
    protected $_entityClass = 'Application_Model_Subscriber';

    public function save(Application_Model_Subscriber $subscriber)
    {
        if (!$subscriber->id) {
            $data = array(
                'email' => $subscriber->email,
            );
            $subscriber->id = $this->_getGateway()->insert($data);
            $this->_setIdentity($subscriber->id, $subscriber);
        } else {
            $data = array(
                'id' => $subscriber->id,
                'email' => $subscriber->email,
            );
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $subscriber->id);
            $this->_getGateway()->update($data, $where);
        }
        return $subscriber->id;
    }
    
    public function find()
    {
        $result = $this->_getGateway()->find($id)->current();
        $subscriber = new $this->_entityClass(array(
            'id' => $result->id,
            'email' => $result->email,
        ));
        $this->_setIdentity($result->id, $subscriber);
        return $subscriber;
    }

    public function findByEmail($email)
    {
        $where = $this->_getGateway()->getAdapter()->quoteInto('email = ?', $email);
        if ($result = $this->_getGateway()->fetchRow($where, 'email')) {
            $subscriber = new $this->_entityClass(array(
                'id' => $result->id,
                'email' => $result->email,
            ));
            $this->_setIdentity($result->id, $subscriber);
            return $subscriber;
        }
        return false;
    }

    public function findAll() {
        //$where = $this->_getGateway()->getAdapter()->quoteInto('username = ?', $username);
        $subscribers = array();
        $select = $this->_getGateway()->select()
                ->from($this->_getGateway(), array('id', 'email'));
        $result = $this->_getGateway()->fetchAll($select);
        foreach ($result as $row) {
            $subscriber = new $this->_entityClass(array(
                'id' => $row->id,
                'email' => $row->email,
            ));
            $subscribers[$row->id] = $subscriber;
        }
        return $subscribers;
    }

    public function delete($subscriber)
    {
        if ($subscriber instanceof Application_Model_Subscriber) {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $subscriber->id);
        } else {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $subscriber);
        }
        $this->_getGateway()->delete($where);
    }

}
