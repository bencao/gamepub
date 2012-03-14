<?php
//header("content-type:text/html; charset=utf-8");
//$mysql=mysql_connect("localhost","shaier","shaier")
//or die("mysql服务器连接失败：".mysql_error());
//$db=mysql_select_db("shaishaidb");

/**
 * Shaishai, the distributed microblog
 *
 * API Docment action
 *
 * PHP version 5
 *
 * @category  api doc
 * @package   Shaishai
 * @author    Jiayuan Liu <loyjyf@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
	exit(1);
}

/**
 * API action
 *
 * @category  api doc
 * @package   Shaishai
 * @author    Jiayuan Liu <loyjyf@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApidocAction extends ShaiAction
{

	function handle($args)
	{
		parent::handle($args);
		//调试
		$sname=$this->trimmed('select_name');
//		$sname=$_POST["select_name"];
		if($sname!=null)
		{
			$this->showDetails($sname);
		} else {
			$names=Api::getAllList();
			if (!empty($names)) 
			{
				$this->showList($names);
			} else {
				$this->clientError('此文档不存在');
				return;
			}
		}
	}

	function showList($names)
	{
		$this->addPassVariable('api_name', $names);
		$this->displayWith('ApiDocHTMLTemplate');
	}

	function showDetails($sname)
	{
		$this->addPassVariable('api_name_select', $sname);
		
		$result_api=Api::getApi($sname);		
		$this->addPassVariable('api', $result_api);
		
		$result_api_format=Api::getFormat($sname);
		$this->addPassVariable('format', $result_api_format);
		
		$result_api_parameter=Api::getParameter($sname);
		$this->addPassVariable('parameter', $result_api_parameter);
		
		$result_api_response=Api::getResponse($sname);
		$this->addPassVariable('response', $result_api_response);
		
		$sql_api_usagenote=Api::getUsagenote($sname);
		$this->addPassVariable('usagenote', $sql_api_usagenote);
		
		$this->displayWith('DetailHTMLTemplate');
	}

	function isReadOnly($args)
	{
		return true;
	}
}
