<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/18
 * Time: 11:15
 */
namespace App\Http\Middleware;

use Closure;

class TokenVerify
{
    /**
     * 处理传入的请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_phone = $request->input('user_phone');
        $md5 = 'tianyu'.$user_phone;
        $token = md5($md5);
        if ($request->input('token')!=$token) {
            return return_err_json('用户未登录');
        }

        return $next($request);
    }

}