<?php

define('ROOT',realpath('./'));      //框架根目录，目前与index.php同级
define('DEBUG',true);               //定义是否开启DEBUG模式

include "vendor/autoload.php";

if(DEBUG){

    $whoops = new \Whoops\Run;
    $errorTitle = "框架出错了";
    $option = new \Whoops\Handler\PrettyPageHandler();
    $option->setPageTitle($errorTitle);
    $whoops->pushHandler($option);
    $whoops->register();

    ini_set('display_errors','On');//对用户发送错误信息
}else{
    ini_set('display_errors','Off');
}

dump($_SERVER);
exit;

include 'Framework/function.php';
include 'Framework/FrameworkCore.class.php';
//装载自动加载类
spl_autoload_register('\Framework\FrameworkCore::standard_autoload');

//运行
\Framework\FrameworkCore::run();
