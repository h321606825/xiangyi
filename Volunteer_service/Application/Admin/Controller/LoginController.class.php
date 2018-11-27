<?php
namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller
{
    /**
     * 登录页面
     */
    public function index()
    {
        layout(false);
        $user = null;
        $redirect = !empty($_GET['redirect']) ? $_GET['redirect'] : "/admin/wish";
        $auto_login = -1;
        
        if(IS_GET){
            $session_user = session("user");
            if(!empty($session_user)){
                $this->display();
            }
            $user = $this->autoLogin();//自动登录验证
            if(is_string($user)){
                $this->assign('error', $user);
            }
        }else{
            $auto_login = I('post.auto_login', -1);
            $password = I('post.password');
            $username = I('post.username');
            if(strlen($password) > 0){
                $password = md5($password);
            }
            
            $user = $this->doLogin(array(
                'username' => $username,
                'password' => $password
            ));
            if(is_string($user)){
                $this->error($user);
            }
        }
        
        // 自动登录
        if($auto_login != -1){
            $this->do_Auto_login($username, $password);
        }
        
        // 登录成功
        if(is_array($user)){
            $this->write_session($user,$redirect);
        }
        
        $this->display();
    }

    /**
     * 自动登录验证
     * @return boolean|string|Ambigous <string, \Think\mixed>
     */
    private function autoLogin(){
        $username = cookie('auto_login');
        if(!$username){
            return false;
        }
    
        $where = "username='%s'";
        $auto_data = M("users_auto_login")->where($where, $username)->find();
        if(empty($auto_data) || $auto_data["last_time"] < time()){ //没有设置自动登录 就跳到登录页面让其输入信息
            return "未设置自动登录或自动登录时间已过期，请重新登录";
        }
    
        $ip = get_client_ip();//获取登录人的ip地址
        if($ip != $auto_data['ip']){
            return 'IP地址变更，请重新登录';
        }
    
        $user = $this->doLogin(array(
                'username' => $auto_data['username'],
                'password' => $auto_data['password']
            ));
        return $user;
    }
    
    /**
     * 执行登陆
     */
    public function doLogin($data)
    {
        //验证输入的信息
        if(strlen($data['username']) == 0){
            return '请输入账号！';
        }elseif(strlen($data['password']) == 0){
            return '请输入密码！';
        }
        
        //账号信息验证
        $Module = M('users');
        $user = $Module->alias('users')
                       ->field('users.*,shop.name AS shop_name')
                       ->join('shop ON users.shop_id=shop.id','INNER')
                       ->where("users.username='%s'",$data['username'])
                       ->find();
                       
        if (empty($user)) { // 账号不存在
            return '账号不存在';
        } elseif ($user['password'] != $data['password']) { // 密码错误
            return '密码错误';
        } elseif ($user['status'] == 0) { // 账号已被禁用
            return '账号未启用';
        }

        return $user;
    }
    
    /**
     * 登录后存储session数据并跳转页面
     */
    private function write_session($user, $redirect){
        // 将用户固定的信息存入session中
        session('user', array(
            'id' => $user['id'],
            'nick' => $user['nick'],
            'is_admin' => $user['administrator'],
            'username' => $user['username'],
            'shop_id'  => $user['shop_id'],
            'shop_name'=> $user['shop_name']
        ));
        // 保存菜单及节点id到session中
        $this->success('登陆成功！', $redirect);
    }
    
    /**
     * 保存自动登录信息
     * $password 已加密
     * @param unknown $username
     */
    private function do_Auto_login($username,$password){
        $ip = get_client_ip();//获取登录人的ip地址
        $where = "username='%s'";
        $auto_data = M("users_auto_login")->where($where,$username)->find();
        if(empty($auto_data)){ //添加
            $add["username"] = $username;
            $add["ip"] = $ip;
            $add["last_time"] = time()+259200;
            $add["password"] = $password;
            M("users_auto_login")->add($add);
        }else{ //修改
            $up["ip"] = $ip;
            $up["last_time"] = time()+259200;
            $up["password"] = $password;
            M("users_auto_login")->where("username='".$auto_data["username"]."'")->save($up);
        }
        //设置cookie
        cookie('auto_login',$username, array('expire'=>259200,'prefix'=>''));
    }
    
    /**
     * 退出登录
     */
    public function out()
    {
        session('[destroy]');
        cookie("auto_login",null);
        $this->redirect('/'.strtolower(MODULE_NAME).'/login');
    }
}