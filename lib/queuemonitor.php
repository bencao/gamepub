<?php

class QueueMonitor
{
    protected $monSocket = null;

    /**
     * Increment monitoring statistics for a given counter, if configured.
     * Only explicitly listed thread/site/queue owners will be incremented.
     *
     * @param string $key counter name
     * @param array $owners list of owner keys like 'queue:jabber' or 'site:stat01'
     */
    public function stats($key, $owners=array())
    {
        $this->ping(array('counter' => $key,
                          'owners' => $owners));
    }

    /**
     * Send thread state update to the monitoring server, if configured.
     *
     * @param string $thread ID (eg 'generic.1')
     * @param string $state ('init', 'queue', 'shutdown' etc)
     * @param string $substate (optional, eg queue name 'omb' 'sms' etc)
     */
    public function logState($threadId, $state, $substate='')
    {
        $this->ping(array('thread_id' => $threadId,
                          'state' => $state,
                          'substate' => $substate,
                          'ts' => microtime(true)));
    }

    /**
     * General call to the monitoring server
     */
    protected function ping($data)
    {
        $target = common_config('queue', 'monitor');
        if (empty($target)) {
            return;
        }

        $data = $this->prepMonitorData($data);

        if (substr($target, 0, 4) == 'udp:') {
            $this->pingUdp($target, $data);
        } else if (substr($target, 0, 5) == 'http:') {
            $this->pingHttp($target, $data);
        } else {
            common_log(LOG_ERR, __METHOD__ . ' unknown monitor target type ' . $target);
        }
    }

    protected function pingUdp($target, $data)
    {
        if (!$this->monSocket) {
            $this->monSocket = stream_socket_client($target, $errno, $errstr);
        }
        if ($this->monSocket) {
            $post = http_build_query($data, '', '&');
            stream_socket_sendto($this->monSocket, $post);
        } else {
            common_log(LOG_ERR, __METHOD__ . " UDP logging fail: $errstr");
        }
    }

    protected function pingHttp($target, $data)
    {
        $client = new HTTPClient();
        $result = $client->post($target, array(), $data);
        
        if (!$result->isOk()) {
            common_log(LOG_ERR, __METHOD__ . ' HTTP ' . $result->getStatus() .
                                ': ' . $result->getBody());
        }
    }

    protected function prepMonitorData($data)
    {
        #asort($data);
        #$macdata = http_build_query($data, '', '&');
        #$key = 'This is a nice old key';
        #$data['hmac'] = hash_hmac('sha256', $macdata, $key);
        return $data;
    }

}
