<?php
namespace Framework\lib;
use \Framework\lib\LoadConfig as LoadConfig;
/**
 * 1.确定日志存储方式
 *
 * 2.写日志
 */
class Log{
    static $driveClass;
    static function init(){
        //确定存储方式
        $driveClassName = LoadConfig::get('log','DRIVE');
        $fullDriveClassName = '\Framework\lib\drive\log\\'.$driveClassName.'Drive';
        self::$driveClass = new $fullDriveClassName;
    }
    static public function writeLog($message,$file='log'){
        return self::$driveClass->writeLog($message,$file);
    }
}