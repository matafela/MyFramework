<?php
namespace Home\Controller;
use Framework\lib as core;
use Home\Model as Model;
/**
 * Class indexController 默认控制器
 * @package Home\Controller
 */
class indexController{
    /**
     * 默认方法
     */
    public function index(){
        $Model = new Model\testModel();
        $Model->data(array(
            'name'          =>'sdddd',
            'content'       =>'asdffffasdfafasdf'
        ))
            ->table('table_1')
            ->where('id=1')
            ->update();
    }


}