<?php

return array(
    Zend_Validate_NotEmpty::IS_EMPTY => 'Required',
    Zend_Validate_StringLength::TOO_SHORT => 'Minimum Length of %min%',
    Zend_Validate_StringLength::TOO_LONG => 'Maximum Length of %max%',
    //Zend_Validate_Date::NOT_YYYY_MM_DD => 'Must use YYYY-MM-DD format',
    Zend_Validate_Date::INVALID => 'Not valid date',
    Zend_Validate_Date::FALSEFORMAT => 'Invalid date format',
    
    Zend_Validate_EmailAddress::INVALID            => 'Invalid email address',
    Zend_Validate_EmailAddress::INVALID_FORMAT     => 'Invalid email address',
    Zend_Validate_EmailAddress::INVALID_HOSTNAME   => 'Invalid email address',
    Zend_Validate_EmailAddress::INVALID_MX_RECORD  => 'Invalid email address',
    Zend_Validate_EmailAddress::INVALID_SEGMENT    => 'Invalid email address',
    Zend_Validate_EmailAddress::DOT_ATOM           => 'Invalid email address',
    Zend_Validate_EmailAddress::QUOTED_STRING      => 'Invalid email address',
    Zend_Validate_EmailAddress::INVALID_LOCAL_PART => 'Invalid email address',
    Zend_Validate_EmailAddress::LENGTH_EXCEEDED    => 'Invalid email address',

    
);