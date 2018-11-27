<?php
namespace Service\Controller;
use Org\WxPay\WxPayNotify;
use Org\WxPay\WxPayOrderQuery;
use Org\WxPay\WxPayApi;

class WxagentnotifyController extends WxPayNotify
{
    public function index(){
        $this->Handle(false);
    }

    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        // 保存通知记录
        M('wx_pay_notify')->add($data);
        
        $notfiyOutput = array();
        
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        
        $result = $this->save($data);
        if($result === true){
            return true;
        }else{
            $msg = $result;
            return false;
        }
    }
    
    /**
     * 保存数据
     * @param unknown $data
     * @return string|boolean
     */
    private function save($data){
        $Model = M('member_recharge');
        $Model->startTrans();
        // 更新用户关注状态
        $subscrib = $data['is_subscribe'] == 'Y' ? 1 : 0;
        $Model->execute("UPDATE wx_user SET subscribe={$subscrib} WHERE openid='{$data['openid']}'");
        
        // 查询订单
        $tid = $data['out_trade_no'];
        $recharge = $Model->find($tid);
        
        if(empty($recharge)){
            return '订单不存在';
        }else if($recharge['status'] != 'topay'){
            return '订单状态：'.$recharge['status'];
        }
        
        //修改订单状态
        $Model->execute("UPDATE member_recharge SET `status`='success' WHERE tid='{$tid}'");
        //修改代理等级
        $Model->execute("UPDATE member SET agent_level={$recharge['agent_level']} WHERE id={$recharge['buyer_id']}");
        
        $list = json_decode($recharge['detail'], true);
        $BalanceModel = D('Balance');
        foreach($list as $level=>$item){
            if($item['money'] <= 0){
                continue;
            }
            
            $balance = $item['id'] == $recharge['buyer_id'] ? 
                array(
                    'mid'        => $item['id'],
                    'reason'     => '升级系统赠送',
                    'type'       => 'agent_up',
                    'no_balance' => $item['money']
                ) : 
                array(
                    'mid'        => $item['id'],
                    'reason'     => $level.'级好友冲值升级',
                    'balance'    => $item['money'],
                    'type'       => 'agent_tj'
                );
            
            $BalanceModel->add($balance);
        }
        $Model->commit();
        return true;
    }
}
?>