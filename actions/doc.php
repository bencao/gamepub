<?php

/**
 * Documentation action.
 *
 * PHP version 5
 *
 * @category Action
 * @package  ShaiShai
 *
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Documentation class.
 *
 * @category Action
 * @package  ShaiShai
 */
class DocAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
    var $filename;
    var $title;
    var $type;

    /**
     * Class handler.
     *
     * @param array $args array of arguments
     *
     * @return nothing
     */
    function handle($args)
    {
        parent::handle($args);
        $this->title    = $this->trimmed('title');
        $this->type     = $this->trimmed('type');
        $this->filename = INSTALLDIR.'/doc-src/'.$this->title;
        if (!file_exists($this->filename)) {
            $this->clientError('此文档不存在');
            return;
        }
        $this->addPassVariable('filename', $this->filename);
        $this->addPassVariable('thistitle', $this->title);
        $this->addPassVariable('thistype', $this->type);
        $this->displayWith('DocHTMLTemplate');
    }

    function isReadOnly($args)
    {
        return true;
    }
}
