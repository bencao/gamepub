<?php
if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for video
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Variables extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'variables';                       // table name
    public $var_name;                        // string(16)  not_null primary_key binary
    public $var_type;                        // string(10)  not_null binary
    public $var_value;                       // string(32)  binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Variables',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getValueByName($name) {
    	$var = Variables::staticGet('var_name', $name);
    	
    	if ($var
    		&& $var->var_value) {
    		$value = $var->var_value;
    		settype($value, $var->var_type);
    	
    		return $value;
    	} else {
    		return false;
    	}
    }
    
    static function updateValueByName($name, $value) {
    	$var = Variables::staticGet('var_name', $name);
    	if ($var) {
    		$orig = clone($var);
    		$var->var_value = '' . $value;
    		$var->update($orig);
    		return true;
    	}
    	return false;
    }
    
	static function increaseIntValueByName($name) {
    	$var = Variables::staticGet('var_name', $name);
    	if ($var
    		&& $var->var_type == 'integer') {
    		$orig = clone($var);
    		$new_value = ((int)$var->var_value) + 1;
    		$var->var_value = '' . $new_value;
    		$var->update($orig);
    		return true;
    	}
    	return false;
    }
}
