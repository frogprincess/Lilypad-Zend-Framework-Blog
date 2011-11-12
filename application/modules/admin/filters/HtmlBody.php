<?php
class Admin_Filter_HtmlBody extends Admin_Filter_HTMLPurifier {

    public function __construct($newOptions = null) {
        $options = array(
            array('Cache.SerializerPath',
                APPLICATION_PATH . '/../cache/htmlpurifier'
                ),
            array('HTML.Doctype', 'XHTML 1.0 Strict'),
            array('HTML.Allowed',
                'p,em,h1,h2,h3,h4,h5,strong,a[href],ul,ol,li,code,pre,'
                .'blockquote,img[src|alt|height|width],sub,sup'
                ),
            array('AutoFormat.Linkify', 'true'),
            array('AutoFormat.AutoParagraph', 'true')
            );

        if (!is_null($newOptions)) {
            // I'll let HTMLPurifier overwrite original options
            // with new ones rather than filter them myself
            $options = array_merge($options, $newOptions);
        }

        parent::__construct($options);
    }

}