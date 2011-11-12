<?php

class Application_Model_EntryMapper extends Application_Model_Mapper
{

    protected $_tableName = 'entries';
    protected $_entityClass = 'Application_Model_Entry';

    public function save(Application_Model_Entry $entry)
    {
        if (!$entry->id) {
            $data = array(
                'title' => $entry->title,
                'content' => $entry->content,
                'extended_content' => $entry->extended_content,
                'hide' => $entry->hide,
                'published_date' => $entry->published_date,
                'last_modified' => $entry->last_modified,
                'author' => $entry->author->username,
                'description' => $entry->description,
            );
            $entry->id = $this->_getGateway()->insert($data);
            $this->_setIdentity($entry->id, $entry);
        } else {
            $data = array(
                'id' => $entry->id,
                'title' => $entry->title,
                'content' => $entry->content,
                'last_modified' => $entry->date,
                'extended_content' => $entry->extended_content,
                'hide' => $entry->hide,
                'description' => $entry->description,
            );
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $entry->id);
            $this->_getGateway()->update($data, $where);
        }
        return $entry->id;
    }

    private function createObject($data) {
        $entry = new $this->_entityClass(array(
            'id' => $data->id,
            'title' => $data->title,
            'content' => $data->content,
            'extended_content' => $data->extended_content,
            'hide' => $data->hide,
            'published_date' => $data->published_date,
            'last_modified' => $data->last_modified,
            'description' => $data->description,
        ));

        $entry->setReferenceId('author', $data->author);
        $this->_setIdentity($data->id, $entry);
        $entry->getComments();
        $entry->getTags();
        return $entry;
    }

    public function find($id)
    {
        if ($this->_getIdentity($id)) {
            return $this->_getIdentity($id);
        }
        $result = $this->_getGateway()->find($id)->current();
        if (!$result) {
            return null;
        }
        $entry = $this->createObject($result);
        return $entry;
    }

    public function findAll($num=false) {
        //$where = $this->_getGateway()->getAdapter()->quoteInto('username = ?', $username);
        $entries = array();

        if ($num) {
            $select = $this->_getGateway()->select()
                ->from($this->_getGateway(), array('id','title','published_date','author','content',
                    'extended_content', 'hide', 'last_modified', 'description'))
                ->order('published_date DESC')
                ->limit($num);
        } else {
            $select = $this->_getGateway()->select()
                ->from($this->_getGateway(), array('id','title','published_date','author','content',
                    'extended_content', 'hide', 'last_modified', 'description'))
                ->order('published_date DESC');
        }

        $result = $this->_getGateway()->fetchAll($select);
        foreach ($result as $row) {
            $entry = $this->createObject($row);
            $entries[$row->id] = $entry;            
        }
        return $entries;
    }

    public function findByTag($tag) {
        //$where = $this->_getGateway()->getAdapter()->quoteInto('username = ?', $username);
        $entries = array();
        $select = $this->_getGateway()->select()
                ->setIntegrityCheck(false)
                ->from($this->_getGateway(), array('id','title','published_date','author','content',
                     'extended_content', 'hide', 'last_modified', 'description'))
                ->order('published_date DESC')
                ->join('tags', 'tags.entry=entries.id', '')
                ->where('tags.tag=?', $tag)
                ->order('published_date DESC');

        $result = $this->_getGateway()->fetchAll($select);
        foreach ($result as $row) {
            $entry = $this->createObject($row);
            $entries[$row->id] = $entry;
        }
        return $entries;
    }

    public function delete($entry) {
        if ($entry instanceof Application_Model_Entry) {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $entry->id);
        } else {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $entry);
        }
        $this->_getGateway()->delete($where);
    }
    

}
