<?php
namespace Admin\Controller;

use Common\Common\CommonController;
/**
 * 学生信息列表
 * 
 * @author 
 *        
 */
class StudentuserController extends CommonController
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
        $academic = M("academic")->select();
        $this->assign(array(
            'academic'     => $academic
            ));
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
            $teacher = M("student")->where("mobile = '{$v['D']}'")->find();
            if(!empty($teacher)){
                $this->error($v['B']."已存在请勿重复导入！");
            }
            $data_all[$k] = array(
                'school_number' => $v['A'],
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
            $data_all_xin[$v['school_number']] = $v;
        }
        $xin_data = array_values($data_all_xin);
        $res = M("student")->addAll($xin_data);
        if($res > 0){
            $this->success('导入学生信息成功','/admin/studentuser/index',3);
        }else{
            $this->error('导入学生信息失败');
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
        if(ord($allColumn) < ord('E')){
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
                if(empty($cell) && $colIndex != 'E'){
                    continue;
                }else{
                    $data[$rowIndex][$colIndex] = $cell;
                }
    
            }
        }
        return $data;
    }

    /**
     * 批量导出
     */
    public function export(){
        $is_export = 1;
        $result = $this->gat_data($is_export);
        foreach ($result['rows'] as $k => $v) {
            switch ($v['sex']) {
                case '1':
                    $result['rows'][$k]['sex'] = '男';
                    break;
                case '2':
                    $result['rows'][$k]['sex'] = '女';
                    break;
                default:
                    $result['rows'][$k]['sex'] = '未知';
                    break;
            }
        }
        $this->doExportList($result,iconv("UTF-8","gb2312", '学生志愿总时长'));
    }

    private function gat_data($is_export = 0){
        $where = array();
        $offset = I('get.offset', 0);
        $limit = I('get.limit', 50);
        $name = I('get.name');
        $school_number = I('get.school_number');
        $sex = I('get.sex');
        $mobile = I('get.mobile');
        $academic_id = I('get.academic_id');
        if($is_export == 1){
            $data = I('get.data');
            $name = $data['name'];
            $school_number = $data['school_number'];
            $sex = $data['sex'];
            $mobile = $data['mobile'];
            $academic_id = $data['academic_id'];
        }

        if(!empty($name)){
            $where['t.name'] = array( 'like', '%' . addslashes($name) . '%' );
        }
        if(!empty($school_number)){
            $where['t.school_number'] = array( 'like', '%' . addslashes($school_number) . '%' );
        }
        if(!empty($sex)){
            $where['t.sex'] = array('eq', $sex);
        }
        if(!empty($mobile)){
            $where['t.mobile'] = array( 'like', '%' . addslashes($mobile) . '%' );
        }
        if(!empty($academic_id)){
            $where['t.academic_id'] = array('eq', $academic_id);
        }
        $total = M('student')
            ->alias("t")
            ->join("academic AS aca ON t.academic_id = aca.id")
            ->where($where)
            ->count();
        $rows = M('student')
            ->alias("t")
            ->field("t.*,aca.name AS a_name")
            ->join("academic AS aca ON t.academic_id = aca.id")
            ->where($where)
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
        return $data;
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
            ->setCellValue('A1', '学号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '性别')
            ->setCellValue('D1', '手机号')
            ->setCellValue('E1', '学院')
            ->setCellValue('F1', '志愿总时长');
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
                    ->setCellValue('A'.$num, $v['school_number'])
                    ->setCellValue('B'.$num, $v['name'])
                    ->setCellValue('C'.$num, $v['sex'])
                    ->setCellValue('D'.$num, $v['mobile'])
                    ->setCellValue('E'.$num, $v['a_name'])
                    ->setCellValue('F'.$num, $v['m_a_time']);
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