<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
	//首页
    public function index()
    {
        $list=db('xuyuans')
             ->field('id,type,content,address,member,pubtime,typecolor')
             ->order('id desc')
             ->paginate(4);
             
             // echo "<pre>";
             // pirnt_r($list);
        $page=$list->render();
        $lists=$list->toArray();
        $vowtype=config('vowtype');
        $tcolor=config('typecolor');
        foreach($lists['data'] as &$v)
        {
            $v['type']=$vowtype[$v['type']];
            $v['typecolor']=$tcolor[$v['typecolor']-1];

        }

        $this->assign('page',$page);
        
        $this->assign('list',$lists);

        return $this->fetch();
    }

    //ajax 分页
     public function newindex()
    {
       $list=db('xuyuans')->page(1,4)->select();
       $countpage=ceil(db('xuyuans')->count()/4);

       // echo $countpage;
       // exit;
       $vowtype=config('vowtype');
        $tcolor=config('typecolor');
        foreach($list as &$v)
        {
            $v['type']=$vowtype[$v['type']];
            $v['typecolor']=$tcolor[$v['typecolor']-1];

        }
       $this->assign('list',$list);
       
       
       $this->assign('countpage',$countpage);
       return $this->fetch();
    }

    public function getlist()
    {
        $page=input('page');
        $list=db('xuyuans')->order('id desc')->page($page,4)->select();
        $vowtype=config('vowtype');
        $tcolor=config('typecolor');
        foreach($list as &$v)
        {
            $v['type']=$vowtype[$v['type']];
            $v['typecolor']=$tcolor[$v['typecolor']-1];

        }
        return json($list); 
    }


    //发布愿望
	 public function add()
    {
    	$typecolor=config('typecolor');
    	$this->assign('typecolor',$typecolor);
    	$vowtype=config('vowtype');
    	$this->assign('vowtype',$vowtype);
        return $this->fetch();
    }

    //public function do_add(){
		//$data=input('post.');
		//echo "<pre>";
		//print_r($data);	

	// 	$data['inputtime']=time();
	// 	$db=db('xuyuans');		
	// 	if($data['username']==''){
	// 		$this->error('用户名不能为空','Index/adduser');
	// 	}
	// 	$list=$db->insert($data);
	// 	if($list){
	// 		$this->success('succ','Index/index');
	// 	}else{
	// 		$this->error('fail','Index/add');
	// 	}
	//} 




	  public function do_add()
    {
        //type,content,address,member,pubtime,
        //typecolor
        $data=input('post.');
        $types=config('vowtype');
        $vowtype=array_search($data['type'],$types);
        $data['type']=$vowtype;
        $code=$data['code'];
        unset($data['code']);
        $data['address']='山东省临沂市罗庄区';
        $data['pubtime']=time();
        if(captcha_check($code))
        {
            $table=db('xuyuans');
            $info=$table->insert($data);
            if($info)
            {
                $result=[
                	'msg'=>'许愿成功','status'=>1,
                ];
            }
            else{
            	$result=[
                	'msg'=>'许愿失败！','status'=>2,
                ];
            }
        }
        else{
        	$result=[
                	'msg'=>'验证码错误！','status'=>3,
                ];
        }
        return json ($result);
   }



   //省 
    public function area()
    {
    	//Db::connect('mysql://root:1234@127.0.0.1:3306/area#utf8');
     $db=db('province');
     $list=$db->select();
     $this->assign('list',$list);
     return $this->fetch();	     
    }

	//市	
	public function shi(){    
	$id=input('id');
	$db=db('city');
	$adata=$db->where('provincecode="'.$id.'"')->select();
	$opt = '<option>--请选择市--</option>';
 	foreach($adata as $key=>$val){
    	$opt .= "<option value='{$val['code']}'>{$val['name']}</option>";
 	}
 	return json($opt);
	}

	//区
	public function qu(){
	$id=input('id');
	$db=db('area');
	$adata=$db->where('citycode="'.$id.'"')->select();
	$opt = '<option>--请选择区--</option>';
	foreach($adata as $key=>$val){
    	$opt .= "<option>{$val['name']}</option>";
	}
    	return json($opt);
	}



    //请假
    public function qingjia()
    {
       
        return $this->fetch();
    }
   

     public function do_qingjia()
    {
        $db=db('qingjia');
        $data=input('post.');
          echo "<pre>";
          print_r($data);   

        
        if($data['name']==''){
            return $this->error('名字不能为空');
        }elseif($data['name']!='')
        {
        if($data['phone']==''){
            return $this->error('手机号不能为空');break;
        }
        if($data['phones']==''){
            return $this->error('家长手机号不能为空');break;
        }
        if($data['banji']==''){
            return $this->error('班级不能为空');break;
        }
        if($data['content']==''){
            return $this->error('内容不能为空');break;
        }   
        $info=$db->insert($data);   
        return $this->success('添加成功');break;
        }    
     }



      public function qingjias()
    {
       
        return $this->fetch();
    }


     public function do_qingjias()
    {
        $table=db('qingjia');
        $data=input('post.');
          echo "<pre>";
          print_r($data);
        // $table['times']=time();
        // $table['notimes']=time();
       // echo "<pre>";
       // print_r($info);exit;
        $list=$table->insert($data);
        if($list){
          $this->success('succ');
      }else{
          $this->error('fail');
      }   
     }
     public function denglu()
    {
       
        return $this->fetch();
    }
    public function do_denglu()
    {
        $username=input('post.logname');
        $password=input('post.logpass');
        $table=db('username');
        $info=$table->where('username="'.$username.'"')->find();
        // echo "<pre>";
        // print_r($info);exit;
      if($info) 
        {
            if($info['password']==$password)
            {
                $this->success('登录成功','Index/newindex');
            }
            else{
                $this->error('密码错误,登录失败');
            }
        }
        else{
            $this->error('登录失败，用户名不存在');
        }


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
