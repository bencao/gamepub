<?php
/**
 * Shaishai, the distributed microblog
 *
 * Dummy action that emulates Twitter's rate limit status API resource
 *
 * PHP version 5
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apibareauth.php';

/**
 * We don't have a rate limit, but some clients check this method.
 * It always returns the same thing: 150 hits left.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiAccountRateLimitStatusAction extends ApiBareAuthAction
{

    /**
     * Handle the request
     *
     * Return some Twitter-ish data about API limits
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError(
                'API方法未找到!',
                404,
                $this->format
            );
            return;
        }

        $reset   = new DateTime();
        $reset->modify('+1 hour');

        $this->initDocument($this->format);

         if ($this->format == 'xml') {
             $this->elementStart('hash');
             $this->element('remaining-hits', array('type' => 'integer'), 150);
             $this->element('hourly-limit', array('type' => 'integer'), 150);
             $this->element(
                 'reset-time', array('type' => 'datetime'),
                 common_date_iso8601($reset->format('r'))
             );
             $this->element(
                 'reset_time_in_seconds',
                 array('type' => 'integer'),
                 strtotime('+1 hour')
             );
             $this->elementEnd('hash');
         } elseif ($this->format == 'json') {
             $out = array(
                 'reset_time_in_seconds' => strtotime('+1 hour'),
                 'remaining_hits' => 150,
                 'hourly_limit' => 150,
                 'reset_time' => common_date_rfc2822(
                     $reset->format('r')
                  )
             );
             print json_encode($out);
         }

         $this->endDocument($this->format);
    }

}

