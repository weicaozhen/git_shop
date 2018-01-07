<?php
namespace app\home\controller;

class User extends \think\Controller
{
	//首页
	public function index(){
		//判断用户是否登陆
		// print_r($this->user_id);
		// 查找数据库的数据
		return $this->fetch();

	}

	//登录
	public function login(){
		return $this->fetch();

	}
	//注册
	public function reg(){
		return $this->fetch();
    }

		// if(IS_POST){
		// 	$username = I('post.username','');
		// 	$tel = I('post.tel','');
		// 	$email = I('post.email','');
  //           $password = I('post.password','');
  //           $password2 = I('post.password2','');
  //           $data = $this->reg_data($username,$tel,$email,$password,$password2);
  //           if($data['status'] != 1)
  //               $this->error($data['msg']);
  //           session('user',$data['result']);
  //           session('user_id',$data['result']['user_id']);
  //           $nickname = empty($data['result']['nickname']) ? $username : $data['result']['nickname'];
  //           setcookie('uname',$nickname,null,'/');
  //           $this->success($data['msg'],U('Home/User/index'));
  //           exit;
		// }
		// $this->assign('regis_sms_enable',tpCache('sms.regis_sms_enable')); // 注册启用短信：
  //       $sms_time_out = tpCache('sms.sms_time_out')>0 ? tpCache('sms.sms_time_out') : 120;
  //       $this->assign('sms_time_out', $sms_time_out); // 手机短信超时时间
  //       $this->display();


	//获取前段传来的数据
	public function reg_data($username,$tel,$email,$password,$password2){
		//验证是否存在用户名
		if(check_username($username) > 0){
			return array('status'=>-1,'msg'=>'该用户名已存在，请重新输入','result'=>'');
		}else{
        	$map['username'] = $username;
		}

		if(check_email($email)){
        	$map['email'] = $email;
        }else{
            return array('status'=>-2,'msg'=>'请输入正确的邮箱','result'=>'');

        }

        if(check_mobile($tel)){
        	$map['mobile'] = $tel;
        }else{
            return array('status'=>-3,'msg'=>'请输入正确的手机','result'=>'');
        }
        

        if(!$username || !$password){
            return array('status'=>0,'msg'=>'请输入用户名或密码','result'=>'');
        }

        //验证两次密码是否匹配
        if($password2 != $password){
            return array('status'=>-4,'msg'=>'两次输入密码不一致','result'=>'');
        }
	

		$map['password'] = encrypt($password);
        $map['reg_time'] = time();
          print_r($map);           
        $row = M('users')->add($map);
        if(!$row)        
            return array('status'=>-1,'msg'=>'注册失败','result'=>'');        
        
        $user_id = M()->getLastInsID();        
        $pay_points = tpCache('basic.reg_integral'); // 会员注册赠送积分
        if($pay_points > 0)
            accountLog($user_id, 0,$pay_points, '会员注册赠送积分'); // 记录日志流水                  
        $user = M('users')->where("user_id = {$user_id}")->find();
        return array('status'=>1,'msg'=>'注册成功','result'=>$user);
        // return $this->fetch('reg');
	}
	//检查用户名是否存在数据库中
	public function check_username($username){
		return Db::table('uz_user')->where('username',$username)->find();
	}
}