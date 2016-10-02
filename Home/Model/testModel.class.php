<?php
namespace Home\Model;
use Framework\lib\Model as coreModel;
class testModel extends coreModel\PDOModel {
    public function re($table){
        return $this->limit(1,3)->select($table,'all');
    }
}