<?php

namespace app\http\middleware;
use think\facade\Cache;
class Check
{
    public function handle($request, \Closure $next)
    {
        $input = input();
        $token = isset($input['token']) ? $input['token'] : '';
        $user_id = isset($input['user_id']) ? $input['user_id'] : '';
        if(!empty($token)){
            self::token_validate($token,$user_id);
        }else{
            echo show(602,'token失效，请重新登陆',array());die;
        }
        return $next($request);
    }

    public static function token_validate($token,$user_id)
    {
        //校验token主要是两方面：
        //第一：是否超时
        //第二：权限是否正确
        $user = db('users')->field('*')->where('id',$user_id)->find();
        $session_param = Cache::get($user['account']);
        if(!empty($session_param)){
            if($token == $session_param['token']){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
