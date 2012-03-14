<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/api.php';


class ApiGetTagAction extends ApiAction
{

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        return true;
    }


    function handle($args)
    {
        parent::handle($args);
        $this->showTimeline();
    }

    /**
     * Show the timeline of notices
     *
     * @return void
     */

    function showTimeline()
    {       
        switch($this->format) {
        case 'xml':
            $this->showXmlTag();
            break;
        }
    }
}