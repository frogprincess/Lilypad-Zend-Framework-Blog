<?php
abstract class Application_Model_Collection implements Iterator {

    protected $mapper;
    public $total = 0;
    protected $raw = array();

    private $result;
    private $pointer = 0;
    private $objects = array();

    public function __construct(array $raw=null, Application_Model_Mapper $mapper=null) {
        if (!is_null($raw) && !is_null($mapper)) { 
            $this->raw = $raw;
            $this->total = count($raw);
        }
        $this->mapper = $mapper;
    }

    public function add(Application_Model_Entity $object) {
        $class = $this->targetClass();
        if (!($object instanceof $class)) {
            throw new Exception("This is a $class collection");
        }
        $this->objects[$this->total] = $object;
        $this->total ++;
    }

    abstract function targetClass();

    private function getRow($num) {
        if ($num >= $this->total || $num < 0) {
            return null;
        }

        if (isset($this->objects[$num])) {
            return $this->objects[$num];
        }

        if (isset($this->raw[$num])) {
            $class = $this->targetClass();
            $this->objects[$num] = new $class($this->raw[$num]);
            return $this->objects[$num];
        }
    }

    public function rewind() {
        $this->pointer = 0;
    }

    public function current() {
        return $this->getRow($this->pointer);
    }

    public function key() {
        return $this->pointer;
    }

    public function next() {
        $row = $this->getRow($this->pointer);
        if ($row) {$this->pointer++;}
        return $row;
    }

    public function valid() {
        return (!is_null($this->current()));
    }
}
