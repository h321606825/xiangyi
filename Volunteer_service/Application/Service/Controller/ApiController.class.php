<?php
namespace Service\Controller;

use Org\Wechat\WechatAuth;
use Common\Common\CommonController;

/**
 * 公共类
 *
 * @author lanxuebao
 */
class ApiController extends CommonController
{
    /**
     * 获取微信JSSDK参数
     */
    public function jssdkconfig(){
        $w = new WechatAuth();
        $data = $w->getSignPackage();
        $this->ajaxReturn($data);
    }
    
    public function accssToken(){
        $w = new WechatAuth();
        $data = $w->getAccessToken();
        exit($data);
    }
    
    public function getJsApiTicket(){
        $w = new WechatAuth();
        $data = $w->getJsApiTicket();
        exit($data);
    }
    
    /**
     * 下载多媒体文件到本地
     * @param string $media_id
     */
    public function syncMaterial($type = 'news', $page = 1){
        $size = 20;
        $appid = C('WEIXIN.appid');
        
        $list = array();
        $result = array();
        $auth = new WechatAuth();
        $data = $auth->batchgetMaterial($type, ($page - 1) * $size, $size);

        if(isset($data['error'])){
            $this->error('更新失败：'.$data['errmsg']);
        }
        
        $total = $data['total_count'];
        
        if($type == 'news'){
            foreach ($data['item'] as $item){
                $content = array();
                $update_time = date('Y-m-d H:i:s', $item['update_time']);
                $news = array(
                    'appid'         => $appid,
                    'type'          => $type,
                    'media_id'      => $item['media_id'],
                    'title'         => $item['content']['news_item'][0]['title'],
                    'update_time'   => $update_time,
                    'url'           => $item['content']['news_item'][0]['url']
                );
            
                foreach($item['content']['news_item'] as $_news){
                    unset($_news['content']);
                    $content[] = $_news;
                }
                
                $news['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
                
                $list[] = $news;
                
                $result[] = array(
                    'media_id'      => $item['media_id'],
                    'update_time'   => $update_time,
                    'content'       => $content
                );
            }
        }else{
            foreach ($data['item'] as $item){
                $list[] = array(
                    'appid'         => $appid,
                    'type'          => $type,
                    'media_id'      => $item['media_id'],
                    'title'         => $item['name'],
                    'update_time'   => date('Y-m-d H:i:s', $item['update_time']),
                    'url'           => $item['url'],
                    'content'       => null
                );
                $result[] = array(
                    'media_id'      => $item['media_id'],
                    'name'          => $item['name'],
                    'update_time'   => date('Y-m-d H:i:s', $item['update_time']),
                    'url'           => $item['url']
                );
            }
        }
        
        $this->ajaxReturn(array('total'=> $total, 'rows' => $result));
    }
    
    /**
     * 下载微信多媒体文件
     */
    public function meida_down(){
        $appid = $_REQUEST['appid'];
        $list  = $_REQUEST['mediaid'];

        $config     = get_wx_config($appid);
        $wechatAuth = new WechatAuth($config['WEIXIN']);
        $url = array();
        foreach ($list as $mediaId){
            $folder = '/upload/refund/'.date('Y-m-d');
            if(!@is_dir($_SERVER['DOCUMENT_ROOT'].$folder)){
                mkdir ($_SERVER['DOCUMENT_ROOT'].$folder, 0777, true);
            }
            
            $url[] = $config['CDN'].$wechatAuth->meidaDownLoad($mediaId,  $folder.'/'.date('YmdHis').rand(100, 999));
        }
        
        $this->ajaxReturn($url, 'JSONP');
    }
}
?>