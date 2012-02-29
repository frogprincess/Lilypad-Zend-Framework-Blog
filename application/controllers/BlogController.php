<?php

class BlogController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->_layout->setLayout('layout');
        $this->view->bodyId = 'blog';
		$this->message_details = Zend_Registry::get('config.message');

        $this->description = array(

        );

        $this->keywords = array(

        );

        $tablegateway = new Zend_Db_Table('entries');
        $this->blog_mapper = new Application_Model_EntryMapper($tablegateway);
        $this->view->blog_archive = $this->blog_mapper->findAllGroupByMonth();
        // Add the tag cloud
        $this->view->cloud = $this->_getCloud();
        $this->_getSubscriberForm();

        //$this->view->headMeta()->appendName('description', implode(",", $this->description));
        
    }

    public function indexAction() {
        // action body
        $page = $this->_getParam('page');
        $this->view->bodyClass = 'tab1';
        $this->view->heading = $this->message_details['blog'];
        $this->view->title = $this->view->heading . ' | ';

        $this->view->blogs = $this->blog_mapper->findAll(false);

        $paginator = Zend_Paginator::factory($this->view->blogs);
        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;

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
        $page = $this->_getParam('page');
        $this->_helper->viewRenderer('index');
        $this->view->bodyClass = 'tab1';
        $this->view->heading = $this->message_details['blog'];

        $tag = preg_replace('/_+/', ' ', $this->_getParam('tag'));
        $this->view->tag = $tag;

        $this->view->title = $this->view->heading . ' | ' . $tag . ' | ';

        $this->view->blogs = $this->blog_mapper->findByTag($tag);

        $paginator = Zend_Paginator::factory($this->view->blogs);
        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;

        $this->keywords[] = $tag;

        $this->keywords = array_unique($this->keywords);
        $this->view->headMeta()->appendName('keywords', implode(",", $this->keywords));
    }

     public function viewAction() {
        // action body
        $this->view->bodyClass = 'tab1';
        $this->view->heading = $this->message_details['blog'];
        $this->view->title = $this->view->heading . ' | ';

        $id = $this->_getParam('id');

        $this->view->blog = $this->blog_mapper->find($id);
        if (count($this->view->blog) == 0) {
             // ID invalid so forward user to Index
             $this->_forward('index', 'blog');
        }

        $this->_getCommentsForm($id);

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

        $this->view->blogs = $this->blog_mapper->findAll(30);
        
        // Delete the buggers
        $id = $this->_getParam('id');
        $hashid = $this->_getParam('sess');
        if ($hashid == md5($id . 'bugger')){
            $subscriber_mapper = new Application_Model_SubscriberMapper();
            $subscriber_mapper->delete($id);
            $this->view->message = '<i>You have succesfully unsubscribed, you swine!</i>';

            $mail = new Zend_Mail();
			$mail->setFrom($values['email'], $values['username']);
            $mail->addTo($this->message_details['email'], $this->message_details['name']);
            $mail->setSubject('Unsubscription');
            $mail->setBodyText($id);
            $mail->setBodyHtml($id);
            $mail->send();

        } else {
            $this->view->message = '<i>Your unsubscribe url is fake!</i>';
        }

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
            $mail->addTo($this->message_details['email'], $this->message_details['name']);
            $mail->setSubject($this->message_details['commentSubject']);
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

            // Send email notifications
            $hashid = md5($id . 'bugger');
            $unsubscribe = "<a href=\"{$this->message_details['url']}/blog/unsubscribe/id/$id/sess/$hashid\">unsubscribe</a>";
            $mail = new Zend_Mail();
            $mail->setFrom($this->message_details['email'], 'Blogger');
            $mail->addTo($values['subscribeemail']);
            $mail->setSubject($this->message_details['blog']);
            $mail->setBodyText("Thanks for subscribing to my blog. If you change you mind you can $unsubscribe");
            $mail->setBodyHtml("<p>Thanks for subscribing to my blog. If you change you mind you can $unsubscribe</p>");
            $mail->send();

            $mail = new Zend_Mail();
            $mail->setFrom('frogprincess@lilypadstudio.net', 'Frog Princess');
            $mail->addTo($values['subscribeemail']);
            $mail->setSubject('New subscription');
            $mail->setBodyText($values['subscribeemail']);
            $mail->setBodyHtml($values['subscribeemail']);
            $mail->send();

            $this->view->message = "<i>New post notifications will be sent to: {$values['subscribeemail']}</i>";
        }

        // Add the form to the view
        $form->reset();
        $this->view->subscriberform = $form;
    }

}


