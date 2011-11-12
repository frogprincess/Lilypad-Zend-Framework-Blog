<?php

class Application_Model_TagMapper extends Application_Model_Mapper
{

    protected $_tableName = 'tags';
    protected $_entityClass = 'Application_Model_Tag';

    public function save(Application_Model_Tag $tag)
    {
        if (!$tag->id) {
            $data = array(
                'tag' => $tag->tag,
                'entry' => $tag->entry,
            );
            $tag->id = $this->_getGateway()->insert($data);
            $this->_setIdentity($tag->id, $tag);
        } else {
            $data = array(
                'id' => $tag->id,
                'tag' => $tag->tag,
                'entry' => $tag->entry,
            );
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $tag->id);
            $this->_getGateway()->update($data, $where);
        }
    }

    public function find($id) {
        if ($this->_getIdentity($id)) {
            return $this->_getIdentity($id);
        }
        $result = $this->_getGateway()->find($id)->current();
        $tag = new $this->_entityClass(array(
            'id' => $result->id,
            'tag' => $tag->tag,
        ));
        $this->_setIdentity($id, $tag);
        return $tag;
    }

    public function findAll($eid) {
        $select = $this->_getGateway()->select()
                ->from($this->_getGateway(), array('id', 'tag'))
                ->where('entry = '.$eid);
        $raw = $this->_getGateway()->fetchAll($select)->toArray();
        return new Application_Model_TagCollection($raw, $this);
    }

    public function findGrouped() {
        $select = $this->_getGateway()->select();
        $select->setIntegrityCheck(false) // allows joins
                ->from($this->_getGateway(), array('tag', 'count(tag) num'))
                ->join('entries', 'tags.entry = entries.id')
                ->where('entries.hide = 0')
                ->group('tag');
        $raw = $this->_getGateway()->fetchAll($select)->toArray();
        return $raw;
    }

    public function delete($tag) {
        if ($tag instanceof Application_Model_Tag) {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $tag->id);
        } else {
            $where = $this->_getGateway()->getAdapter()
                ->quoteInto('id = ?', $tag);
        }
        $this->_getGateway()->delete($where);
    }



}