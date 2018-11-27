<?php
namespace Admin\Controller;
use Common\Common\CommonController;
use Org\Wechat\WechatAuth;

/**
 * 微信配置
 *
 * @author 王宝福
 *
 */
class WeixinController extends CommonController
{
    function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $Model_wx = M("wx_config");
        $Model = M("shop");
        $shop = $Model->where("id='".$this->shop['id']."'")->field("wx_appid")->find();
        
        $data = $Model_wx->where("appid='{$shop["wx_appid"]}'")->find();
        
        if(IS_POST){
            $up = array(
                "appid" => I("post.appid"),
                "name" => I("post.name"),
                "wxid" => I("post.wxid"),
                "server_url" => I("post.server_url"),
                "original_id" => I("post.original_id"),
                "secret" => I("post.secret"),
                "token" => I("post.token"),
                "encoding_aes_key" => I("post.encoding_aes_key"),
                "mchAccess" => I("post.mchaccess"),
                "mch_id" => I("post.mch_id"),
                "sub_mch_id" => I("post.sub_mch_id"),
                "mch_key" => I("post.mch_key"),
                "mch_name" => I("post.mch_name"),
                "login_email" => I("post.login_email"),
                "qrcode" => I("post.qrcode")
            );
            
            if(I("post.edit_login_pwd") && I("post.edit_login_pwd")!=""){
                $up['login_pwd'] = I("post.edit_login_pwd");
            }
            
            if(I("post.login_pwd")){
                $up['login_pwd'] = I("post.login_pwd");
            }
            
            if(I("post.edit_mchpwd") && I("post.edit_mchpwd")!=""){
                $up['mchPwd'] = I("post.edit_mchpwd");
            }
            
            if(I("post.mchpwd")){
                $up['mchPwd'] = I("post.mchpwd");
            }
                
            //修改商铺的wx_appid
            $up_shop["wx_appid"] = I("post.appid");
            $Model->where("id='{$this->shop['id']}'")->save($up_shop);
            //修改商铺的微信配置文件
            if(empty($data)){
                $Model_wx->add($up);
            }else{
                $Model_wx->where("appid='{$shop["wx_appid"]}'")->save($up);
            }
            $this->success("已保存！");
        }
        
