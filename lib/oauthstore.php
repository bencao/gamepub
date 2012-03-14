<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

//require_once(INSTALLDIR.'/lib/omb.php');

class LShaiOAuthDataStore extends OAuthDataStore
{

    // We keep a record of who's contacted us

    function lookup_consumer($consumer_key)
    {
        $con = Consumer::staticGet('consumer_key', $consumer_key);
        if (!$con) {
            $con = new Consumer();
            $con->consumer_key = $consumer_key;
            $con->seed = common_good_rand(16);
            $con->created = DB_DataObject_Cast::dateTime();
            if (!$con->insert()) {
                return null;
            }
        }
        return new OAuthConsumer($con->consumer_key, '');
    }

    function lookup_token($consumer, $token_type, $token_key)
    {
        $t = new Token();
        $t->consumer_key = $consumer->key;
        $t->tok = $token_key;
        $t->type = ($token_type == 'access') ? 1 : 0;
        if ($t->find(true)) {
            return new OAuthToken($t->tok, $t->secret);
        } else {
            return null;
        }
    }

    // http://oauth.net/core/1.0/#nonce
    // "The Consumer SHALL then generate a Nonce value that is unique for
    // all requests with that timestamp."

    // XXX: It's not clear why the token is here

    function lookup_nonce($consumer, $token, $nonce, $timestamp)
    {
        $n = new Nonce();
        $n->consumer_key = $consumer->key;
        $n->ts = $timestamp;
        $n->nonce = $nonce;
        if ($n->find(true)) {
            return true;
        } else {
            $n->created = DB_DataObject_Cast::dateTime();
            $n->insert();
            return false;
        }
    }

    function new_request_token($consumer)
    {
        $t = new Token();
        $t->consumer_key = $consumer->key;
        $t->tok = common_good_rand(16);
        $t->secret = common_good_rand(16);
        $t->type = 0; // request
        $t->state = 0; // unauthorized
        $t->created = DB_DataObject_Cast::dateTime();
        if (!$t->insert()) {
            return null;
        } else {
            return new OAuthToken($t->tok, $t->secret);
        }
    }

    // defined in OAuthDataStore, but not implemented anywhere

    function fetch_request_token($consumer)
    {
        return $this->new_request_token($consumer);
    }

    function new_access_token($token, $consumer)
    {
        $rt = new Token();
        $rt->consumer_key = $consumer->key;
        $rt->tok = $token->key;
        $rt->type = 0; // request
        if ($rt->find(true) && $rt->state == 1) { // authorized
            $at = new Token();
            $at->consumer_key = $consumer->key;
            $at->tok = common_good_rand(16);
            $at->secret = common_good_rand(16);
            $at->type = 1; // access
            $at->created = DB_DataObject_Cast::dateTime();
            if (!$at->insert()) {
                $e = $at->_lastError;
                return null;
            } else {
                // burn the old one
                $orig_rt = clone($rt);
                $rt->state = 2; // used
                if (!$rt->update($orig_rt)) {
                    return null;
                }
                // Update subscription
                // XXX: mixing levels here
                $sub = Subscription::staticGet('token', $rt->tok);
                if (!$sub) {
                    return null;
                }
                $orig_sub = clone($sub);
                $sub->token = $at->tok;
                $sub->secret = $at->secret;
                if (!$sub->update($orig_sub)) {
                    return null;
                } else {
                    return new OAuthToken($at->tok, $at->secret);
                }
            }
        } else {
            return null;
        }
    }

    // defined in OAuthDataStore, but not implemented anywhere

    function fetch_access_token($consumer)
    {
        return $this->new_access_token($consumer);
    }
}
