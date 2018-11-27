<?php 
namespace Common\Model;

use Org\Wechat\WechatAuth;
/**
 * 订单处理modal
 * @author lanxuebao
 *
 */
class OrderModel extends BaseModel{
    protected $tableName = 'wx_order';

    /**
     * 生成微信支付(*total_fee单位为元，系统会自动转成分)
     * @param unknown $data
     * @param string $retry
     * @return \Org\WxPay\成功时返回，其他抛异常|\Org\WxPay\json数据，可直接填入js函数作为参数
     */
    public function createWxPayOrder($data, $retry = false){
        //下单结果通知url
        if(!isset($data['notify_url']))
            $data['notify_url'] = U('/service/wxpaynotify', '', true, true);
        
        ini_set('date.timezone','Asia/Shanghai');
        
        $tools = new \Org\WxPay\JsApiPay();

        //②、统一下单
        $input = new \Org\WxPay\WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        $input->SetDetail($data['detail']);
        $input->SetAttach($data['attach']);
        $input->SetOut_trade_no($data['order_no']);
        $input->SetTotal_fee($data['total_fee'] * 100);
        $input->SetTime_start($data['time_start']);
        $input->SetTime_expire($data['time_expire']);
        $input->SetGoods_tag($data['goods_tag']);
        $input->SetNotify_url($data['notify_url']);
        $input->SetSpbill_create_ip(get_client_ip());
        $input->SetTrade_type('JSAPI');
        $input->SetOpenid($data['openid']);
        $order = \Org\WxPay\WxPayApi::unifiedOrder($input);
        
        // 保存微信订单记录
        $values = $input->GetValues();
        $data = array_merge($values, $order);
        M('wx_order')->add($data);
        
        if($order['result_code'] != 'SUCCESS'){
            if($retry == true){
                $this->createWxPayOrder($data, false);
            }else{
                return $order;
            }
        }
        
        return $tools->GetParameters($order);
    }
    
  
}
?>