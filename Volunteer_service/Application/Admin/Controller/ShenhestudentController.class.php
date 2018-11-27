<?php
namespace Admin\Controller;

use Common\Common\CommonController;
/**
 * 紧急心愿
 * 
 * @author 
 *        
 */
class ShenhestudentController extends CommonController
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
            //$data = $this->gat_data();
            $where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 50);
            $t_where['unix_timestamp(end_time)'] = array("EGT",time());
            $t_where['shenhe'] = array("eq" , 0);
            $tep = M("wish2")->where($t_where)->field("wish2.id")->select();

            foreach ($tep as $k => $v) {
                $id .= ','.$v['id'];//$id=$id.,id; 
            }
            $id = ltrim($id,',');
            $where['wish2.id'] = array("in",$id);
            $where['wish2.status'] = array("eq", 1);
            //$where['wish2.status_p'] = array("eq", 2);
            $total = M('wish2')
                ->join("student AS s ON wish2.t_id = s.id")
                ->where($where)
                ->count();
            $rows = M('wish2')
                ->field("wish2.*,s.name AS t_name,s.mobile AS t_mobile")
                ->join("student AS s ON wish2.t_id = s.id")
                ->order('wish2.status,wish2.end_time')
                ->where($where)
                ->limit($offset,$limit)
                ->select();
            // var_dump($rows);die;
            // print_data(M()->getlastsql());die;
            foreach ($rows as $k => $v) {
                $rows[$k]['level'] = $this->return_data($v['level']);
                $rows[$k]['end_time'] = substr($v['end_time'], 0,16);
                if($v['status'] == 0){
                    $rows[$k]['tpye'] = 2; // 灰色可分配
                }else{
                    $rows[$k]['tpye'] = 1; // 不可分配心愿
                }
            }
            // $total = count($rows);
            $data = array(
                "total" => $total,
                "rows" => $rows
            );
            //return $data;
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    public function shenhe(){
        //echo(arg1);die;
            $id = I('post.id');
            //$t_id =
            //var_dump($id);die;
            $flag = I('post.flag');
            //var_dump($flag);die;
            if(empty($id))
                $this->error('未指定心愿id');
            if(empty($flag))
                $this->error('未选择审核状态');
            $where = array();
            $where['id'] = array("eq", $id);
            $data = array();
            $data['shenhe'] = $flag;
            //$data = array('shenhe' => 1)
            $result = M('wish2')
                ->where($where)
                ->save($data);
                //print_data(M()->getlastsql());die;
            if($result > 0){
                $action['status'] = 1;
                $this->ajaxReturn($action);
            }
        $this->error("审核心愿失败！");
            //return $data;
            ///$this->ajaxReturn($data);
        

        
    }
  
}
?>