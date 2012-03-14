<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Generator for in-memory XML
 *
 * @category  Output
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Create in-memory XML
 *
 * @category Output
 * @package  LShai
 * @see      Action
 * @see      HTMLOutputter
 */

class XMLStringer extends XMLOutputter
{
    function __construct($indent=false)
    {
        $this->xw = new XMLWriter();
        $this->xw->openMemory();
        $this->xw->setIndent($indent);
    }
    
	function endXML()
    {
        $this->xw->endDocument();
//        $this->xw->flush();
    }

    function getString()
    {
        return $this->xw->outputMemory();
    }

    // utility for quickly creating XML-strings

    static function estring($tag, $attrs=null, $content=null)
    {
        $xs = new XMLStringer();
        $xs->element($tag, $attrs, $content);
        return $xs->getString();
    }
}