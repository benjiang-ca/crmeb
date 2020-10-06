<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::any('api/wechat/serve', 'WechatNotice/serve');
Route::get('install','Install/begin');
Route::group('install',function(){
   route::get('environment','/environment') ;
   route::get('databases','/databases') ;
   route::post('databases/create','/create') ;
   route::post('databases/check','/databasesCheck') ;
   route::post('perform/:n','/perform') ;
   route::get('end','/end') ;
   route::get('loader','/swooleCompiler') ;
})->prefix('Install');
