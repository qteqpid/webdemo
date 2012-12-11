<?php

/**
 *  将性能log发送到远程log服务器
 * @author qteqpid
 */
class CRemoteLogRoute extends AbstractLogRoute {
	
	protected $host;
	protected $port;
	
	public function processLogs($logs) {
		$client = new SocketClient($this->host, $this->port);
		if ($client->connect()) {
			foreach ($logs as $log) {
				$client->send($this->formatLogMessage($log[0],$log[1],$log[2],$log[3]));
			}
			$client->close();
		} else {
			MD::log("Socket Client can NOT connect to {$this->host}:{$this->port}",CLogger::LEVEL_ERROR, 'socket');
		}
	}
	
    protected function formatLogMessage($message,$level,$category,$time) {  
        $log = @date('Y-m-d H:i:s',$time)." [$category] s=".MD::app()->ip." $message";
        return "$level#$log";
    }
}