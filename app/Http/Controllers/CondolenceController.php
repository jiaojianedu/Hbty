<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\lib\Ucpaas;
use Illuminate\Support\Facades\Cache;
// 引入鉴权类
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

// 引入上传类
use Qiniu\Storage\UploadManager;
require_once app_path('../vendor/autoload.php');

class CondolenceController extends Controller
{
	//创建吊唁
   public function createCond(Request $request){
       $req = $request->all();
       return return_json($req);
       $req['death_pic'] = $this->uploads($request);
       if($req['death_pic']==false){
           return return_err_json('图片上传失败','2002');
       }
       $db = DB::table('condolence')->insertGetId([
           'cond_death_name'=>$req['death_name'],
           'cond_birth_place'=>$req['birth_place'],
           'cond_death_place'=>$req['death_place'],
           'cond_birth_day'=>$req['birth_day'],
           'cond_death_day'=>$req['death_day'],
           'cond_pic'=>$req['death_pic'],
           'cond_death_intro'=>$req['death_intro'],
           'cond_comment_switch'=>$req['aaa']
       ]);
       if($db){
           foreach($req['cond_impression'] as $v){
               DB::table('impression')->insert(['imp_impress'=>$v,'imp_death_id'=>$db]);
           }
           $death = DB::table('condolence')
               ->leftJoin('impression','condolence.cond_id','=','impression.imp_death_id')
               ->where('cond_id',$db)->first();
           return return_json($death);
       }

   }

   public function uploads(Request $request){
       // 需要填写你的 Access Key 和 Secret Key
       $accessKey = '79vup0Y_lRWo6AIosx6OGPVKMS7eISP2kKPY-wWi';
       $secretKey = 'C_-UL-JbCzn-wySDilSCaia79Dpb6Sl0aJAPJ3yA';
       // 构建鉴权对象
       $auth = new Auth($accessKey, $secretKey);
       // 要上传的空间
       $bucket = 'hbty';
       // 生成上传 Token
       $token = $auth->uploadToken($bucket);
       // 要上传文件的本地路径
       $filePath = $request->file('death_pic');
       $a = time();
       $b = rand(1111,9999);
       $name = $a.$b;
       $suffix = $request->file('death_pic')->getClientOriginalExtension();
       // 上传到七牛后保存的文件名
       $key = $name.'.'.$suffix;
       // 初始化 UploadManager 对象并进行文件的上传
       $uploadMgr = new UploadManager();
       // 调用 UploadManager 的 putFile 方法进行文件的上传
       list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
       echo "\n====> putFile result: \n";
       if ($err !== null) {
           return false;
       } else {
           return 'ouvegnn6u.bkt.clouddn.com/hbty'.$key;
           //$pic = 'ouvegnn6u.bkt.clouddn.com/hbty/'.$key;
          // $db = DB::table('rotation')->insert(['lun_intro'=>$req['lun_intro'],'lun_original_pic'=>$pic,'lun_link'=>$req['lun_link'],'lun_original_pic'=>$req['lun_pic'],'lun_pic_name'=>$key]);
           //if($db){
           //    return redirect()->route('admin.firstRotation')->withFlashSuccess('添加成功');
          // }else{
               //return back()->withErrors('添加失败');
           //}
       }
   }

   public function deleteCond(Request $request){
       // 需要填写你的 Access Key 和 Secret Key
       $accessKey = '79vup0Y_lRWo6AIosx6OGPVKMS7eISP2kKPY-wWi';
       $secretKey = 'C_-UL-JbCzn-wySDilSCaia79Dpb6Sl0aJAPJ3yA';
       //初始化Auth状态
       $auth = new Auth($accessKey, $secretKey);
       //初始化BucketManager
       $bucketMgr = new BucketManager($auth);
       //你要测试的空间， 并且这个key在你空间中存在
       $bucket = 'hbty';
       $key = '15032924845299.jpg';
       //删除$bucket 中的文件 $key
       $err = $bucketMgr->delete($bucket, $key);
       echo "\n====> delete $key : \n";
       if ($err !== null) {
           var_dump($err);
       } else {
           echo "Success!";
       }
   }
}
