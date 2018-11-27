<?php
namespace Admin\Controller;

use Common\Common\CommonController;
/**
 * 老干部信息列表
 * 
 * @author 
 *        
 */
class TeacheruserController extends CommonController
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
            $total = M('teacher')
                ->alias("t")
                ->join("academic AS aca ON t.academic_id = aca.id")
                ->count();
            if($total == 0){
                $this->ajaxReturn(array(
                    "total" => $total,
                    "rows" => array()
                ));
            }
            $rows = M('teacher')
                ->alias("t")
                ->field("t.*,aca.name AS a_name")
                ->join("academic AS aca ON t.academic_id = aca.id")
                ->order('t.id desc')
                ->limit($offset,$limit)
                ->select();
            foreach ($rows as $k => $v) {
                $rows[$k]['m_a_time'] = intval($v['mutual_aid_time']/60).'小时'.($v['mutual_aid_time']%60).'分钟';
            }
            $data = array(
                "total" => $total,
                "rows" => $rows
            );

            $this->ajaxReturn($data);
        }
        $this->display();
    }

    public function importfile(){
        if(IS_POST){
            $this->saveimport();
        }
        $this->display();
    }

    public function saveimport(){
        set_time_limit(0);
        ob_end_clean();
        ob_implicit_flush(1);
        $url = I("url");
        if(empty($url)){
            $this->error('参数错误');
        }
        $lin_url = explode('http://'.$_SERVER['HTTP_HOST'].'/',$url);
        $filepath = $_SERVER['DOCUMENT_ROOT'].'/'.$lin_url['1'];
        $filepath = preg_replace('/\//', '\\', $filepath);
        // $filepath =  $_SERVER['DOCUMENT_ROOT'].'/upload/file/20180809/1533779069358454.xls';
        $data=$this->format_excel2array($filepath);

        if(!is_array($data)){
            $this->error($data);
        }
        foreach ($data as $k => $v) {
            $teacher = M("teacher")->where("mobile = '{$v['D']}'")->find();
            if(!empty($teacher)){
                $this->error($v['B']."已存在请勿重复导入！");
            }
            $data_all[$k] = array(
                'job_number' => $v['A'],
                'name' => $v['B'],
                'mobile' => $v['D'],
                'mutual_aid_time' => $v['F']*60,

            );
            if($v['C'] == '男'){
                $data_all[$k]['sex'] = 1;
            }else if($v['C'] == '女'){
                $data_all[$k]['sex'] = 2;
            }else{
                $data_all[$k]['sex'] = 3;
            }
            $academic = '';
            $academic = M("academic")->where("name = '{$v['E']}'")->getField("id");
            if(!empty($academic)){
                $data_all[$k]['academic_id'] = $academic;
            }else{
                $add_academic['name'] = $v['E'];
                $add_academic['created'] = date("Y-m-d H:i:s");
                $academic = M("academic")->add($add_academic);
                if($academic > 0){
                    $data_all[$k]['academic_id'] = $academic;
                }else{
                    $this->error($v['B']."学院信息有误，请核对后再导入！");
                }
            }
            $data_all[$k]['add_time'] = date("Y-m-d H:i:s");
        }
        foreach ($data_all as $k => $v) {
            $data_all_xin[$v['job_number']] = $v;
        }
        $xin_data = array_values($data_all_xin);
        $res = M("teacher")->addAll($xin_data);
        if($res > 0){
            $this->success('导入老干部信息成功','/admin/teacheruser/index',3);
        }else{
            $this->error('导入老干部信息失败');
        }
    }

     //excel转成数组
    function format_excel2array($filePath='',$sheet=0){
        Vendor('PHPExcel.PHPExcel');
        if(empty($filePath) or !file_exists($filePath)){
            return 'file not exists';
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();        //建立reader对象
        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                return 'no Excel';
            }
        }
        $PHPExcel = $PHPReader->load($filePath);        //建立excel对象
        $currentSheet = $PHPExcel->getSheet($sheet);        //**读取excel文件中的指定工作表*/
        $allColumn = $currentSheet->getHighestColumn();        //**取得最大的列号*/
        if(ord($allColumn) < ord('F')){
            return '导入文件格式不符';
        }
        $allRow = $currentSheet->getHighestRow();        //**取得一共有多少行*/
        $data = array();
        for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){        //循环读取每个单元格的内容。注意行从2开始，列从A开始
            for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;

                $cell = $currentSheet->getCell($addr)->getFormattedValue();
                if($cell instanceof PHPExcel_RichText){ //富文本转换字符串
                    $cell = $cell->__toString();
                }
                if(empty($cell) && $colIndex != 'F'){
                    continue;
                }else{
                    $data[$rowIndex][$colIndex] = $cell;
                }
    
            }
        }
        return $data;
    }
}
?>