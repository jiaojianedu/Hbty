<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\lib\Ucpaas;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
	//注册
   public function register(Request $request){
       $a = $request->all();
       if($a['verify_code']!=Cache::get('regcode')){
           return return_err_json('验证码不正确');
       }
       $db = DB::table('frontuser')->where('user_phone',$a['user_phone'])->first();
       if($db){
           return return_err_json('用户已存在');
       }else{

           $b = Hash::make($a['user_pass']);
           $dd = DB::table('frontuser')->insert(['user_phone'=>$a['user_phone'],'user_pass'=>$b]);
           if($dd){
               return return_json();
           }else{
               return return_err_json('注册失败');;

           }

       }
   }

   //注册验证码
   public function regCode(Request $request){
   		$options['accountsid']='e5e3329d9db1ee630550fe8944e7f42d';
        $options['token']='852f05395fd51f3555e7b1ab54c2bd80';
        //初始化 $options必填
        $ucpass = new Ucpaas($options);
        //开发者账号信息查询默认为json或xml
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "a46c3020f89549c3a9c501300b711a42";
        $to = $request->input('user_phone');
        $templateId = "120944";
        $code = rand(1000,9999);
        $param=$code;
        echo $ucpass->templateSMS($appId,$to,$templateId,$param);
        Cache::put('regcode',$param,3);
        return return_json();
   }

   //用户登录
    public function login(Request $request){
       $login = $request->all();
       $log =  DB::table('frontuser')->where('user_phone',$login['user_phone'])->first();
       if(!$log){
            return return_err_json('用户不存在');
       }else{
           if(Hash::check($login['user_pass'],$log->user_pass)){
           $arr = [];
           $user_phone = $request->input('user_phone');
           $md5 = 'tianyu'.$user_phone;
           $token = md5($md5);
           $arr['token'] = $token;
           $arr['userInfo'] = $log;
           }
           return return_json($arr);
       }
    }

    //找回验证
    public function resetCode(Request $request){
        $options['accountsid']='e5e3329d9db1ee630550fe8944e7f42d';
        $options['token']='852f05395fd51f3555e7b1ab54c2bd80';
        //初始化 $options必填
        $ucpass = new Ucpaas($options);
        //开发者账号信息查询默认为json或xml
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "a46c3020f89549c3a9c501300b711a42";
        $to = $request->input('user_phone');
        $templateId = "122289";
        $code = rand(1000,9999);
        $param=$code;
        echo $ucpass->templateSMS($appId,$to,$templateId,$param);
        Cache::put('resetCode',$param,3);
        return return_json();
    }

    //找回密码
    public function resetPass(Request $request){
        $reset = $request->all();
        $res = DB::table('frontuser')->where('user_phone',$reset['user_phone'])->first();
        if(!$res){
           return return_err_json('账号不存在');exit;
        }
        if($reset['reset_code']!=Cache::get('resetCode')){
            return return_err_json('验证码不正确','2002');exit;
        }
        return return_json();
    }

    //设置新密码
    public function newPass(Request $request){
        $new = $request->all();
        $b = Hash::make($new['user_newpass']);
        $db = DB::table()->where('user_phone',$new['user_phone'])->update(['user_pass'=>$b]);
        return return_json();
    }
}
