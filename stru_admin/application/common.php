<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * /组合返回数据函数
 * @param  [type]  $code  [返回码]
 * @param  [type]  $msg   [错误/正确消息]
 * @param  [type]  $data  [返回的数据]
 * @param  integer $total [分页总条数]
 */
function show($code,$msg,$data,$total = -1){
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