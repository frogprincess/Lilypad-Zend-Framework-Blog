<?php
class RssController extends Zend_Controller_Action {
    /**
     * The default action for the rss controller
     * Which shows the rss document
     */
    public function indexAction() {         
        $blog_mapper = new Application_Model_EntryMapper();
        $blogs = $blog_mapper->findAll(10);

        //Create an array for our rss
        $feedData = array();

        //Seting up the head information of the rss
        $feedData['title']      = $this->message_details['blog'] . ' - The Blog';
        $feedData['link']       = $this->baseUrl();
        $feedData['published']     = time(); //Set the published date to now
        $feedData['charset']       = 'utf-8';
        $feedData['language']     = 'en'; 
        $feedData['logo'] = $this->baseUrl() . '/images/logo.png';
        $feedData['entries']       = array();

        //Looping through the news to add them to the 'entries' array.
        foreach($blogs as $blog){
            $entry = array(); //Container for the entry before we add it on
            $entry['title']     = $blog->title; //The title of the news
            $entry['link']         = $this->baseUrl() . $this->view->EntryUrl($blog);
            $entry['description']     = $blog->description; //a brief of the news
            //$entry['content']     = $blog->content; //details of the news
            $entry['lastUpdate']     = $blog->published_date;

            $feedData['entries'][]     = $entry;

        }


        // create our feed object and import the data
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($feedData),'rss');

        //disabling the layout and the rendering
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        //printing the rss feed to standard output
        $feed->saveXML();

        //sending the HTTP headers and output the rss feed
        $feed->send();
        
    }

     /**
     *  Get base url
     *
     * @return string
     */
    public function baseUrl()
    {
        return "http://{$_SERVER['HTTP_HOST']}";
    }

}