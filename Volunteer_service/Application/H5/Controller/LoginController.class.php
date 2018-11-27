<?php
namespace H5\Controller;

use Common\Common\CommonController;

class LoginController extends CommonController
{
	public function index(){
		$user = $this->user();
        if(IS_AJAX){
            $username = I("post.username");
            $password = I("post.password");
            if(empty($username)){
                $this->error("用户名不能为空！");
            }
            if(empty($password)){
                $this->error("密码不能为空！");
            }
            $teacher = M("teacher")->where("job_number = '".$username."'")->order("id DESC")->find();
            $student = M("student")->where("school_number = '".$username."'")->order("id DESC")->find();
            if(empty($teacher) && empty($student)){
                $this->error("对不起该您的工号/学号还未导入后台！");
            }
            if(!empty($teacher)){
	            if($teacher['mobile'] != $password){
	                $this->error("对不起密码不正确！");
	            }
	            if(!empty($teacher['mid']) && $teacher['mid'] != $user['id']){
	                $this->error("该工号已被其他账户绑定!");
	            }
	            //首次登录，更新teacher信息
	            $res = $this->update_info('teacher',$user['id'],$username);
	            $action['url'] = '/h5/teacher/index';
            }else{
            	if($student['mobile'] != $password){
	                $this->error("对不起密码不正确！");
	            }
	            if(!empty($student['mid']) && $student['mid'] != $user['id']){
	                $this->error("该学号已被其他账户绑定!");
	            }
	            //首次登录，更新student信息
	            $res = $this->update_info('student',$user['id'],$username);
	            $action['url'] = '/h5/student/index';
            }

            if($res['status'] == 0){
                $this->error($res['err_msg']);
            }
            $action['status'] = 1;
            $this->ajaxReturn($action);
        }
        //$redirect = I("get.redirect","");
        //$this->assign('redirect_url'  , $redirect);
        $this->display();
	}

	private function update_info($table,$mid,$username){
        $data = array('status' => 1,'err_msg'=>'');
        $userinfo = M('member')->where("id = %d",$mid)->find();
        if(!empty($userinfo)){
            $add_data['mid'] = $mid;
            if($table == 'student'){
            	M('student')->where("school_number=%d",$username)->save($add_data);
            }else{
            	M('teacher')->where("job_number=%d",$username)->save($add_data);
            }
            return $data;
        }else{
            $data['status'] = 0;
            $data['err_msg'] = '账号不存在';
            return $data;
        }
    }

}