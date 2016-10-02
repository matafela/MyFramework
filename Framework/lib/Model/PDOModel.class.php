<?php
namespace Framework\lib\Model;
use \Framework\lib\LoadConfig as LoadConfig;
class PDOModel extends \PDO{
    private $dsn;
    private $username;
    private $passwd;

    private $table;     //存放table语句（以字符串形式）
    private $where;     //存放where条件（以字符串形式）
    private $limit;     //存放limit语句（以字符串形式）
    private $order;     //存放order语句（以字符串形式）
    private $field;     //存放select语句的field条件（以字符串形式）
    private $join;      //存放join语句（以字符串形式）
    private $data;      //存放用于提交/更新的数据（以键值对形式）

    public function __construct(){
        $host = LoadConfig::get('dataBase','host');
        $dbname = LoadConfig::get('dataBase','dbName');
        $this->username = LoadConfig::get('dataBase','userName');
        $this->passwd = LoadConfig::get('dataBase','password');
        $this->dsn = 'mysql:host='.$host.';dbname='.$dbname;
        try{
            parent::__construct($this->dsn, $this->username, $this->passwd);
        }catch(\PDOException $e){
            preDump($e->getMessage());
        }
    }

    /**
     * @param $_table
     * @return $this
     * @throws \Exception
     */
    public function table($_table){
        unset($this->table);

        if(is_array($_table)){
            $this->table = ' `'.implode('`,`',$_table).'` ';
        }else if(is_string($_table)){
            $this->table = ' `'.$_table.'` ';
        }else{
            throw new \Exception('使用非法参数调用field语句');
        }
        return $this;
    }

    /**
     * 做最简单的数据过滤
     * @param array $_data
     * @return $this
     */
    public function data(Array $_data){
        unset($this->data);//清空之前的数据

        foreach ($_data as $_key => $_value)
            $this->data[$_key] = slashesAdding($_value);
        return $this;   //用于链式调用
    }

    /**
     * @param string $_option
     * @return $this
     */
    public function where($_option){
        unset($this->where);//清空之前的where条件

        $this->where=' WHERE '.$_option;
        return $this;   //用于链式调用
    }

    /**
     * @param mixed $_option 字符串原生语句或者数组传值
     * @return mixed $this
     */
    public function field($_option){
        unset($this->field);//清空之前的field条件

        if(is_array($_option)){
            $this->field = implode(',',$_option);
        }else if(is_string($_option)){
            $this->field=$_option;
        }else{
            throw new\Exception('使用非法参数调用field语句');
        }

        return $this;   //用于链式调用
    }

    /**
     * @param $begin
     * @param null $num
     * @return $this
     */
    public function limit($begin,$num=null){
        unset($this->limit);//清空之前的limit条件
        if(is_numeric($begin))
            $this->limit=' LIMIT '.$begin;
        if(is_numeric($num))
            $this->limit.=",$num";
        return $this;   //用于链式调用
    }

    /**
     * @param array $_option
     * @return $this
     */
    public function order($_option){
        unset($this->order);//清空之前的where条件

        $this->order=' ORDER BY '.$_option;
        return $this;   //用于链式调用
    }

    /**
     * 尚待改进
     * 还要区分 inner join、right join、left join、full join 等
     * 需要支持数组调用来完成
     * array(
     *      array=(
     *          数据表名字符串，
     *          条件名字符串
     *      )，
     *      array=(
     *          数据表明字符串，
     *          条件名字符串
     *      )
     *      。。。
     * )
     * @param mixed $_option
     * @return $this
     */
    public function join($_option){
        unset($this->join);//清空之前的where条件
        $this->join='';
        if(is_array($_option)){
            foreach($_option as $value)
                $this->join.=' JOIN '.$value[0].' ON '.$value[1];
        }else if(is_string($_option)){
            $this->join=' JOIN '.$_option;
        }
        return $this;   //用于链式调用
    }

    /**
     * 注：该方法必须在data方法执行后执行
     * @param $_tables :表名
     * @return bool
     */
    public function insert(){
        //data为空则返回false
        if($this->data==null) return false;

        $_field=$_valueField=$_values=" ";
        foreach ($this->data as $key => $value){
            $_field.="$key,";
            $_valueField.=":$key,";
        }
        $_field =substr($_field, 0, -1);
        $_valueField =substr($_valueField, 0, -1);
        $instruct = "INSERT INTO $this->table ($_field) VALUES($_valueField)";

        $re=$this->prepare($instruct);
        foreach ($this->data as $_key => $_value)
            $re->bindValue(":$_key",$_value);


        //插入完毕清空data
        unset($this->data);

        return $re->execute();
    }

    /**
     * 注：该方法必须在data和where方法执行后执行
     * @param $_tables
     * @return bool
     */
    function update(){
        //data或者where为空则返回false
        if($this->data==null||$this->where==null||$this->table==null) return false;

        $_set='';
        foreach ($this->data as $key => $value)
            $_set.="$key".' = '.":$key,";

        $_set =substr($_set, 0, -1);

        $instruct="UPDATE $this->table SET $_set $this->where";

        $re=$this->prepare($instruct);

        foreach ($this->data as $_key => $_value)
            $re->bindValue(":$_key",$_value);

        //更新完毕清空
        unset($this->data);
        unset($this->where);

        return $re->execute();
    }

    /**
     * @param $_table
     * @param string $method
     * @return array|mixed|\PDOStatement
     */
    function select($method="all"){
        if(!isset($this->table)) return false;
        if(isset($this->field))
            $field=$this->field;
        else
            $field='*';
        $instruct = "SELECT $field FROM $this->table ";

        if(isset($this->join))
            $instruct.=$this->join;
        if(isset($this->where))
            $instruct.=$this->where;
        if(isset($this->order))
            $instruct.=$this->order;
        if(isset($this->limit))
            $instruct.=$this->limit;

        $record=$this->query($instruct);

        preDump($instruct);
        if($method==="one")
            $record=$record->fetch(\PDO::FETCH_ASSOC);
        else if($method==="all")
            $record=$record->fetchAll(\PDO::FETCH_ASSOC);

        //select完毕清空
        unset($this->field);
        unset($this->where);
        unset($this->join);
        unset($this->limit);
        unset($this->order);

        return $record;
    }

     public function delete($_table){
         if($this->where==null)
             return false;
         $instruct = 'DELETE '.$this->table.' FROM '.$this->where;
         $re = $this->prepare($instruct);
         return $re->execute();
     }

     public function createView(){

    }

    public function dropView(){

    }
}