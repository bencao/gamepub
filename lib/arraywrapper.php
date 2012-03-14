<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class ArrayWrapper
{
    var $_items = null;
    var $_count = 0;
    var $_i = -1;
    var $N = 0;

    function __construct($items)
    {
        $this->_items = $items;
        $this->_count = count($this->_items);
        $this->N = $this->_count;
    }

    function fetch()
    {
        if (!$this->_items) {
            return false;
        }
        $this->_i++;
        if ($this->_i < $this->_count) {
            return true;
        } else {
            return false;
        }
    }

    function __set($name, $value)
    {
        $item =& $this->_items[$this->_i];
        $item->$name = $value;
        return $item->$name;
    }

    function __get($name)
    {
        $item =& $this->_items[$this->_i];
        return $item->$name;
    }

    function __isset($name)
    {
        $item =& $this->_items[$this->_i];
        return isset($item->$name);
    }

    function __unset($name)
    {
        $item =& $this->_items[$this->_i];
        unset($item->$name);
    }

    function __call($name, $args)
    {
        $item =& $this->_items[$this->_i];
        return call_user_func_array(array($item, $name), $args);
    }
}