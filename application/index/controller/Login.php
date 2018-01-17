<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Controller;
use app\index\controller\Common;
class Login extends Common
{
    public function denglu(){
        return $this->fetch();
    }
    public function zhuce(){
        return $this->fetch();
    }

    public function do_login(){
        $data=input('post.');
        $username=input('post.username');
        $db=db('username')->where("username='".$data['username']."'")->find();
        if($db){
            if($data['password']==$db['password']){
                session('username',$data['username']);
                $result=[
                    'msg'=>'登录成功',
                    'status'=>1
                ];
                if(isset($data['ischeck']))
                {
                    cookie('username',$username,864000);
                }
                return json($result);
            }else{
                $result=[
                    'msg'=>'密码错误',
                    'status'=>0
                ];
                return json($result);
            }

        }else{
            $result=[
                'msg'=>'用户名不存在',
                'status'=>0
            ];
            return json($result);
        }

    }


}
