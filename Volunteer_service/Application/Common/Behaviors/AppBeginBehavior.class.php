<?php
namespace Common\Behaviors;

class AppBeginBehavior extends \Think\Behavior{

    //行为执行入口
    public function run(&$param){
        $this->init();
        
        // 微信授权登陆
        if (MODULE_NAME == 'H5' && isset($_GET['state']) && !empty($_GET['code'])) {
            $uid = session('user.id');
            if(empty($uid)){
                $this->getWXUserInfo();
            }
        }
    }
    
    /**
     * 初始化系统变量
     */
    private function init(){
        $config = get_wx_config();
        if(is_array($config)){
            C($config);
        }
        
        // print_data('未找到微信配置文件，HOST域名不存在：'. $_SERVER['HTTP_HOST']);
    }
    
    /**
     * 获取微信用户信息
     */
    private function getWXUserInfo()
    {
        $Model = D('Common/Member');
        $userinfo = $Model->syncWXUserByCode($_GET['code'], $_GET['state']);
        
        if(empty($userinfo)){
            return;
        }
        session('user', array('id' => $userinfo['id'], 'openid' => $userinfo['openid'], 'login_type' => 1));
        
        /*
        // 查询该会员是否在黑名单中
        $black = M("member_black")
                 ->where("mid='{$member['id']}' AND enabled=1 AND end_time>".time()."")
                     ->order("end_time desc")
                 ->find();
        
        if(!empty($black)){
                $session['black_start'] = $black['start_time'];
                $session['black_end'] = $black['end_time'];
        }
        */
    }
}
?>