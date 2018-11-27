<?php
namespace H5\Controller;

use Common\Common\CommonController;

class StudentController extends CommonController
{
	// 学生端个人中心页
	public function index(){
		$user = $this->user();
		$data = M("student")
			->alias("t")
			->field("wu.headimgurl,t.school_number,t.name,t.sex,t.mobile,t.mutual_aid_time,aca.name AS a_name")
			->join("wx_user AS wu ON t.mid = wu.mid")
			->join("academic AS aca ON t.academic_id = aca.id")
			->where("t.mid = %d",$user['id'])
			->find();
		if(!empty($data)){
			$data['m_a_time'] = intval($data['mutual_aid_time']/60).'小时'.($data['mutual_aid_time']%60).'分钟';
			if(empty($data['headimgurl'])){
	            $data['headimgurl'] = "/img/weixin/no_image.png";
	        }
			$this->assign('data',$data);	
		}else{
			redirect("/h5/login/index");
		}
		$this->display();
	}
	// 心愿池
	public function wish_pool(){
		$user = $this->user();
		if(IS_AJAX){
			$student = M("student")
				->alias("t")
				->field("t.id,aca.id AS a_id")
				->join("academic AS aca ON t.academic_id = aca.id")
				->where("t.mid = %d",$user['id'])
				->find();
			
			$where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 10);
            $status = I('get.status', 0);
            // var_dump($status);die;
            if(!empty($status)){
            	$where['wish.level'] = array("eq" , $status);
            }
            // var_dump($student['a_id']);die;
            //if(isset($student['a_id']))
            	//$where['wish.academic_id'] = array("eq" , $student['a_id']);
            $where['wish.status'] = array("eq" , 1);
            $where['wish.status_p'] = array("eq", 1);
            // var_dump(isset($where['wish.level']));die;
            if(! isset($where['wish.level']))
            	$where['wish.level'] = array("NEQ", 8);
            $where['unix_timestamp(wish.end_time)'] = array("EGT" , time());
            $rows1 = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->select();//非其他类型的
	        // var_dump($rows1);die;
	        unset($where);
	        $where = array();
	        // $where['wish.academic_id'] = array("eq" , $student['a_id']);
            $where['wish.status'] = array("eq" , 1);
            $where['wish.status_p'] = array("eq", 1);
            // $where['wish.level'] = array("eq", 8);
            if(!empty($status)){
            	$where['wish.level'] = array("eq" , $status);
            }
            $where['wish.shenhe'] =array("eq", 1);
            $where['unix_timestamp(wish.end_time)'] = array("EGT" , time());
            $rows2 = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->select();//其他类型的！
			// var_dump($rows1);die;
	        $rows = array();
            $rows = array_merge($rows2,$rows1);

            foreach ($rows as $k => $v) {
            	$rows[$k]['end_time'] = substr($v['end_time'], 0,16);
            	$rows[$k]['level'] = $this->return_data($v['level']);
            	if(empty($v['wu_headimgurl'])){
            		$rows[$k]['wu_headimgurl'] = '/img/weixin/no_image.png';
            	}
            }
            $data = array();
            $data['rows'] = $rows;
            $this->ajaxReturn($data);
		}
		$this->display();
	}

	// 心愿列表页
	public function wish_list(){
		$user = $this->user();
		if(IS_AJAX){
			$student= M("student")
				->alias("t")
				->field("t.id,aca.id AS a_id")
				->join("academic AS aca ON t.academic_id = aca.id")
				->where("t.mid = %d",$user['id'])
				->find();
			$where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 10);
            $status = I('get.status', 1);
            // var_dump($status);die;
            $where['wish.status'] = array("eq" , $status);
            $where['wish.s_id'] = array("eq" , $student['id']);
            $rows = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->select();
            foreach ($rows as $k => $v) {
            	$rows[$k]['end_time'] = substr($v['end_time'], 0,16);
            	$rows[$k]['level'] = $this->return_data($v['level']);
            	$rows[$k]['duration'] = intval($v['duration']/60).'小时'.($v['duration']%60).'分钟';
            	if(empty($v['wu_headimgurl'])){
            		$rows[$k]['wu_headimgurl'] = '/img/weixin/no_image.png';
            	}
            }
            $data = array();
            $where_c = array();
            $data['rows'] = $rows;
            $where_c['s_id'] = array("eq" , $student['id']);
            $where_c['status'] = array("eq" , 2);
            $data['total_1'] = M("wish")->where($where_c)->count();
            $where_c['status'] = array("eq" , 3);
            $data['total_2'] = M("wish")->where($where_c)->count();
            $where_c['status'] = array("eq" , 4);
            $data['total_3'] = M("wish")->where($where_c)->count();
            $this->ajaxReturn($data);
		}
		$this->display();
	}
	// 我已完成
	public function finish(){
		$id = I("post.id");
		$data['finsh_time'] = date("Y-m-d H:i:s");
		$data['status'] = 3;
		
		// $result = M("wish")->where("id = %d",$id)->save($data);
		// if($result > 0){
		// 	$action['status'] = 1;
		// 	$this->ajaxReturn($action);
		// }
		// $this->error("提交完成失败！");
	}
	// 认领心愿
	public function claim(){
		$user = $this->user();
		$student = M("student")->where("mid = %d",$user['id'])->find();
		$id = I("post.id");
		$data['status'] = 2;
		$data['s_id'] = $student['id'];
		$result = M("wish")->where("id = %d",$id)->save($data);
		if($result > 0){
			$action['status'] = 1;
			$this->ajaxReturn($action);
		}
		$this->error("认领心愿失败！");
	}

	public function release_wish(){//发布心愿
		$user = $this->user();
		// var_dump($user);die;
		if(IS_AJAX){
			$student = M("student")
				->alias("s")
				->field("s.id,s.academic_id")
				->join("academic AS aca ON s.academic_id = aca.id")
				->where("s.mid = %d",$user['id'])
				->find();
				//print_data(M()->getlastsql());die;
			$level = I("post.level");
			$content = I("post.content");
			$end_time = I("post.end_time");
			//$status_p = I("post.status_p");
			if(empty($level)){
				$this->error("心愿类型不能为空！");
			}
			// if(empty($status_p)){
			// 	$this->error("求助类型不能为空！");
			// }
			$data = array(
				'level'           => $level,//传
				'content'         => $content,//传
				'end_time'        => $end_time,//传
				't_id'            => $student['id'],
				'status'          => 1,
				'academic_id'     => $student['academic_id'],
				'add_time'        => date("Y-m-d H:i:s")
			);
			$result = M("wish2")->add($data);
			//print_r($this->getlastsql());die;
			//echo($result);die;
			if($result > 0){
				$action['status'] = 1;
				$this->ajaxReturn($action);
			}
			$this->error("心愿发布失败！");
		}
		$date_time = time();
		$this->assign('date_time',$date_time);
		$this->display();
	}	

	public function delete(){
		$id = I("post.id");
		$result = M("wish2")->where("id = %d",$id)->delete();
		$action['status'] = 1;
		$this->ajaxReturn($action);
	}

	public function wish_release(){//我的发布
		$user = $this->user();
		//echo ("bughai");die;
		if(IS_AJAX){
			$student= M("student")
				->alias("t")
				->field("t.id,aca.id AS a_id")
				->join("academic AS aca ON t.academic_id = aca.id")
				->where("t.mid = %d",$user['id'])
				->find();
				// echo($student['id']);
			//echo $student['a_id'];//die;
				//$this->ajaxReturn($student['a_id']);die;
			
			$where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 10);
            $status = I('get.status', 1);
            // var_dump($status);
            $where['wish2.status'] = array("eq" , $status);
            $where['wish2.t_id'] = array("eq" , $student['id']);
            $rows = M("wish2")
            	->field("wish2.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("student AS s ON wish2.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish2.id desc")
	            ->select();
	        // print_r($rows);
            foreach ($rows as $k => $v) {
            	$rows[$k]['end_time'] = substr($v['end_time'], 0,16);
            	$rows[$k]['level'] = $this->return_data($v['level']);
            	$rows[$k]['duration'] = intval($v['duration']/60).'小时'.($v['duration']%60).'分钟';
            	if(empty($v['wu_headimgurl'])){
            		$rows[$k]['wu_headimgurl'] = '/img/weixin/no_image.png';
            	}
            }
            $data = array();
            $where_c = array();
            $data['rows'] = $rows;
            $where_c['s_id'] = array("eq" , $student['id']);
            $where_c['status'] = array("eq" , 2);
            $data['total_1'] = M("wish2")->where($where_c)->count();
            $where_c['status'] = array("eq" , 3);
            $data['total_2'] = M("wish2")->where($where_c)->count();
            $where_c['status'] = array("eq" , 4);
            $data['total_3'] = M("wish2")->where($where_c)->count();
            $this->ajaxReturn($data);
		}
		$this->display();
	}
}