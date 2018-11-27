<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Service\Controller;

use Org\Wechat\Wechat;
use Org\Wechat\WechatAuth;

class WechatController
{
    private $wechat;
    private $data;
    private $wechatAuth;
    private $config;

    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function index()
    {
        // 微信修改配置验证token
        if (IS_GET) {
            exit(isset($_GET['echostr']) ? $_GET['echostr'] : '');
        }

        /* 加载微信SDK */
        $this->config = C('WEIXIN');
        $this->wechat = new Wechat($this->config);

        /* 获取请求信息 */
        $data = $this->wechat->request();
        if (! $data || ! is_array($data)) {
            exit('');
        }else{
            $this->data = $data;
        }

        if($data['MsgType'] == Wechat::MSG_TYPE_EVENT){ // 事件消息
            switch ($data['Event']) {
                case Wechat::MSG_EVENT_SUBSCRIBE: // 关注
                    $this->subscribe($data);
                    break;
                case Wechat::MSG_EVENT_SCAN: // 二维码扫描
                    $this->scan($data['FromUserName'], $data['EventKey']);
                    break;
                case Wechat::MSG_EVENT_UNSUBSCRIBE: // 取消关注
                    $this->unSubscribe($data['FromUserName']);
                    break;
                case Wechat::MSG_EVENT_TEMPLATESENDJOBFINISH: // 发送模板消息 - 事件推送
                    $this->templateSendJobFinish($data);
                    break;
                case Wechat::MSG_EVENT_CLICK: // 菜单点击
                    $this->msgEventClick($data);
                    break;
                case Wechat::MSG_EVENT_LOCATION: // 报告位置
                    break;
                case Wechat::MSG_EVENT_MASSSENDJOBFINISH: // 群发消息成功
                    break;
                case 'scancode_waitmsg':    // 扫码推事件且弹出“消息接收中”提示框
                    $this->scancode($data['FromUserName'], $data['ScanCodeInfo']['ScanResult'], $data['ScanCodeInfo']['ScanType']);
                    break;
                case 'VIEW':
                default:
                    exit('success');
            }
        }else if($data['MsgType'] == Wechat::MSG_TYPE_TEXT){
           // $this->receiveText($data['Content'], $data['FromUserName']);
           // $this->autoReply($data['Content'], $data['FromUserName']);
        }
        
        exit('success');
    }

    /**
     * 关注事件处理
     *
     * @param mixed $data
     */
    private function subscribe($data){
        $openid = $data['FromUserName'];
        
        $Model = D('Common/Member');
        $userinfo = $Model->syncWXUserBySubscribe($data);
        
        if(!empty($data['EventKey'])){
            $this->scan($openid, substr($data['EventKey'], 8), $userinfo['is_new']);
        }

        //$wxName = $this->config['name'];
        //$host = C('HOST');
        //$text = "欢迎关注{$wxName}！\r\n本商城是服务广大微商的采购性平台，B2B的模式，本着“正品优价、售后保障、品类齐全、轻松分享、一件代发“的口号来服务客户，让微商变得简单，让分享变成骄傲！商城是B2B的模式，采取会员制即会员级和游客级，游客级免费加盟，购买商品享受普通零售价；会员级需要交380元会员服务费，享受会员神秘价格、购物返积分、优惠券、培训指导等服务。\r\n✿立马创业当老板！<a href=\"{$host}/h5/pay/rule\">戳我马上升级会员</a>\r\n✿微商进货选{$wxName}！<a href=\"{$host}/h5/mall\">戳我去商城看看</a>";
        $text = '';
        $this->wechat->replyText($text);
        /*
        // 自动回复优惠券
        //$this->replyCoupon('57d50c6c6c799', $openid, $userinfo['is_new']);
        //查询关注回复内容
        $reply = M("wx_reply")->where("appid='{$this->config["appid"]}' AND is_subscribe=1")->find();
        $this->doAutoReply($reply);
        */
    }
    
    private function WechatAuth(){
        if(is_null($this->wechatAuth)){
            $this->wechatAuth = new WechatAuth($this->config);
        }
        
        return $this->wechatAuth;
    }
    
