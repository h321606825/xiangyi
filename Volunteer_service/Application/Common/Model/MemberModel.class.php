<?php 
namespace Common\Model;

use Org\Wechat\WechatAuth;
class MemberModel extends BaseModel{
    protected $tableName = 'member';

    
    public function syncWxUser($userInfo, $from, $state = null){
        // 抛出异常
        if (isset($userInfo['errcode'])) {
            E('获取用户信息失败<hr>微信错误码：' . $userInfo['errcode'] . '<br>错误信息：' . $userInfo['errmsg']);
        }

        $now = time();
        $isNew = 0; // 是否为新会员
        
        $WXModel = M('wx_user');
        $MemberModel = M('member');
        
        $oldWXUser = $WXModel->where("openid='{$userInfo['openid']}'")->find();
        $oldMember = is_numeric($oldWXUser['mid']) && $oldWXUser['mid'] > 0 ? $MemberModel->find($oldWXUser['mid']) : array();

        $member = array();
        if(empty($oldMember)){
            $member = array(
                'reg_time'      => $now,
                'from'          => $from,
                'first_from'    => $from,
            );
        }
        
        if(empty($oldMember['nickname']))
            $member['nickname'] = $userInfo['nickname'];
        if(empty($oldMember['sex']))
            $member['sex'] = $userInfo['sex'];
        if(empty($oldMember['head_img']))
            $member['head_img'] = $userInfo['headimgurl'];
        
        // 获取省市区id
        if(empty($member['province_id']) && !empty($userInfo['province'])){
            $cityModel = M('city');
            if(!empty($userInfo['province']) && empty($member['province_id'])){
                $province = $cityModel->where("`short_name`='{$userInfo['province']}' AND `level`=1")->find();
                if($province) $member['province_id'] = $province['code'];
            }
            if(!empty($userInfo['city']) && empty($member['city_id'])){
                $city = $cityModel->where("`short_name`='{$userInfo['city']}' AND `level`=2")->find();
                if($city) $member['city_id'] = $city['code'];
            }
        }
        
        if(empty($oldMember)){
            $member['pid'] = 0;
            $member['id'] = $MemberModel->add($member);
        }else{
            if(!empty($member)){
                $MemberModel->where("id=".$oldMember['id'])->save($member);
                $member = array_merge($oldMember, $member);
            }else{
                $member = $oldMember;
            }
        }
        
        $userInfo['last_login'] = $now;
        $userInfo['mid'] = $member['id'];
        if(empty($oldWXUser)){
            $userInfo['created'] = $now;
            $WXModel->add($userInfo);
        }else{
            $WXModel->save($userInfo);
        }
        
        return array(
            'id'          => $member['id'],
            'pid'         => $member['pid'],
            'agent_level' => $member['agent_level'],
            'openid'      => $userInfo['openid'],
            'nickname'    => $userInfo['nickname'],
            'subscribe'   => $userInfo['subscribe'],
            'first_subscribe' => $userInfo['subscribe'] && (empty($oldWXUser) || empty($oldWXUser['subscribe_time'])),
            'is_new'      => empty($oldWXUser)
        );
    }
    
    /**
     * 添加微信会员
     */
    public function syncWXUserByCode($code, $state = null,$_config = array()){
        // 微信配置文件
        $config = C('WEIXIN');
        if(!empty($_config)){
            $config = $_config;
        }
        $wechatAuth = new WechatAuth($config);
        $token = $wechatAuth->getAccessToken('code', $code);
        if(!$token){
            return null;
        }
        $openid = $token['openid'];
        
        // 获取用户信息(未关注则获取不到)
        $userInfo = $wechatAuth->userInfo($openid);
        if(isset($userInfo['subscribe']) && $userInfo['subscribe'] == 0){
            // 用网页授权方式获取用户信息
            $userInfo = $wechatAuth->getUserInfo($token);
            $userInfo['subscribe'] = 0;
        }
        
        $userInfo['appid'] = $config['appid'];
        $from = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        
        return $this->syncWxUser($userInfo, $from, $state);
    }
    
    /**
     * 同步微信关注用户信息
     * @param unknown $data
     */
    public function syncWXUserBySubscribe($data){
        $config = C('WEIXIN');
        $wechatAuth = new WechatAuth($config);
        $userInfo = $wechatAuth->userInfo($data['FromUserName']);
        $userInfo['appid'] = $config['appid'];
        $from = !empty($data['EventKey']) ? 'wx_'.substr($data['EventKey'], 8) : 'wx';
        
        return $this->syncWxUser($userInfo, $from);
    }
    
    /**
     * 根据pId获取member表信息
     */
    public function edit($data){
        if(! preg_match('/^1[3|4|5|7|8]\d{9}$/', $data['mobile'])){
            $this->error = '手机号格式错误.';
            return -1;
        }
        $this->where("id = ".addslashes($data['id']))->save($data);
        return 1;
    }
    
}