<?php
// 判断函数是否已经存在
if (!function_exists('return_json')) {


    function return_json($data = [],$state = 2000,$message = '操作成功')
    {
        return array('code' => $state, 'msg' => $message, 'data' => $data);
    }


}

if (!function_exists('return_err_json')) {


    function return_err_json($message = '操作失败',$state = 2001)
    {
        return array('code' => $state, 'msg' => $message);
    }


}