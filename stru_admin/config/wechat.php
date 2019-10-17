<?php
// +----------------------------------------------------------------------
// | 微信公众号设置
// +----------------------------------------------------------------------
return [

    'app_id' => 'wx0edc8d11077c4fea',
    'app_secret' => 'abf5f1601045b829cd1cf6d271c3fe71',
    'encoding_aes_key' => 'M3y79Dco9670jq7Y0ACUuQmSzTpYf0gmxUy1ZHMkxMS',
    'access_token_url' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&',

    // 定义微信返回的错误码
    'ok' => 0,
    'business' => -1,
    'secret_error' => 40001, //AppSecret错误或者AppSecret不属于这个公众号
    'grant_error' => 40002, //请确保grant_type字段值为client_credential
    'ip_error' => 40003,    //不在白名单
    'not_white_list' => 40164, //IP地址不在白名单里

    //高德地图key
    'map_key' => '1a5b65624003ad8b11f5350a11a0bcd4',
];
