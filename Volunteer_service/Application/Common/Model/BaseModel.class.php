<?php 
namespace Common\Model;

use Think\Model;
class BaseModel extends Model{
    private static $agentList = null;
    protected $autoCheckFields = true;
    
    function __construct($name='',$tablePrefix='',$connection=''){
        if(empty($name) || get_class($this) == 'Common\Model\BaseModel'){
            $this->autoCheckFields = false;
        }
        parent::__construct($name, $tablePrefix, $connection);
    }
    
    /**
     * 获取微信用户信息
     */
    public function getWXUserConfig($mid, $openid = ''){
        $sql = "SELECT wx_user.mid, wx_user.openid, wx_user.appid, wx_user.subscribe, wx_user.nickname, member.nickname AS `name`, member.pid, member.id
                FROM member
                INNER JOIN wx_user ON member.id=wx_user.mid
                WHERE member.id='{$mid}' 
                ORDER BY subscribe DESC, last_login DESC";
        $list = $this->query($sql);
        if(count($list) == 0){
            return;
        }
        $wxUser = $list[0];
        
        // 找当前openid
        if(!empty($openid) && count($list) > 1){
            foreach($list AS $item){
                if($item['openid'] == $openid){
                    if($item['subscribe'] == 1){
                        $wxUser = $item;
                    }
                    break;
                }
            }
        }

        if($wxUser['subscribe'] != 1){
            return $wxUser;
        }
        
        $wxUser['config'] = get_wx_config($wxUser['appid']);
        return $wxUser;
    }
    
    /**
     * 获取店铺信息
     */
    public function getAllShop(){
        return $this->query("SELECT id, `name` FROM shop");
    }
}
?>