    /**
     * 扫描带参数二维码
     * @param unknown $openid
     * @param unknown $scene_str
     */
    private function scan($openid, $scene_str, $newMember = false){
        if($scene_str == 'dls_13'){ // 魔力果冻印刷的二维码
            $scene_str = 'moliguodong';
        }
        
        if($scene_str == 'lxkf'){
            $this->contactCustomer($openid);
        }else if($scene_str == 'moliguodong'){
            // https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQGs8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL3NUaV8xVFhsdnMtSDRTUGRtaEF0AAIEmOURWAMEAAAAAA==
            $this->moliguodong($openid, $newMember);
        }else if(is_numeric($scene_str)){
            $QrcodeModel = new \Common\Model\QrcodeModel();
            $qrcode = $QrcodeModel->where("id='{$scene_str}' AND appid='{$this->config['appid']}'")->find();
            if(!empty($qrcode)){
                switch ($qrcode['type']){
                    case $QrcodeModel::COUPON:  // 优惠券
                        $this->replyCoupon($qrcode['outer_id'], $openid, $newMember);
                        break;
                    case $QrcodeModel::GOODS:  // 商品
                        $this->replyGoods($qrcode['outer_id']);
                        break;
                }
            }
        }else if(substr($scene_str, 0, 4) == 'dls_'){ //发展代理
            $pid = substr($scene_str, 4);
            $this->updatePid($openid, $pid, $newMember);
        }
        
        $array = explode('-', $scene_str);
        switch ($array[0]){
            case 'coupon':
                $Model = M('kf_list');
                $videoList = $Model->field("weixin, qrcode")->where("type=2 AND enabled=1")->select();
                if(count($videoList) > 0){
                    shuffle($videoList);
                    $wechatAuth = $this->WechatAuth();
                    $html = '想了解更多请添加我们的小视频微信号：';
                    foreach ($videoList as $i=>$item){
                        $html .= '\r\n<a href=\"'.$videoList[0]['qrcode'].'\">'.$videoList[0]['weixin'].'</a>';
                    }
                    $wechatAuth->sendText($openid, $html);
                }
                
                $this->replyCoupon($array[1], $openid, $newMember);
                break;
        }
    }
    
    /**
     * 绑定上级
     * @param String $openid
     * @param int $pid
     * @param Boolean $newMember
     */
    private function updatePid($openid, $pid, $newMember = false){
        $Model = M('member');
        $member = $Model->join("wx_user AS wx ON wx.mid=member.id")
                ->where("wx.openid='{$openid}'")
                ->find();
        if(empty($member)){
            $this->wechat->replyText('二维码无效，无法绑定为好友！');
        }else if($member['agent_level'] > 0){
            $this->wechat->replyText('您已成为代理，无需绑定上级好友');
        }else if($member['id'] == $pid){
            $this->wechat->replyText('您不能绑定您自己');
        }else if($member['pid'] == $pid){
            $this->wechat->replyText('您早已与推荐人成为好友关系了!');
        }else if($member['pid'] > 0){
            $this->wechat->replyText('不可重新绑定推荐人!');
        }

        $member['is_new'] = $newMember;
        $member['subscribe'] = 1;
    
        $GuanzhuModel = new \Common\Model\GuanzhuModel();
        $shangxian = $GuanzhuModel->shareGuanzhu($member, $pid);
        if($shangxian == -1){
            $this->wechat->replyText($GuanzhuModel->getError());
        }
    
        $this->wechat->replyText('您已与推荐人【'.$shangxian['nickname'].'】绑定为好友！');
    }
    
    /**
     * 回复商品信息
     * @param int $goods_id
     */
    private function replyGoods($goods_id){
        $goods = M('mall_goods')->find($goods_id);
        if(empty($goods)){
            //  || $goods['is_del'] == 1 || $goods['is_display'] != 1
            return;
        }
        
        $title = $goods['title'];
        $discription = $goods['digest'];
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/h5/goods?id='.$goods['id'];
        $picurl = $goods['pic_url'];
        
        $this->wechat->replyNewsOnce($title, $discription, $url, $picurl);
    }
    
