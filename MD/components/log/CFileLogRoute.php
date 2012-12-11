<?php

class CFileLogRoute extends AbstractLogRoute {

	private $_maxFileSize=2048; // in KB

	private $_maxLogFiles=10;

	public $logPath;

	public $logFile='application.log';

	public function init() {
		parent::init();
		if($this->getLogPath()===null){
			$this->setLogPath(MD::app()->params['logPath']);
		}
	}

	public function getLogPath() {
		return $this->logPath;
	}

	public function setLogPath($value) {
		$this->logPath=MD::getRealPath($value,true);
		if(!is_dir($this->logPath) || !is_writable($this->logPath)){
			throw new CException('CFileLogRoute log路径设置错误，请确保目录存在且有写权限 '.$this->logPath);
		}
	}


	public function getMaxFileSize() {
		return $this->_maxFileSize;
	}

	public function getMaxLogFiles() {
		return $this->_maxLogFiles;
	}

	protected function processLogs($logs) {
		$logFile=$this->getLogPath().$this->logFile;
		if(@filesize($logFile)>$this->getMaxFileSize()*1024) {
			$this->rotateFiles();
			$fp=fopen($logFile, "w"); //创建文件并修改权限
			fclose($fp);  //关闭指针
			chmod($logFile,0777);		
		}
		$fp=@fopen($logFile,'a');
		@flock($fp,LOCK_EX);
		foreach($logs as $log)
			@fwrite($fp,$this->formatLogMessage($log[0],$log[1],$log[2],$log[3]));
		@flock($fp,LOCK_UN);
		@fclose($fp);
	}

	protected function rotateFiles() {
		$file=$this->getLogPath().$this->logFile;
		$max=$this->getMaxLogFiles();
		for($i=$max;$i>0;--$i)
		{
			$rotateFile=$file.'.'.$i;
			if(is_file($rotateFile))
			{
				// suppress errors because it's possible multiple processes enter into this section
				if($i===$max)
					@unlink($rotateFile);
				else
					@rename($rotateFile,$file.'.'.($i+1));
			}
		}
		if(is_file($file))
			@rename($file,$file.'.1'); // suppress errors because it's possible multiple processes enter into this section
	}
	
    protected function formatLogMessage($message,$level,$category,$time)
    {  
        return @date('Y-m-d H:i:s',$time)." [$level] [$category] $message\n";
    }
}
