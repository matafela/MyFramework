<?php
namespace Framework;
/**
 * Class FrameworkCore 框架核心类
 * @package Framework
 */
class FrameworkCore{
    /**
     * 根据路由类加载控制器和方法
     */
    static function run(){
        $route = new Route();
        $applicationName = $route->application;
        $controllerName = $route->controller;
        $action = $route->action;


        $controllerPath=ROOT.'/'.$applicationName.'/Controller/'.$controllerName.'Controller.class.php';
        if(is_file($controllerPath)){
            include $controllerPath;
            $fullClassName = '\\'.$applicationName.'\\Controller\\'.$controllerName.'Controller';
            $Controller = new $fullClassName;
            //运行
            $Controller->$action();
        }else{
            throw new \Exception('找不到控制器'.$controllerName);
        }
    }

    /**
     * 标准自动加载方法
     * @param $fullClassName：类名必须包含完整名称空间，当前的类的名称空间与文件路径基本一致
     * @return bool
     */
    static function standard_autoload($fullClassName){
        $className = str_replace('\\','/',$fullClassName);
        $filePath = ROOT.'/'.$className.'.class.php';
        if(is_file($filePath)){
            include $filePath;
            return true;
        }else{
            return false;
        }
    }
}