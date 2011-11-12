<?php
class Admin_Filter_StripSlashes implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return get_magic_quotes_gpc() ? $this->_clean($value) : $value;
    }

    protected function _clean($value)
    {
        return is_array($value) ? array_map(array($this, '_clean'), $value) : stripslashes($value);
    }
}

?>
