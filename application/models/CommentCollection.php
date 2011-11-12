<?php

class Application_Model_CommentCollection extends Application_Model_Collection {
    public function targetClass() {
        return 'Application_Model_Comment';
    }
}