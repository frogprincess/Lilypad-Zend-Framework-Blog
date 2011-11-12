<?php

class BlogController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->_layout->setLayout('layout');
        $this->view->bodyId = 'blog';

        $this->description = array(

        );

        $this->keywords = array(

        );

        
    }

    public function indexAction() {
        // action body
        $this->view->bodyClass = 'tab1';
        $this->view->heading = 'Zend Framework Blog';
        $this->view->title = $this->view->heading . ' | ';

        $tablegateway = new Zend_Db_Table('entries');
        $blog_mapper = new Application_Model_EntryMapper($tablegateway);

        $this->view->blogs = $blog_mapper->findAll(30);

        $this->_getSubscriberForm();

        // Add the tag cloud
        $this->view->cloud = $this->_getCloud();

        foreach ($this->view->blogs as $blog) {
            foreach ($blog->tags as $tag) {
                $this->keywords[] = $tag->tag;
            }
        }
        $this->keywords = array_unique($this->keywords);
        $this->view->headMeta()->appendName('keywords', implode(",", $this->keywords));

    }

    public function tagAction() {
        // action body
        $this->_helper->viewRenderer('index');
        $this->view->bodyClass = 'tab1';
        $this->view->heading = 'Zend Framework Blog';

        $tag = preg_replace('/_+/', ' ', $this->_getParam('tag'));
        $this->view->tag = $tag;

        $this->view->title = $this->view->heading . ' | ' . $tag . ' | ';

        $tablegateway = new Zend_Db_Table('entries');
        $blog_mapper = new Application_Model_EntryMapper($tablegateway);

        $this->view->blogs = $blog_mapper->findByTag($tag);

        $this->_getSubscriberForm();
        
        // Add the tag cloud
        $this->view->cloud = $this->_getCloud();

        $this->keywords[] = $tag;

        $this->keywords = array_unique($this->keywords);
        $this->view->headMeta()->appendName('keywords', implode(",", $this->keywords));
    }

     public function viewAction() {
        // action body
        $this->view->bodyClass = 'tab1';
        $this->view->heading = 'Zend Framework Blog';
        $this->view->title = $this->view->heading . ' | ';

        $id = $this->_getParam('id');

        $tablegateway = new Zend_Db_Table('entries');
        $blog_mapper = new Application_Model_EntryMapper($tablegateway);       

        $this->view->blog = $blog_mapper->find($id);
        if (count($this->view->blog) == 0) {
             // ID invalid so forward user to Index
             $this->_forward('index', 'blog');
        }

        $this->_getCommentsForm($id);
        $this->_getSubscriberForm();

        // Add the tag cloud
        $this->view->cloud = $this->_getCloud();

        $this->keywords[] = $this->view->blog->title;

        $this->keywords = array_unique($this->keywords);
        $this->view->headMeta()->appendName('keywords', implode(",", $this->keywords));
    }

    public function unsubscribeAction() {
        // action body
        $this->_helper->viewRenderer('index');
        $this->view->bodyClass = 'tab1';
        $this->view->heading = 'Blog';
        $this->view->title = $this->view->heading . ' - ';

        $blog_mapper = new Application_Model_EntryMapper();

        $this->view->blogs = $blog_mapper->findAll(3);

        $this->_getSubscriberForm();
        
        // Delete the buggers
        $id = $this->_getParam('id');
        $hashid = $this->_getParam('sess');
        if ($hashid == md5($id . 'bugger')){
            $subscriber_mapper = new Application_Model_SubscriberMapper();
            $subscriber_mapper->delete($id);
            $this->view->message = '<i>You have succesfully unsubscribed, you swine!</i>';
        } else {
            $this->view->message = '<i>Your unsubscribe url is fake!</i>';
        }
        // Add the tag cloud
        $this->view->cloud = $this->_getCloud();
    }

    protected function _repopulateForm($form, $comment) {
        $values = array(
            'username' => $comment['username'],
            'email' => $comment['email'],
            'url' => $comment['url'],
            'comment' => $this->_reverseAutoFormat($comment['comment']),            
            'entry' => $comment['entry'],
            'published_date' => $comment['published_date']
            );
        $form->populate($values);
    }

    protected function _reverseAutoFormat($string) {
        //$string = preg_replace("/\ /", '', $string);
        $string = preg_replace("/\<p\>/", '', $string);
        $string = preg_replace("/\<\/p\>/", "\n\n", $string);
        $string = preg_replace("/\]*\>/", '', $string);
        $string = preg_replace("/\<a\>/", '', $string);
        $string = preg_replace("/\<\/a\>/", '', $string);
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        return $string;
    }

    protected function _getCloud() {
        $tablegateway = new Zend_Db_Table('tags');
        $tag_mapper = new Application_Model_TagMapper($tablegateway);
        $tags = $tag_mapper->findGrouped();
        $tags_array = array();

        foreach ($tags as $tag) {            
            $url = $this->view->tagUrl($tag['tag']);
            $tags_array[] = array(
                'title' => $tag['tag'],
                'weight' => $tag['num'],
                'params' => array (
                    'url' => $url
                )
            );
        }

        $cloud = new Zend_Tag_Cloud(array(
            'tags' => $tags_array,
            'cloudDecorator' => array(
                'decorator' => 'HtmlCloud',
                'options' => array (
                    'htmlTags' => array (
                        'div' => array ('id' => 'tags')
                        ),
                    'separator' => '&nbsp;&nbsp;&nbsp;'
                    )
                ),
            'tagDecorator' => array (
                'decorator' => 'HtmlTag',
                'options' => array (
                    'htmlTags' => array (
                        'span' => array('class' => 'cloud')
                        ),
                    'classList' => array('tag1', 'tag2', 'tag3', 'tag4',)
                    )
                )));

        return $cloud;
    }

    protected function _getCommentsForm ($id) {
        // Get the comment form        
        $form = new Application_Form_CommentAdd(array('entry'=>$id));
        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
            return;
        }

        $message_details = Zend_Registry::get('config.message');

        if ($_POST['submit'] == 'Comment') {
            if (!$form->isValid($_POST)) {
                $this->view->errors = $form->getMessages();
                $form->setElementFilters(array()); // disable all element filters
                $this->_repopulateForm($form, $form->getValues());
                $this->view->form = $form;
                return;
            }

            $values = $form->getValues();
            $data = array(
                'username' => $values['username'],
                'email' => $values['email'],
                'url' => $values['url'],
                'comment' => $values['comment'],
                'published_date' => $values['published_date'],
                'entry' => $values['entry']
            );
            $comment = new Application_Model_Comment($data);
            $comment_mapper = new Application_Model_CommentMapper();
            $comment_mapper->save($comment);
            $this->view->blog->addComment($comment);

            // Send email notification
            $mail = new Zend_Mail();
            $mail->setFrom($values['email'], $values['username']);
            $mail->addTo($message_details['email'], $message_details['name']);
            $mail->setSubject('Message from Zend Framework Blog Comment Form');
            $mail->setBodyText("{$values['comment']}");
            $mail->setBodyHtml("<p>{$values['comment']}</p>");
            $mail->send();
        }

        // Add the form to the view
        $form->reset();
        $this->view->form = $form;

    }

    protected function _getSubscriberForm () {
        $form = new Application_Form_SubscriberAdd();
        if (!$this->getRequest()->isPost()) {
            $this->view->subscriberform = $form;
            return;
        }

        $message_details = Zend_Registry::get('config.message');

        if ($_POST['submit'] == 'Subscribe') {
            if (!$form->isValid($_POST)) {
                $this->view->errors = $form->getMessages();
                $this->view->subscriberform = $form;
                return;
            }

            $values = $form->getValues();
            $data = array(
                'email' => $values['subscribeemail'],
            );

            $subscriber_mapper = new Application_Model_SubscriberMapper();

            if(!$subscriber = $subscriber_mapper->findByEmail($values['subscribeemail'])) {
                $subscriber = new Application_Model_Subscriber($data);
            }
            $id = $subscriber_mapper->save($subscriber);

            // Send email notification
            $hashid = md5($id . 'bugger');
            $unsubscribe = "<a href=\"{$message_details['url']}/blog/unsubscribe/id/$id/sess/$hashid\">unsubscribe</a>";
            $mail = new Zend_Mail();
            $mail->setFrom($message_details['email'], 'Blogger');
            $mail->addTo($values['subscribeemail']);
            $mail->setSubject('Zend Framework Blog');
            $mail->setBodyText("Thanks for subscribing to my blog. If you change you mind you can $unsubscribe");
            $mail->setBodyHtml("<p>Thanks for subscribing to my blog. If you change you mind you can $unsubscribe</p>");
            $mail->send();

            $this->view->message = "<i>New post notifications will be sent to: {$values['subscribeemail']}</i>";
        }

        // Add the form to the view
        $form->reset();
        $this->view->subscriberform = $form;
    }

}


