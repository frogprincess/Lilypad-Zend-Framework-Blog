<?php

class Application_Model_Entry extends Application_Model_Entity {

    protected $_data = array(
        'id' => null,
        'title' => '',
        'content' => '',
        'extended_content' => '',
        'hide' => 0,
        'published_date' => '',
        'last_modified' => '',
        'author' => null,
        'description' => '',
        'comment_count' => 0,
        'comments' => null,
        'tags' => null
    );

    protected $_references = array();
    protected $_authorMapperClass = 'Application_Model_AuthorMapper';
    protected $_authorMapper = null;
    protected $_commentCollectionClass = 'Application_Model_CommentCollection';
    protected $_commentMapperClass = 'Application_Model_CommentMapper';
    protected $_commentMapper = null;
    protected $_tagCollectionClass = 'Application_Model_TagCollection';
    protected $_tagMapperClass = 'Application_Model_TagMapper';
    protected $_tagMapper = null;

    public function __set($name, $value) {
        if ($name == 'author' && !$value instanceof Application_Model_Author) {
            throw new Application_Model_Exception('Author can only be set using'
            . ' an instance of Application_Model_Author');
        }
        if ($name == 'comments' && !$value instanceof Application_Model_CommentCollection) {
            throw new Application_Model_Exception('Comments can only be set using'
            . ' an instance of Application_Model_CommentCollection');
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        if ($name == 'author' && $this->getReferenceId('author')
        && !$this->_data['author'] instanceof Application_Model_Author) {
            if (!$this->_authorMapper) {
                $this->_authorMapper = new $this->_authorMapperClass;
            }
            $this->_data['author'] = $this->_authorMapper
                ->find($this->getReferenceId('author'));
        }
        if ($name == 'comments') {
            return $this->getComments();
        }
        if ($name == 'tags') {
            return $this->getTags();
        }
        return parent::__get($name);
    }

    public function setAuthorMapper(Application_Model_AuthorMapper $mapper) {
        $this->_authorMapper = $mapper;
    }

    public function setComments(Application_Model_CommentCollection $comments) {
        $this->_data['comments'] = $comments;
    }

    public function getComments() {
        if (!isset($this->_data['comments'])) {
            $this->_commentMapper = new $this->_commentMapperClass;
            $this->_data['comments'] = $this->_commentMapper->findAll($this->id);
        }
        return $this->_data['comments'];
    }

    public function addComment(Application_Model_Comment $comment) {
        $this->getComments()->add($comment);
        //$comment->setEntry($this);
    }

    public function setTags(Application_Model_CommentCollection $tags) {
        $this->_data['tags'] = $tags;
    }

    public function getTags() {
        if (!isset($this->_data['tags'])) {
            $this->_tagMapper = new $this->_tagMapperClass;
            $this->_data['tags'] = $this->_tagMapper->findAll($this->id);
        }
        return $this->_data['tags'];
    }

    public function addTag(Application_Model_Tag $tag) {
        $this->getTag()->add($tag);
        //$comment->setEntry($this);
    }

}
