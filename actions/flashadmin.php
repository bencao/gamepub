<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

/**
 * Admin flash
 * @category Administrator
 * @author   AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @package  ShaiShai
 */
class FlashadminAction extends ShaiAction
{
	var $msg;

	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}

		if ($this->cur_user->uname != 'gamepub') {
			$this->clientError('您没有权限访问此链接！');
			return false;
		}

		return true;
	}

	function handle($args)
	{
		parent::handle($args);
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->displayWith('FlashAdminHTMLTemplate');
		} else {
			$this->handleFlashPic();
			$this->handleFlashDel();
		}
	}

	function handleFlashPic()
	{
		$update_flash_id = $this->trimmed('flashid');
		if (!$update_flash_id) {
			return;
		}
		if (! ($flash = Flash::staticGet('id', $update_flash_id))) {
			$this->msg = '要更新的Flash不存在！';
			$this->addPassVariable('msg', $this->msg);
			$this->displayWith('FlashAdminHTMLTemplate');
			return false;
		}

		$server_site = common_config('site', 'server');
		$picfilepath = $this->trimmed('picfilename'); //ie. http://www.gamepub.cn/tmp/file/10/00/66/new.jpg
		$oripicpath = $flash->picpath;				  //ie. http://www.gamepub.cn/file/10/00/01/flash/ori.jpg
		$targetPath = substr($oripicpath, strpos($oripicpath, $server_site) + strlen($server_site) + 1);
		$fromPath = substr($picfilepath, strpos($picfilepath, $server_site) + strlen($server_site) + 1);

		if (file_exists($fromPath)) {
			@unlink($targetPath);
			rename($fromPath, $targetPath);
		}
			
		$this->msg = '小游戏截图更新成功';
		$this->addPassVariable('msg', $this->msg);
		$this->addPassVariable('flashid', false);
		$this->addPassVariable('picfilename', false);
		$this->displayWith('FlashAdminHTMLTemplate');
	}

	function handleFlashDel()
	{
		$delete_flash_id = $this->trimmed('flashdelid');
		if (!$delete_flash_id) {
			return;
		}
		if (! ($flash = Flash::staticGet('id', $delete_flash_id))) {
			$this->msg = '要删除的Flash不存在！';
			$this->addPassVariable('msg', $this->msg);
			$this->displayWith('FlashAdminHTMLTemplate');
			return false;
		}
			
		$server_site = common_config('site', 'server');
		$oriflashpath = $flash->path;	//ie. http://www.gamepub.cn/file/10/00/01/flash/ori.swf
		$delflashPath = substr($oriflashpath, strpos($oriflashpath, $server_site) + strlen($server_site) + 1);
		@unlink($delflashPath);
		$flash->delete();
			
		$this->msg = '小游戏成功删除';
		$this->addPassVariable('msg', $this->msg);
		$this->addPassVariable('flashdelid', false);
		$this->displayWith('FlashAdminHTMLTemplate');
	}
}