<?php

class BatchInvite {
	
	/**
	 * current support 126,163,sina,sohu,tom,yeah
	 * return 0 if fail , return array(array(name, email), ...) if success.
	 * @param unknown_type $source
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @return unknown_type
	 */
	static function importFrom($source, $username, $password)
	{
		require_once 'class.' . $source . ".php";
		$className = 'Http' . $source;
		$clazz = new $className();
		return $clazz->getAddressList($username, $password);
	}
	
	static function getQQReturnHTML($username, $password)
	{
		require_once 'class.qq.php';
		$className = 'Httpqq';
		$clazz = new $className();
		return $clazz->makeForm($username, $password);
	}
}