    /**
     * 取消关注事件处理
     *
     * @param mixed $data
     */
    private function unSubscribe($openid){
        M()->execute("UPDATE wx_user SET subscribe=0, unsubscribe_time=" . time() . " WHERE openid='{$openid}'");
        exit();
    }

    /**
     * 发送模板消息 - 事件推送
     *
     * @param mixed $data
     */
    private function templateSendJobFinish($data) {
        
    }

    /**
     * 菜单点击事件推送
     *
     * @param mixed $data
     */
    private function msgEventClick($data){
        switch ($data['EventKey']) {
            case 'fzdl':
                $this->fzdl($data['FromUserName']);
                break;
            case 'lxkf':
                $this->contactCustomer($data['FromUserName']);
            case 'sign':
                $this->sign($data['FromUserName']);
            default:
                $this->menuMsg($data['EventKey']);
                break;
        }
    }

    /**
     * 生成推荐二维码
     * @param unknown $openid
     */
    public function fzdl($openid){
        $dlsqr = M('member_dlsqr')->where("openid='{$openid}'")->find();
         
        // 素材已上传并未过期
        if(!empty($dlsqr) && !empty($dlsqr['expires']) && time() < $dlsqr['expires']){
            $this->wechat->replyImage($dlsqr['mediaid']);
        }
        
        $url = 'http://admin.xingyebao.com/h5/qr/recommend?openid='.$openid.'&time='.NOW_TIME;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPGET, 1);   
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); 

        $output = curl_exec($ch);
        curl_close($ch);
        $this->wechat->replyText('正在生成二维码...');
    }
    
    /**
     * 响应菜单消息（回复 文字 图文 消息）
     *
     * @param mixed $key
     */
    private function menuMsg($key){
        $event = explode(".", $key);
        
        //回复文字消息
        if($event[0] == "text"){
            $msg = M("wx_menu_event")->where("event_key='{$key}'")->find();
            if(!empty($msg)){
                $this->wechat->replyText($msg["content"]);
            }
        }
        
        //回复图文消息
        if($event[0] == "advanced_news" && is_numeric($event[1])){
            $this->replyAdvanced($event[1]);
        }
    }

   /**
     * 回复高级图文
     *
     * @param mixed $key
     */
    private function replyAdvanced($id){
        $news = array();
        $msg = M()->query("SELECT title, digest, link ,cover_url FROM wx_advanced_news WHERE id=".$id." or pid=".$id);
        
        if(empty($msg)){
           return; 
        }
        
        foreach($msg as $k=>$v){
            $v['cover_url'] = 'http://wslm.xingyebao.com/'.$v['cover_url'];
            $news[] = array_values($v);
        }
        
        $this->wechat->replyNews($news);
    }
    
    /**
     * 关键字自动回复
     *
     * @param unknown $text
     * @param unknown $openid
     */
    private function receiveText($text, $openid){
        $this->contactCustomer($openid);
    }
     
    /**
     * 自动回复
     * array $reply_con 
     * @param unknown $reply_con
     */
    private function autoReply($text, $openid){
        $Model = M();
        $sql = "SELECT *
                FROM wx_keyword AS kw
                INNER JOIN wx_reply AS reply ON reply.id=kw.reply_id
                WHERE reply.appid='{$this->config['appid']}' AND (kw.keyword='{$text}' OR kw.keyword LIKE '%{$text}%' OR is_default=1)
                ORDER BY kw.full_match DESC,is_default LIMIT 1";
        $reply = $Model->query($sql);
        $this->doAutoReply($reply[0]);
    }
    
    private function doAutoReply($reply){
        //如果没有匹配到则使用默认回复
        if(empty($reply)){
            return;
        }
        
        //格式化回复内容
        $reply_con = json_decode($reply["content"], true);
        //获取随机数
        $rand = rand(0, count($reply_con)-1);
        
        if($reply_con[$rand]["type"] == "text"){ //回复文字
            $this->wechat->replyText($reply_con[$rand]["content"]);
        }else if($reply_con[$rand]["type"] == "senior"){ //回复高级图文
            $this->replyAdvanced($reply_con[$rand]["content"]);
        }else{
            $ModelMaterial = M("wx_material");
            $material = $ModelMaterial->where("media_id='{$reply_con[$rand]["content"]}'")->find();
            if(empty($material)){return;}
        
            $content = json_decode($material["content"], true);
            if($material["type"] == "news"){ //图文
                if(count($content) == 1){
                    $content = $content[0];
                    $this->wechat->replyNewsOnce($content['title'], $content['digest'], $content['url'], $content['thumb_url']);
                }
        
                $news = array();
                foreach($content as $item){
                    $news[] = array($item['title'], $item['digest'], $item['url'], $item['thumb_url']);
                }
                $this->wechat->replyNews($news);
            }else if($material["type"] == "voice"){ //语音
                $this->wechat->replyVoice($content["media_id"]);
            }else if($material["type"] == "video"){
                $this->wechat->replyVideo($content["media_id"],$content["name"],$content["name"]);
            }
        }
    }
    
    /**
     * 转发到微信多客服
     * @param string $KfAccount
     */
    private function toCustomer($KfAccount = null){
        $this->wechat->replyCustomer($KfAccount);
    }
    
    /**
     * 联系在线客服
     */
    private function contactCustomer($openid){
        $this->wechat->replyNewsOnce(
            '【通知】好消息！！专属客服功能开通了~更快捷，更方便，更贴心！', 
            '点击查看详细信息，或请通过商品详情、订单详情来咨询客服，以便更精准、更快速的解决您的问题！', 
            'http://mp.weixin.qq.com/s?__biz=MzI3NjI3OTc4NQ==&mid=2247486095&idx=1&sn=6320672e5a9d86b02962e34210f0632a&chksm=eb76b209dc013b1f87a13401ef473ec4ca43886ea9d4ae55df7a393d11ed8285b5555d6f7485&scene=4#wechat_redirect', 
            'https://mmbiz.qlogo.cn/mmbiz_jpg/5Z5kMc99DGI1h25smjMzZycwEJuQTTqPttMAGiaaKGUkXSULHWSExAcZCAzuZEgW7oMB095nHty7gOdELhibIVNQ/0?wx_fmt=jpeg'
        );
    }
    
    /**
     * 签到
     */
    private function sign($openid){
        $Model = D('Sign');
        $sign = $Model->sign($openid);
        if($sign < 0){
            $this->wechat->replyText($Model->getError());
        }
        
        $text = "签到成功，已连续签到".$sign['continued']."次，\r\n本次获得".sprintf('%.2f', $sign['money']).'积分';
        $this->wechat->replyText($text);
    }
    
    /**
     * 扫码推事件且弹出“消息接收中”提示框
     * @param unknown $data
    */
    private function scancode($openid, $result, $type){
        if($type != 'barcode') $this->wechat->replyText("运单号不存在，\r\n请勿扫其他无关运单号，否则您将被屏蔽使用本功能");
        $code = explode(',', $result);
        $code = $code[1];
        
        $Model = new \Common\Model\HandbagExpressModel();
        $result = $Model->scan($openid, $code);
        $this->wechat->replyText($result);
    }
    
    /**
     * 回复优惠券信息
     * @param String $openid
     * @param String $code
     * @param Int $newMember
     */
    private function replyCoupon($code, $openid, $newMember){
        $Model = M('mall_coupon');
        $list = $Model->field("id, `code`, title, notice, cover")->where("`code`='{$code}'")->select();
        if(empty($list)){
            $this->wechat->replyText('优惠券不存在：'.$code);
        }
        
        $news = array();
        foreach ($list as $coupon){
            $news[] = array(
                $coupon['title'], 
                $coupon['notice'],
                C('HOST').'/h5/coupon/'.$coupon['code'],
                preg_match('/^(http)/', $coupon['cover']) ? $coupon['cover'] : C('CDN').$coupon['cover']
            );
        }
        $this->wechat->replyNews($news);
    }
    
    private function moliguodong($openid, $isNew = false){
       $this->replyGoods(1914);
    }
}
?>