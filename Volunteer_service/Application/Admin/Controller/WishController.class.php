<?php
namespace Admin\Controller;

use Common\Common\CommonController;
/**
 * 紧急心愿
 * 
 * @author 
 *        
 */
class WishController extends CommonController
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
            $data = $this->gat_data();
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    /**
     * 批量导出
     */
    public function export(){
        $is_export = 1;
        $result = $this->gat_data($is_export);
        foreach ($result['rows'] as $k => $v) {
            switch ($v['status']) {
                case '1':
                    $result['rows'][$k]['status'] = '待认领';
                    break;
                default:
                    $result['rows'][$k]['status'] = '已认领';
                    break;
            }
        }
        $this->doExportList($result,iconv("UTF-8","gb2312", '汇总心愿'));
    }

    private function gat_data($is_export = 0){
        $where = array();
        $offset = I('get.offset', 0);
        $limit = I('get.limit', 50);
        $t_where['unix_timestamp(end_time)'] = array("LT",time());
        $t_where['status'] = array("eq" , 1);
        $tep = M("wish")->where($t_where)->field("id")->select();
        foreach ($tep as $k => $v) {
            $id .= ','.$v['id']; 
        }
        $id = ltrim($id,',');
        $where['wish.id'] = array("NOT IN",$id);
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
            if($v['status'] != 1){
                $rows[$k]['tpye'] = 1; // 灰色不可分配
            }else if((strtotime($v['end_time']) - time()) > 1800){
                $rows[$k]['tpye'] = 2; // 红色 点击弹窗提示
            }else{
                $rows[$k]['tpye'] = 3; // 蓝色 点击弹窗分配心愿
            }
        }
        $data = array(
            "total" => $total,
            "rows" => $rows
        );
        return $data;
    }

    public function search_student(){
        $val = I("post.val");
        // var_dump($_POST['val']);die;
        if(empty($val)){
            $this->error("学号不能为空！");
        }
        $data = M("student")->where("school_number = '{$val}'")->find();
        if(empty($data)){
            $data = array();
        }
        $this->ajaxReturn($data);
    }

    public function stribution(){
        $id = I("post.id");
        $s_id = I("post.s_id");
        $data['status'] = 2;
        $data['s_id'] = $s_id;
        $result = M("wish")->where("id = %d",$id)->save($data);
        if($result > 0){
            $action['status'] = 1;
            $this->ajaxReturn($action);
        }
        $this->error("认领心愿失败！");
    }
    
    private function doExportList($data,$name){
        Vendor('PHPExcel.PHPExcel');//加载类库
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new \PHPExcel();
        //设置宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        //加粗
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        // 以下就是对处理Excel里的数据
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '心愿类型')
            ->setCellValue('B1', '心愿内容')
            ->setCellValue('C1', '心愿截止时间')
            ->setCellValue('D1', '发布人')
            ->setCellValue('E1', '发布人联系方式')
            ->setCellValue('F1', '待认领状态');
        $num = 1;
        if(empty($data['rows'])){
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:F2');
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', '暂无记录!');
        }else{
            foreach($data['rows'] as $k => $v){
                $num++;
                $objPHPExcel->getActiveSheet()->getStyle('A'.$num.':E'.$num)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$num.':E'.$num)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $v['level'])
                    ->setCellValue('B'.$num, $v['content'])
                    ->setCellValue('C'.$num, $v['end_time'])
                    ->setCellValue('D'.$num, $v['t_name'])
                    ->setCellValue('E'.$num, $v['t_mobile'])
                    ->setCellValue('F'.$num, $v['status']);
            }
        }
        
        $objPHPExcel->getActiveSheet()->setTitle('Sheet');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
    
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
?>