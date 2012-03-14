<?php

/**
 * 根据传入得classname，返回一个模板类。
 * @author bencao
 *
 */
class TemplateFactory {
	static function get($class)
	{
		require_once INSTALLDIR . '/templates/' . strtolower($class) . '.php';
		return new $class();
	}
}

?>