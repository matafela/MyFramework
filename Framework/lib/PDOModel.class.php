<?php
namespace Framework\lib;
class PDOModel extends \PDO{
    private $dsn;
    private $username;
    private $passwd;

    private $table;     //存放table语句（以字符串形式）     还没用上
    private $where;     //存放where条件（以字符串形式）
    private $limit;     //存放limit语句（以字符串形式）
    private $order;     //存放order语句（以字符串形式）
    private $field;     //存放field语句（以字符串形式）
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
     * @param array $_option
     * @return $this
     */
    public function where($_option){
        unset($this->where);//清空之前的where条件

        $this->where=$_option;
        return $this;   //用于链式调用
    }

    /**
     * @param array $_option
     * @return $this
     */
    public function field($_option){
        unset($this->field);//清空之前的where条件

        $this->field=$_option;
        return $this;   //用于链式调用
    }

    /**
     * @param array $_option
     * @return $this
     */
    public function limit($_option){
        unset($this->limit);//清空之前的where条件

        $this->limit=$_option;
        return $this;   //用于链式调用
    }

    /**
     * @param array $_option
     * @return $this
     */
    public function order($_option){
        unset($this->order);//清空之前的where条件

        $this->order=$_option;
        return $this;   //用于链式调用
    }

    /**
     * @param array $_option
     * @return $this
     */
    public function join($_option){
        unset($this->join);//清空之前的where条件

        $this->join=$_option;
        return $this;   //用于链式调用
    }

    /**
     * 注：该方法必须在data方法执行后执行
     * @param $_tables :表名
     * @return bool
     */
    public function insert($_tables){
        //data为空则返回false
        if($this->data==null) return false;

        $_field=$_valueField=$_values=" ";
        foreach ($this->data as $key => $value){
            $_field.="$key,";
            $_valueField.=":$key,";
        }
        $_field =substr($_field, 0, -1);
        $_valueField =substr($_valueField, 0, -1);
        $instruct = "INSERT INTO `$_tables` ($_field) VALUES($_valueField)";

        $re=$this->prepare($instruct);
        foreach ($this->data as $_key => $_value)
            $re->bindValue(":$_key",$_value);

        $re->execute();

        //插入完毕清空data
        unset($this->data);
    }

    /**
     * 注：该方法必须在data和where方法执行后执行
     * @param $_tables
     * @return bool
     */
    function update($_tables){
        //data或者where为空则返回false
        if($this->data==null||$this->where==null) return false;

        $_set='';
        foreach ($this->data as $key => $value)
            $_set.="$key".' = '.":$key,";

        $_set =substr($_set, 0, -1);

        $instruct="UPDATE `$_tables` SET $_set WHERE $this->where";
        echo $instruct;
        $re=$this->prepare($instruct);

        foreach ($this->data as $_key => $_value)
            $re->bindValue(":$_key",$_value);
        $re->execute();

        //更新完毕清空
        unset($this->data);
        unset($this->where);
    }

    /**
     * @param $_table
     * @param string $method
     * @return array|mixed|\PDOStatement
     */
    function select($_table,$method="one"){
        if(isset($this->field))
            $field=$this->field;
        else
            $field='*';
        $instruct = "SELECT $field FROM `$_table` ";

        if(isset($this->join))
            $instruct.=' JOIN '.$this->join;
        if(isset($this->where))
            $instruct.=' WHERE '.$this->where;
        if(isset($this->order))
            $instruct.=' ORDER BY '.$this->order;
        if(isset($this->limit))
            $instruct.=' LIMIT '.$this->limit;

        preDump($instruct);
        // echo $instruct;
        $record=$this->query($instruct);

        if($method==="one")
            $record=$record->fetch(\PDO::FETCH_ASSOC);
        else if($method==="all")
            $record=$record->fetchAll(\PDO::FETCH_ASSOC);

        unset($this->field);
        unset($this->where);
        unset($this->join);
        unset($this->limit);
        unset($this->order);

        return $record;
    }
}