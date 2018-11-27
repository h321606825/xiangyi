<?php
namespace Common\Common;

use Think\Controller;

/**
 * 基础父类.
 *
 * CollegeController description.
 *
 * @version 1.0
 * @author Administrator
 */
class CommonController extends Controller
{
    private $_common_shops;
    function __construct(){
        parent::__construct();
        
        if(MODULE_NAME == 'Admin'){
            $this->checkAccess();
        }
        
        if(IS_AJAX || IS_POST){
            C('LAYOUT_ON', false);
        }
        if(MODULE_NAME == 'H5' && CONTROLLER_NAME != 'Login'){
            //判断是否绑定账号
            $redirect = __SELF__;
            $this->judge_user($redirect);
        }
    }
    
    /**
     * 检查权限
     */
    private function checkAccess(){
        $uid = $this->user('id');
        $url = '';
        if(isset($this->authRelation)){
            $current = strtolower(ACTION_NAME);
            foreach($this->authRelation as $action=>$target){
                if($action == $current){
                    $url = $target;
                    break;
                }
            }
        }
        
        $result = Auth::get()->check($url);
        if(!$result){
            if(IS_AJAX || IS_POST){
                $this->error('您没有权限');
            }
            $auth_html = MODULE_PATH.'View/'.CONTROLLER_NAME.'/'.ACTION_NAME.'.auth.html';
            if(is_file($auth_html)){
                $this->display(ACTION_NAME.'.auth');
            }else{
                $this->display('./auth');
            }
        }
    }

    /**
     * 用户信息
     *
     * @param string $key
     *            字段名称（string表示多个用逗号间隔，array表示更新用户信息）
     * @return Ambigous <boolean, unknown>|\Think\mixed
     */
    protected function user($key = '*', $login = true)
    {
        //session("user" , array("openid" => "og13YvgpTnp82zYmM-5DbWsession("user" , array("openid" => "og13Yvg-fVD7WSP5sjMwD6we0eLY",'nickname'=>'心若止水','id'=>"1025719"));gcf9Wk",'nickname'=>'✎﹏ℳ๓₯㎕','id'=>"1022541"));
        session("user" , array("openid" => "og13Yvg-fVD7WSP5sjMwD6we0eLY",'nickname'=>'心若止水','id'=>"1025719"));

        $user = session('user');
        if (is_null($user)) {
            if($login)
                $this->goLogin();
            else
                return null;
        }
        
        /*
        $debug_list = array(1);
        if(!in_array($user['id'], $debug_list)){
            print_data('<h1>系统升级维护中</h1>');
        }
        */
        
        // 判断是否为封号状态
        if(isset($user['black_end']) && $user['black_end'] > time()){
            $this->error('您已被封号：'.date('Y-m-d H:i:s', $user['black_start']).' ~ '.date('Y-m-d H:i:s', $user['black_end']));
        }

        if (isset($user[$key])) {
            return $user[$key];
        }
        
        if(MODULE_NAME == 'Mall'){
            E('获取字段'.$key.'未实现！');
        }else if($key == '*'){
            return $user;
        }else{ // 微信登录
            if (isset($user[str_replace('wx.','',$key)])) {
                return $user[str_replace('wx.','',$key)];
            }
            
            if (isset($user[str_replace('m.','',$key)])) {
                return $user[str_replace('m.','',$key)];
            }
            
            $sql = "SELECT {$key}
                    FROM member, wx_user AS wx
                    WHERE member.id={$user['id']} AND wx.openid='".$user['openid']."'";
            $user = M()->query($sql);
            if(count($user) > 0){
                $user = $user[0];
            }
        }
        
        if(empty($user)){
            session('user', null);
            $this->goLogin();
        }
        
        if(count($user) == 1){
            return current($user);
        }
        return $user;
    }

    /**
     * 跳转到登陆
     */
    protected function goLogin()
    {   
        session('user', null);
        $redirect = (IS_AJAX || IS_POST) ? urlencode($_SERVER['HTTP_REFERER']) : urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $data = array('type' => '','status' => -1, 'url' => '/'.strtolower(MODULE_NAME).'/login?redirect=' . $redirect,'redirect' => $redirect, 'mobile' => cookie('auth_mobile'), 'appid' => '');
        
        if (MODULE_NAME == 'H5') {
            $config = C('WEIXIN');
            if(IS_WEIXIN){
                $data['url'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $config['appid'] . '&redirect_uri=' . $redirect . '&response_type=code&scope=snsapi_userinfo#wechat_redirect';
            }else{
                $data['type'] = 'app';
                $data['url'] = '/h5/login?redirect='.$redirect;
            }
            
            $data['appid'] = $config['appid'];
        }
        
        if(IS_AJAX){
            $this->ajaxReturn($data);
        }else{
            redirect($data['url'], 0);
        }
    }
    
    /*代理级别*/
    public function agentLevel($level = ''){
        $Model = M("agent");
        $data = $Model->field("id, `level`, title, price_title")->order("level asc")->select();
        $list = array();
        foreach($data as $k=>$v){
            $list[$v['level']] = $v;
        }
        
        if($level !== ''){
            return $list[$level];
        }
        return $list;
    }
    
    /**
     * 获取所有店铺
     */
    public function shops(){
        if(is_null($this->_common_shops)){
            $this->_common_shops = M('shop')->field('id, `name`, state')->order("id, state desc")->select();
        }
        return $this->_common_shops;
    }

    public function return_data($level = 1){
        $return_data = array(
            "1"  =>  "购物",
            "2"  =>  "取快递",
            "3"  =>  "校园出行",
            "4"  =>  "上门陪伴",
            "5"  =>  "整理资料",
            "6"  =>  "辅导手机应用",
            "7"  =>  "读报",
            "8"  =>  "其他",
            "9"  =>  "学生求助"
        );
        return $return_data[$level];
    }

    // 判断是否绑定账号
    public function judge_user($redirect){
        $user = $this->user();
        $teacher = M("teacher")->where("mid = %d",$user['id'])->find();
        if(empty($teacher)){
            $student = M("student")->where("mid = %d",$user['id'])->find();
            if(empty($student)){
                redirect('/h5/login?redirect='.$redirect);
            }
        }
    }
}
