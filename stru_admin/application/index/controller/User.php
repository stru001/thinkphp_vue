<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class User extends Controller
{
    public function checkLogin()
    {
    	$input = input();
    	$account = isset($input['account']) ? $input['account'] : '';
    	$password = isset($input['password']) ? $input['password'] : '';
    	$user = db('users')->where('account',$account)->find();
    	if($user){
    		$en_pwd = md5($password.$user['salt']);
    		if($en_pwd == $user['password']){
    			$token = md5($user['id'].$account.time());
    			//存储session
    			session($account,['token' => $token,'expire_time' => time() + 3600]);
                $data = ['user_id' => $user['id'],'user_name'=>$user['account'],'token' => $token];
    			return $this->show(600,'登陆成功',$data);
    		}else{
    			return $this->show(601,'密码错误',array());
    		}
    	}else{
    		return $this->show(601,'用户不存在',array());
    	}
    }

    public function show($code,$msg,$data,$total = -1){
    	if($total>-1){
    		$res = array(
	    		"code"=>$code,
	    		"msg"=>$msg,
	    		"total"=>$total,
	    		"data"=>$data
	    	);
    	}else{
    		$res = array(
	    		"code"=>$code,
	    		"msg"=>$msg,
	    		"data"=>$data
	    	);
    	}
    	return json_encode($res);
    }

}
