<?php
/**
 * 自定义输出变量
 * @param $var：想要输出的变量
 * @return string
 */
function preDump($var){
    if (is_bool($var)){
        var_dump($var);
    }else if(is_null($var)){
        var_dump(null);
    }else{
        echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>".print_r($var,true)."</pre>";
    }
}

//用于过滤非法参数
function slashesAdding($string){
    //get_magic_quotes_gpc()这函数5.4以后默认都是关闭的，返回false
    return (!get_magic_quotes_gpc()?addslashes($string):$string);//看是否开启
}