        $this->assign("data",$data);
        $this->display();
    }
    
    /**
     * 微信菜单
     */
    public function menu(){
        $weixin = C('WEIXIN');
        $appid  = $weixin['appid']; 
        if(IS_POST){
            $this->saveMenu($appid);
        }
        $Module = M('wx_menu');
        $menu = $Module->where("appid='{$appid}'")->find();
        if(empty($menu)){
            $menu = array('button' => 'null');
        }
        $this->assign('menu', $menu);
        $this->assign('wxname', $weixin['name']);
        $this->display();
    }
    
    private function saveMenu($appid){
        $id          = $_POST['id'];
        $button      = $_POST['button'];
        $is_matchrule= $_POST['is_matchrule'];
        $matchrule   = $_POST['matchrule'];
        $modify_time = date('Y-m-d H:i:s');
        
        $data = array(
                'appid'        => $appid,
                'button'       => json_encode($button, JSON_UNESCAPED_UNICODE),
                'is_matchrule' => $is_matchrule,
                'matchrule'    => is_array($matchrule) ? json_encode($matchrule, JSON_UNESCAPED_UNICODE) : '',
                'modify_time'  => $modify_time
            );
        
        $Module = M('wx_menu');
        if(is_numeric($id) && $id > 0){
            $Module->where("id={$id} AND appid='{$appid}'")->save($data);
        }else{
            $Module->add($data);
            $id = $Module->getLastInsID();
        }
        
        $button = $this->initButton($button, $id);
        
        $wechat = new WechatAuth();
        
        if($is_matchrule == 1){ //个性化菜单
            $matchrule = array(
                "group_id" => "",
                "sex" => 0,
                "country" => "中国",
                "province" => "",
                "city" => "",
                "client_platform_type" => "",
                "language" => ""
            );
            $result = $wechat->menuConditional($button, $matchrule);
            
            if($result["menuid"] > 0){
                $this->success('个性化菜单已更新，重新关注可立即看到效果','/weixin/menu');
            }
        }else{//自定义菜单
            $result = $wechat->menuCreate($button, $matchrule);
            
            if($result['errmsg'] == 'ok'){
                $this->success('微信菜单已更新，重新关注可立即看到效果','/weixin/menu');
            }
        }
        
        $this->error($result['errmsg']);
    }
    
    /**
     * 转成微信JSON格式
     * @param unknown $_button
     * @param unknown $menu_id
     */
    private function initButton($_button, $menu_id){
        $Model = M('wx_menu_event');
        $Model->where("menu_id=".$menu_id)->delete();
        
        $button = array();
        
        $events = array('text', 'advanced_news', 'event');
        $event_key = '';
        foreach($_button as $key=>$item){
            if(in_array($item['type'], $events)){
                if($item['type'] == "advanced_news"){
                    $event_key = $item['type'].".".$item["news_id"];
                    $Model->add(array( 'menu_id' => $menu_id, 'type' => $item['type'], 'content' => $item['content'], 'event_key' => $event_key));
                }else if($item['type'] == "text"){
                    $event_key = $item['type'].".".$menu_id.$key;
                    $Model->add(array( 'menu_id' => $menu_id, 'type' => $item['type'], 'content' => $item['content'], 'event_key' => $event_key));
                }else{
                    $event_key = $item['content'];
                }
            }
            
            $_button = $this->getButton($item, $event_key);
            
            if(isset($item['sub_button']) && is_array($item['sub_button'])){
                $_button['sub_button'] = array();
                foreach($item['sub_button'] as $_key=>$_item){
                    if(in_array($_item['type'], $events)){
                        if($_item['type'] == "advanced_news"){
                            $event_key = $_item['type'].".".$_item["news_id"];
                            $Model->add(array( 'menu_id' => $menu_id, 'type' => $_item['type'], 'content' => $_item['content'], 'event_key' => $event_key));
                        }else if($_item['type'] == "text"){
                            $event_key = $_item['type'].".".$menu_id.$key.$_key;
                            $Model->add(array( 'menu_id' => $menu_id, 'type' => $_item['type'], 'content' => $_item['content'], 'event_key' => $event_key));
                        }else{
                            $event_key = $_item['content'];
                        }
                    }
                    
                    $_button['sub_button'][] = $this->getButton($_item, $event_key);
                }
            }
        
            $button[] = $_button;
        }
        
        return $button;
    }
    
    /**
     * 菜单单项解析
     * @param unknown $item
     * @param unknown $key
     * @return multitype:multitype: string NULL unknown
     */
    private function getButton($item, $key){
        $menu = array('name' => $item['name']);
        // 跳转网页
        if($item['type'] == 'view'){
            $menu['type'] = 'view';
            $menu['url']  = $item['content'];
        }
        // 扫码
        else if($item['type'] == 'scan'){
            $menu['type'] = 'scancode_waitmsg';
            $menu['key']  = $key;
            $menu['sub_button']  = array();
        }
        // 相册或拍照
        else if($item['type'] == 'pic_photo_or_album' || $item['type'] == 'pic_sysphoto' || $item['type'] == 'pic_weixin' ){
            $menu['type'] = $item['type'];
            $menu['key']  = $key;
            $menu['sub_button']  = array();
        }
        // 图文消息
        else if($item['type'] == 'news' || $item['type'] == 'voice' || $item['type'] == 'video'){
            $menu['type']     = 'media_id';
            $menu['media_id'] = $item['content']['media_id'];
        }else if($item['type'] == 'text' || $item['type'] == 'advanced_news' || $item['type'] == 'event'){
            $menu['type'] = 'click';
            $menu['key']  = $key;
        }
        
        return $menu;
    }
    
    /**
     * 消息回复
     */
    public function reply(){
        $type = $_GET['type'] == 'keyword' ? 'keyword' : 'watch';
        
        $this->assign(array('type' => $type));
        $this->display();
    }
}

?>