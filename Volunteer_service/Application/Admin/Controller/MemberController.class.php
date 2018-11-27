<?php
namespace Admin\Controller;

use Common\Common\CommonController;

/**
 * 会员管理
 *
 */
class MemberController extends CommonController
{
    public function index(){
        if(IS_AJAX){
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 50);
            
            $where = "";
            
            if($_GET['name'])//姓名
                $where .= " AND m.nickname like '%".addslashes($_GET['name'])."%'";
            if($_GET['nickname'])//昵称
                $where .= " AND w_u.nickname like '%".addslashes($_GET['nickname'])."%'";
            if(is_numeric($_GET['mobile']))//联系电话
                $where .= " AND m.mobile='".addslashes($_GET['mobile'])."'";
            if($_GET['wx_no'])//微信号
            	$where .= " AND m.wx_no like '%".addslashes($_GET['wx_no'])."%'";
            
            if($where != ''){
                $where = "WHERE ".ltrim($where, ' AND');
            }

            $Model = M();
            $rowstotal =$Model->query("SELECT count(*) AS count
                                     FROM member AS m
                                     INNER JOIN wx_user AS w_u ON m.id=w_u.mid ".$where);
            $total = $rowstotal[0]["count"];
            
            if($total == 0){
                $this->ajaxReturn(array(
                    "total" => $total,
                    "rows" => array()
                ));
            }
            
            $rows = $Model->query("SELECT 
                                    m.id,m.nickname as name,w_u.nickname,w_u.province,w_u.city,w_u.country,
                                    FROM_UNIXTIME(m.reg_time, '%Y-%m-%d %H:%i') as reg_time,
                                    m.sex,m.mobile, m.wx_no
                                FROM member AS m
                                INNER JOIN wx_user AS w_u ON m.id=w_u.mid
                                {$where}
                                ORDER BY m.id DESC
                                LIMIT {$offset},{$limit}");
            
            $this->ajaxReturn(array(
                "total" => $total,
                "rows" => $rows
            ));
        }
        
        $this->display();
    }
    
    
        
}
?>