<?
namespace Framework\lib;
class LoadConfig{
    static public $conf = array();//用于缓存当前所有配置信息，第一维键值是配置文件名，第二维键值是配置项名

    /**
     * @param $fileName ：配置文件名
     * @param null $name :配置项名
     * @return mixed
     * @throws \Exception
     */
    static public function get($fileName, $name=NULL)
    {
        // 判断配置是否已缓存
        if(isset(self::$conf[$fileName])){
            $config = self::$conf[$fileName];//取该文件配置项
        }
        else {
            // 判断配置文件是否存在
            $filePath = ROOT.'/Framework/config/'.$fileName.'.php';
            if(!is_file($filePath)) {
                throw new \Exception('没有配置文件'.$filePath);
            }
            $config = include $filePath;//加载该文件的配置
            // 缓存配置
            self::$conf[$fileName] = $config;
        }
        // 如果$name没有设置则返回所有的设置
        if ($name === NULL) {
            return $config;
        }
        // 判断配置项是否存在
        if(!isset($config[$name])) {
            throw new \Exception('没有配置项'.$name);
        }
        // 返回配置
        return $config[$name];
    }
}