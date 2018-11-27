<?php
namespace Admin\Controller;

use Common\Common\CommonController;
/**
 * 紧急心愿
 * 
 * @author 
 *        
 */
class ShenheteacherController extends CommonController
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 列表
     */
    public function index(){
        if (IS_AJAX) {
            $where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 50);
            $t_where['unix_timestamp(end_time)'] = array("LT",time());
            //$t_where['shenhe'] = array("eq" , 0);
            $tep = M("wish")->where($t_where)->field("id")->select();

            foreach ($tep as $k => $v) {
                $id .= ','.$v['id'];//$id=$id.,id; 
            }
            //var_dump($id);die;
            $id = ltrim($id,',');
            $where['wish.id'] = array("NOT IN",$id);
            $where['wish.status'] = array("eq", 1);
            $where['wish.status_p'] = array('eq',1);
            $where['wish.level'] = array("eq", 8);
            $where['shenhe'] = array("eq",0);
            $total = M('wish')
                ->join("teacher AS t ON wish.t_id = t.id")
                ->where($where)
                ->count();
            $rows = M('wish')
                ->field("wish.*,t.name AS t_name,t.mobile AS t_mobile")
                ->join("teacher AS t ON wish.t_id = t.id")
                ->order('wish.status,wish.end_time')
                ->where($where)
                ->limit($offset,$limit)
                ->select();
            foreach ($rows as $k => $v) {
                $rows[$k]['level'] = $this->return_data($v['level']);
                $rows[$k]['end_time'] = substr($v['end_time'], 0,16);
                if($v['status'] == 0){
                    $rows[$k]['tpye'] = 2; // 灰色可分配
                }else{
                    $rows[$k]['tpye'] = 1; // 不可分配心愿
                }
            }
            $data = array(
                "total" => $total,
                "rows" => $rows
            );
            $this->ajaxReturn($data);
        }
        $this->display();

    }

    public function shenhe(){
        
            $id = I('post.id');
            //$t_id =
            $flag = I('post.flag');
            //var_dump($id);die;
            if(empty($id))
                $this->error('未指定心愿id');
            if(empty($flag))
                $this->error('未选择审核状态');
            $where = array();
            $where['id'] = array("eq", $id);
            //$where['level'] = array("eq",8);
            $data = array();
            $data['shenhe'] = $flag;
            
            $result = M('wish')
                ->where($where)
                ->save($data);
                //print_data(M()->getlastsql());die;
            if($result > 0){
                $action['status'] = 1;
                $this->ajaxReturn($action);
            }
            //return $data;
            $this->error("审核心愿失败！");
        

        
    }
  
}
?>