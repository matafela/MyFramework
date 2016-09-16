<?php
namespace Framework\lib\drive\log;
use \Framework\lib\LoadConfig as LoadConfig;
    //存放在文件系统中的日志
class fileDrive{
    public $path;//日志存储位置
    public function __construct(){
        $logPath = LoadConfig::get('log','OPTION');
        $this->path = $logPath['PATH'];
    }

    public function writeLog($message,$file = 'log'){
        /*
         * 1.确认文件存储位置是否存在，不存在则新建位置
         * 2.写入日志
         */
        if(!is_dir($this->path)){
            mkdir($this->path,0777,true);
        }
        $message= date('Y-m-d H:i:s').' '.json_encode($message).PHP_EOL;
        $writeFilePath = $this->path.$file.'_'.date('YmdH').'.log';

        return file_put_contents($writeFilePath,$message,FILE_APPEND);
    }
}