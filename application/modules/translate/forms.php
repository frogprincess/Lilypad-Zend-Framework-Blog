<?php

return array(
    Zend_Validate_NotEmpty::IS_EMPTY => 'Required',
    Zend_Validate_StringLength::TOO_SHORT => 'Minimum Length of %min%',
    Zend_Validate_StringLength::TOO_LONG => 'Maximum Length of %max%',
    //Zend_Validate_Date::NOT_YYYY_MM_DD => 'Must use YYYY-MM-DD format',
    Zend_Validate_Date::INVALID => 'Not valid date',
    Zend_Validate_Date::FALSEFORMAT => 'Invalid date format',
);