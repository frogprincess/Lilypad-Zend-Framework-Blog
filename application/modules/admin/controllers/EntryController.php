<?php
class Admin_EntryController extends Zend_Controller_Action {

    public function init() {
        $this->view->headScript()->appendFile('/js/tinymce/jscripts/tiny_mce/tiny_mce.js');
        $this->view->headScript()->appendFile('/js/tinymce_config.js');
    }

    public function addAction() {        
        $form = new Admin_Form_EntryAdd;

        if (!$this->getRequest()->isPost()) {
            $this->view->entryForm = $form;
            return;
        } elseif (!$form->isValid($_POST)) {
            $this->view->failedValidation = true;
            $this->view->entryForm = $form;
            return;
        }

        $values = $form->getValues();
        $author_mapper = new Application_Model_AuthorMapper();
        $author = $author_mapper->find(Zend_Auth::getInstance()->getIdentity()->username);
        $data = array(
            'title' => $values['title'],
            'published_date' => strtotime($values['date']),
            'last_modified' => strtotime($values['date']),
            'author' => $author,
            'content' => $values['body'],
            'extended_content' => $values['extended_body'],
            'hide' => $values['hide'],
            'description' => $values['description']
        );
        $entry = new Application_Model_Entry($data);
        $entry_mapper = new Application_Model_EntryMapper();
        $eid = $entry_mapper->save($entry);
        $this->view->entrySaved = true;

        $tag_string = strtolower($values['tags']);
        $tag_array = explode(',', $tag_string);
        $tag_array = array_map('trim', $tag_array);
        $tag_array = array_unique($tag_array);

        foreach ($tag_array as $tag) {
            $tag_obj = new Application_Model_Tag(array('tag' => $tag, 'entry' => $eid));
            $tag_mapper = new Application_Model_TagMapper();
            $tag_mapper->save($tag_obj);
        }

        // email subscribers
        $this->_emailSubscribers($entry);
    }

    public function listAction() {
        $entry_mapper = new Application_Model_EntryMapper();
        $this->view->entries = $entry_mapper->findAll(false);
    }

    public function editAction() {
        if (!$this->_getParam('id')) {
            throw new Exception('No entry id given or url is wrong');
        }

        $form = new Admin_Form_EntryEdit;
        if (!$this->getRequest()->isPost()) {
            $entry_mapper = new Application_Model_EntryMapper();
            $this->view->entry = $entry_mapper->find($this->_getParam('id'));
            $this->view->entry->getTags();
            if (!$this->view->entry) {
                $this->view->failedFind = true;
                return;
            }
            $form->setElementFilters(array()); // disable all element filters
            $this->_repopulateForm($form, $this->view->entry);
            $this->view->entryForm = $form;
            return;
        } elseif (!$form->isValid($_POST)) {
            $this->view->failedValidation = true;
            $this->view->entryForm = $form;
            return;
        }

        $values = $form->getValues();
        $data = array(
            'id' => $values['id'],
            'title' => $values['title'],
            'last_modified' => strtotime($values['date']),
            'content' => $values['body'],
            'extended_content' => $values['extended_body'],
            'hide' => $values['hide'],
            'description' => $values['description']
            );
        $entry = new Application_Model_Entry($data);
        $entry->getTags();
        $entry_mapper = new Application_Model_EntryMapper();
        $entry_mapper->save($entry);
        $this->view->entrySaved = true;
        $this->view->entry = $entry;

        $tag_string = strtolower($values['tags']);
        $tag_array = explode(',', $tag_string);
        $tag_array = array_map('trim', $tag_array);
        $tag_collection = $entry->tags; 
        $tag_array = array_unique($tag_array);
        $tag_mapper = new Application_Model_TagMapper();

        // add new tags        
        foreach ($tag_array as $tag) {
            $add = true;
            // if the tag does not already exist
            foreach ($tag_collection as $obj) { 
                if($obj->tag == $tag) {
                   $add = false;
                }
            }
            if($add) { 
                $tag_obj = new Application_Model_Tag(array('tag' => $tag, 'entry' => $data['id']));
                $tag_mapper->save($tag_obj);
            }
        }

        // delete removed tags        
        foreach ($tag_collection as $obj) {
            $delete = true;
            // if the tag does exist but is not in the array from the form
            foreach ($tag_array as $tag) {
                if($obj->tag == $tag) {
                    $delete = false;
                }                
            }
            if($delete) $tag_mapper->delete($obj);
        }

    }

    public function hideAction() {
        $entry_mapper = new Application_Model_EntryMapper();
        $entry = $entry_mapper->find($this->_getParam('id'));
        $entry->hide = 1;
        $entry_mapper->save($entry);
        return $this->_response->setRedirect('/admin/entry/list');
    }

    public function showAction() {
        $entry_mapper = new Application_Model_EntryMapper();
        $entry = $entry_mapper->find($this->_getParam('id'));
        $entry->hide = 0;
        $entry_mapper->save($entry);
        return $this->_response->setRedirect('/admin/entry/list');
    }

    protected function _repopulateForm($form, $entry) {
        $values = array(
            'title' => $this->_reverseAutoFormat($entry->title),
            'last_modified' => $entry->date,
            'body' => $this->_reverseAutoFormat($entry->content),
            'extended_body' => $this->_reverseAutoFormat($entry->extended_content),
            'hide' => $entry->hide,
            'description' => $entry->description,
            'author' => $entry->author->username,
            'id' => $this->_getParam('id'),
            'tags' => $this->_formatTagString($entry->tags),
            );
        $form->populate($values);
    }

    protected function _reverseAutoFormat($string) {
//        //$string = preg_replace("/\ /", '', $string);
//        $string = preg_replace("/\<p\>/", '', $string);
//        $string = preg_replace("/\<\/p\>/", "\n\n", $string);
//        $string = preg_replace("/\]*\>/", '', $string);
//        $string = preg_replace("/\<a\>/", '', $string);
//        $string = preg_replace("/\<\/a\>/", '', $string);
//        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        return $string;
    }

    protected function _addTags(){}

    protected function _formatTagString ($tags) {
        return implode(', ', $this->_getTagArray($tags));
    }

    protected function _getTagArray ($tags) {
        foreach ($tags as $tag) {
            $tag_array[] = $tag->tag;
        }
        return $tag_array;
    }

    protected function _emailSubscribers ($entry) {
        $subscriber_mapper = new Application_Model_SubscriberMapper();
        $subscribers = $subscriber_mapper->findAll();
        $message_details = Zend_Registry::get('config.message');

        foreach ($subscribers as $subscriber) {
            // Send email notification
            $hashid = md5($id . 'bugger');
            $unsubscribe = "<a href=\"{$message_details['url']}/blog/unsubscribe/id/{$subscriber->id}/sess/$hashid\">unsubscribe</a>";
            $url = "<a href=\"{$message_details['url']}{$this->view->EntryUrl($entry)}\">Read it</a>";

            $mail = new Zend_Mail();
            $mail->setFrom($message_details['email'], $message_details['name']);
            $mail->addTo($subscriber->email, '');
            $mail->setSubject('Zend Framework Blog');
            $mail->setBodyText("[New post] $entry->title  $url \n $unsubscribe");
            $mail->setBodyHtml("<p>[New post] $entry->title $url</p><p>$unsubscribe</p>");
            $mail->send();
        }
    }


}