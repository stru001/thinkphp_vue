<?php

class db{
    private $type;
    private $host;
    private $port;
    private $dbname;
    private $charset;
    private $user;
    private $pass;
    private $pdo;
    private static $instance;
    private function __construct($param){
        $this->initparam($param);
        $this->initconnect();
        $this->auto_exception();
    }
    private function __clone(){

    }
    public static function getinstance($param){
        if(!self::$instance instanceof self){
            self::$instance=new self($param);
            return self::$instance;
        }else{
            return self::$instance;
        }
    }
    //初始化参数
    public function initparam($param){
            $this->type = isset($param['type']) ? $param['type'] : 'mysql';
            $this->host = isset($param['host']) ? $param['host'] : 'rm-2zeq0erg91vk7tq0i.mysql.rds.aliyuncs.com';
            $this->port = isset($param['port']) ? $param['port'] : '3306';
            $this->dbname = isset($param['dbname']) ? $param['dbname'] : 'crab';
            $this->charset = isset($param['charset']) ? $param['charset'] : 'utf8';
            $this->user = isset($param['user']) ? $param['user'] : 'root';
            $this->pass = isset($param['pass']) ? $param['pass'] : '58iPI7ya@n%&';
    }
    //连接数据库
    private function initconnect(){
        try {
            $dsn = "$this->type:host=$this->host;port=$this->port;
            dbname=$this->dbname;charset=$this->charset";
            $this->pdo = new \PDO($dsn, $this->user, $this->pass);
        }catch(\PDOException $e){
            $this->Exception($e);
        }
     }

    //数据库操作
    //1：执行SQL语句函数
    public function execute($sql){
        try {
            return $this->pdo->exec($sql);
        }catch(\PDOException $e){
            $this->Exception($sql,$e);
        }
    }

    //2：匹配类型函数封装
    private function fetchtype($type='assoc'){
        switch($type){
            case 'assoc':return \PDO::FETCH_ASSOC;
            case 'num':  return \PDO::FETCH_NUM;
            case 'both': return \PDO::FETCH_BOTH;
            default:return \PDO::FETCH_ASSOC;
        }
    }
    //3：获取所有结果函数
    public function fetchall($sql,$type='assoc'){
        try {
            $stmt = $this->pdo->query($sql);
            $type = $this->fetchtype($type);
            return $stmt->fetchall($type);
        }catch(\PDOException $e){
            $this->Exception($sql,$e);
        }
    }

    //4:获取一条结果函数
    public function fetchone($sql,$type='assoc'){
        try {
            $stmt = $this->pdo->query($sql);
            $type = $this->fetchtype($type);
            return $stmt->fetch($type);
        }catch(\PDOException $e){
            $this->Exception($sql,$e);
        }
    }

    //5:获取一行一列
    public function fetchcolumn($sql){
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchcolumn();
        }catch(\PDOException $e){
            $this->Exception($sql,$e);
        }
    }

    //6：获取最后插入数据的编号（编号要是主键自动增长）
    public function getid(){
        return $this->pdo->lastinsertid();
    }

    //异常处理
    //1：设置异常自动抛出
    private function auto_exception(){
        return $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
    }

    //2:封装异常显示处理
    private function Exception($e,$sql=''){
        if($sql!=''){
            echo 'sql语句执行失败','<br>';
            echo '执行失败的sql语句是'.$sql,'<br>';
        }
        echo '错误编号：'.$e->getCode(),'<br>';
        echo '错误行号：'.$e->getline(),'<br>';
        echo '错误文件：'.$e->getfile(),'<br>';
        echo '错误信息：'.$e->getmessage(),'<br>';
        exit;

    }
}

$options['type'] = 'mysql';
$options['host'] = 'rm-2zeq0erg91vk7tq0i.mysql.rds.aliyuncs.com';
$options['port'] = '3306';
$options['dbname'] = 'crab';
$options['charset'] = 'utf8';
$options['user'] = 'root';
$options['pass'] = '58iPI7ya@n%&';

$db = db::getinstance($options);

$list = $db->fetchall('select a.id as order_id,a.phone,b.express_id from orders as a left join expresss as b on a.id=b.order_id where a.state = 4');

// print_r($list);die;
if(!empty($list)){
    include './ExpressBird.php';
    $expressBird = new ExpressBird();
    foreach($list as $k => $v){
        $phone = substr($v['phone'],7,4);
        $orderCode = $v['order_id'];
        $logisticCode = $v['express_id'];

        $express = $expressBird->getOrderTracesByJson($phone,$orderCode,$logisticCode);
        $express_arr = json_decode($express,true);
        // print_r($express_arr['State']);die;
        $sql = "update expresss set express_desc='".$express."',express_state=".$express_arr['State']." where order_id=".$v['order_id']." and express_id='".$v['express_id']."'";

        $ret = $db->execute($sql);

        //2-在途中,3-签收,4-问题件
        if($express_arr['State'] == 3){
            $order_sql = 'update orders set state=5 where id='.$v['order_id'];
            $order_ret = $db->execute($order_sql);
        }
    }
}