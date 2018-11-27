<?php
namespace H5\Controller;

use Common\Common\CommonController;

class TeacherController extends CommonController
{
	// 老师端个人中心页
	public function index(){
		$user = $this->user();
		$data = M("teacher")
			->alias("t")
			->field("wu.headimgurl,t.job_number,t.name,t.sex,t.mobile,t.mutual_aid_time,aca.name AS a_name")
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
	// 发布心愿页
	public function release_wish(){
		$user = $this->user();
		if(IS_POST){
			$teacher = M("teacher")
				->alias("t")
				->field("t.id,aca.id AS a_id")
				->join("academic AS aca ON t.academic_id = aca.id")
				->where("t.mid = %d",$user['id'])
				->find();
			$level = I("post.level");
			$content = I("post.content");
			$end_time = I("post.end_time");
			$status_p = I("post.status_p");
			//var_dump($teacher);die;
			if(empty($level)){
				$this->error("心愿类型不能为空！");
			}
			if(empty($status_p)){
				$this->error("求助类型不能为空！");
			}
			// if (empty($teacher['id'])) {
			// 	$this->error("jiaoshixinxiweikong");
			// }
			$data = array(
				'level'           => $level,
				'content'         => $content,
				'end_time'        => $end_time,
				't_id'            => $teacher['id'],
				'status'          => 1,
				'status_p'        => $status_p,
				'academic_id'     => $teacher['a_id'],
				'add_time'        => date("Y-m-d H:i:s")
			);
			$result = M("wish")->add($data);
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

	// 心愿列表页
	public function wish_list(){
		$user = $this->user();
		// echo(arg1);die;
		if(IS_AJAX){
			$teacher = M("teacher")
				->alias("t")
				->field("t.id,aca.id AS a_id")
				->join("academic AS aca ON t.academic_id = aca.id")
				->where("t.mid = %d",$user['id'])
				->find();
				// echo($teacher['id']);die;
			$where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 10);
            $status = I('get.status', 2);
            $where['wish.status'] = array("eq" , $status);
            $where['wish.s_id'] = array("eq" , $teacher['id']);
            // var_dump($status);die;
            if($status == 1){
            	$where['unix_timestamp(wish.end_time)'] = array("EGT" , time());
            }
            $rows1 = M("wish")
            	->field("wish.*,t.name AS t_name,t.mobile AS t_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS t ON wish.s_id = t.id")
            	->join("wx_user AS wu ON t.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->select();
	        // var_dump($rows1);die;
	        unset($where);
	        $where = array();
	        $where['wish2.status'] = array("eq" , $status);
            $where['wish2.s_id'] = array("eq" , $teacher['id']);
	        $rows2 = M("wish2")
	        	->field("wish2.*,t.name AS t_name,t.mobile AS t_mobile,wu.headimgurl AS wu_headimgurl")
	        	->join("teacher AS t ON wish2.s_id = t.id")
	        	->join("wx_user AS wu ON t.mid = wu.mid")
	        	->where($where)
	        	->limit($offset,$limit)
	        	->order("wish2.id desc")
	            ->select();
	         // var_dump($rows2);die;
	         $rows =array();
	         $rows = array_merge($rows1,$rows2);
	         // var_dump($rows);die;
            foreach ($rows as $k => $v) {
            	$rows[$k]['end_time'] = substr($v['end_time'], 0,16);
            	$rows[$k]['level'] = $this->return_data($v['level']);
            	$rows[$k]['duration'] = intval($v['duration']/60).'小时'.($v['duration']%60).'分钟';
            	if(empty($v['wu_headimgurl'])){
            		$rows[$k]['wu_headimgurl'] = '/img/weixin/no_image.png';
            	}
            }
            $data['rows'] = $rows;
            $where_c = array();
            $where_c['s_id'] = array("eq" , $teacher['id']);
            $where_c['status'] = array("eq" , 2);
            $where_c['shenhe'] = array("neq", 2);
            
            $to1 = M("wish")->where($where_c)->count();
            $to2 = M("wish2")->where($where_c)->count();
            $data['total_1'] = $to1 + $to2;
            unset($where_c['shenhe']);
            $where_c['status'] = array("eq" , 3);

            $to1 = M("wish")->where($where_c)->count();
            $to2 = M("wish2")->where($where_c)->count();
            $data['total_2'] = $to1 + $to2;
            $where_c['status'] = array("eq" , 4);

            $to1 = M("wish")->where($where_c)->count();
            $to2 = M("wish2")->where($where_c)->count();
            

            $data['total_3'] = $to1 + $to2;
            $this->ajaxReturn($data);
		}
		$this->display();
	}
	// 评价
	public function evaluate(){
		$evaluate = I("post.evaluate");
		$duration = I("post.duration");
		$id = I("post.id");
		if(empty($duration)){
			$this->error("心愿完成时长不能为空！");
		}
		$save_data['evaluate'] = $evaluate;
		$save_data['duration'] = $duration;
		$save_data['status'] = 4;
		$result = M("wish")->where("id = %d",$id)->save($save_data);
		if($result > 0){
			$wish = M("wish")
				->field("t.id AS t_id,t.mutual_aid_time AS t_mutual_aid_time,s.id AS s_id,s.mutual_aid_time AS s_mutual_aid_time")
				->join("teacher AS t ON wish.t_id = t.id")
				->join("student AS s ON wish.s_id = s.id")
				->where("wish.id = %d",$id)
				->find();
			// $save_teacher['mutual_aid_time'] = $wish['t_mutual_aid_time']-$save_data['duration'];
			// M("teacher")->where("id = %d",$wish['t_id'])->save($save_teacher);
			$evaluate = $evaluate.'0';
			switch ($evaluate) {
				case '40':
					$save_student['mutual_aid_time'] = $wish['s_mutual_aid_time']+$save_data['duration'];
					break;
				
				case '30':
					$save_student['mutual_aid_time'] = $wish['s_mutual_aid_time']+($save_data['duration']*0.8);
					break;

				case '20':
					$save_student['mutual_aid_time'] = $wish['s_mutual_aid_time']+($save_data['duration']*0.5);
					break;

				case '10':
					$save_student['mutual_aid_time'] = $wish['s_mutual_aid_time']+($save_data['duration']*0.3);
					break;
			}
			
			M("student")->where("id = %d",$wish['s_id'])->save($save_student);
			$action['status'] = 1;
			$this->ajaxReturn($action);
		}
		$this->error("评价失败！");
	}

	// 取消心愿
	public function delete(){
		$id = I("post.id");
		$result = M("wish")->where("id = %d",$id)->delete();
		$action['status'] = 1;
		$this->ajaxReturn($action);
	}
	//教师心愿列表
	public function wish_pool(){
		$user = $this->user();
		if(IS_AJAX){
			// $teacher = M("teacher")
			// 	->alias("t")
			// 	->field("t.id,aca.id AS a_id")
			// 	->join("academic AS aca ON t.academic_id = aca.id")
			// 	->where("t.mid = %d",$user['id'])
			// 	->find();
			//var_dump(expression);die;
			$where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 10);
            $status = I('get.status', 1);
            if(!empty($status)){
            	$where['wish.level'] = array("eq" , $status);
            }
            //$where['wish.academic_id'] = array("eq" , $teacher['a_id']);
            // $where['wish.status'] = array("eq" , 1);
            $where['unix_timestamp(wish.end_time)'] = array("EGT" , time());
            $where['wish.status_p'] = array("eq" ,2);//求助老师的全部
            // $where['wish.shenhe'] = array('neq',2);
            $count1 = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->count();//教师
	        // var_dump($rows1);die;
            $rows1 = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->select();//教师发布的求助老师的全部心愿
	        unset($where);//教师发布的求助学生的其他
	        // var_dump($rows1);die;
            $where = array();
            // $where['wish.academic_id'] = array("eq" , $teacher['a_id']);
            $where['wish.status'] = array("eq" , 1);
            $where['unix_timestamp(wish.end_time)'] = array("EGT" , time());
            $where['wish.status_p'] = array("eq" ,1);
            if(!empty($status)){
            	$where['wish.level'] = array("eq" , $status);
            }
	        // $where['wish.shenhe'] = array("eq" ,1);//1是通过
	        // var_dump($rows1);die;
	        $count2 = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->count();//教师
	            // var_dump($count2);die;
            $rows2 = M("wish")
            	->field("wish.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("teacher AS s ON wish.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish.id desc")
	            ->select();
	        foreach ($rows2 as $i => $value) {
	        	# code...
	        	if ($value['level'] == 8 && $value['shenhe'] != 1) {
	        		# code...
	        		unset($rows2[$i]);
	        		$count2--;
	        	}
	        }
	   //      $student = M("student")
				// ->alias("s")
				// ->field("s.id,aca.id AS a_id")
				// ->join("academic AS aca ON s.academic_id = aca.id")
				// ->where("s.mid = %d",$user['id'])
				// ->find();
			unset($where);
			$where = array();
			// $where['wish2.academic_id'] = array("eq" , $student['a_id']);
            $where['wish2.status'] = array("eq" , 1);
            $where['shenhe'] = 1;
            $where['unix_timestamp(wish2.end_time)'] = array("EGT" , time());
            //$where['wish2.status_p'] = array("eq" ,2);
            if( !empty($status)){
            	$where['wish2.level'] = array("eq" , $status);
            }
	        // $where['wish2.shenhe'] = array("eq" ,1);//1是通过
	        $count3 = M("wish2")
	        	->field("wish2.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")//
	        	->join("student AS s ON wish2.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish2.id desc")
	            ->count();
	        $rows3 = M("wish2")
	        	->field("wish2.*,s.name AS s_name,s.mobile AS s_mobile,wu.headimgurl AS wu_headimgurl")//
	        	->join("student AS s ON wish2.t_id = s.id")
            	->join("wx_user AS wu ON s.mid = wu.mid")
	            ->where($where)
	            ->limit($offset,$limit)
	            ->order("wish2.id desc")
	            ->select();//学生发布的心愿信息
            // var_dump($rows3);die;

	        $rows = array_merge($rows1 ,$rows2,$rows3);
            foreach ($rows as $k => $v) {
            	$rows[$k]['end_time'] = substr($v['end_time'], 0,16);
            	$rows[$k]['level'] = $this->return_data($v['level']);
            	if(empty($v['wu_headimgurl'])){
            		$rows[$k]['wu_headimgurl'] = '/img/weixin/no_image.png';
            	}
            }
            $data=array();
            $data['count'] = $count1+$count2+$count3;
            $data['rows'] = $rows;
            // var_dump($data);die;
            $this->ajaxReturn($data);
		}
		
		$this->display();
	}

	// 我已完成
	public function finish(){
		$id = I("post.id");
		$data['finsh_time'] = date("Y-m-d H:i:s");
		$data['status'] = 3;
		$result = M("wish2")->where("id = %d",$id)->save($data);
		if($result > 0){
			$action['status'] = 1;
			$this->ajaxReturn($action);
		}
		$this->error("提交完成失败！");
	}

	public function claim(){
		$user = $this->user();
		$teacher = M("teacher")->where("mid = %d",$user['id'])->find();
		$id = I("post.id");
		// var_dump($id);die;
		$s_mobile = I("post.s_mobile");//die;
		//var_dump($s_mobile);die;
		$teacher1 = M("teacher")->where("mobile = '%s'",$s_mobile)->find();
		// var_dump($teacher1);die;
		$flag = 0;//教师表
		if(empty($teacher1))
		{
			$flag = 1;//学生表
			$student = M("student")->where("mobile = %d",$s_mobile)->find();
		}
		if($flag == 0){
			$data = array();
			$data['status'] = 2;
			$data['s_id'] = $teacher1['id'];
			// var_dump(expression);die;
			$result = M("wish")->where("id = %d",$id)->save($data);
			if($result > 0){
				$action['status'] = 1;
				$this->ajaxReturn($action);
			}
		}else if($flag == 1){
			$data = array();
			$data['status'] = 2;
			$data['s_id'] = $teacher['id'];
			// var_dump(expression1);die;
			$result = M("wish2")->where("id = %d",$id)->save($data);
			if($result > 0){
				$action['status'] = 1;
				$this->ajaxReturn($action);
			}
		}
		
		$this->error("认领心愿失败！");
	}
	//我的发布
	public function wish_release(){
		$user = $this->user();
		if(IS_AJAX){
			$teacher = M("teacher")
				->alias("t")
				->field("t.id,aca.id AS a_id")
				->join("academic AS aca ON t.academic_id = aca.id")
				->where("t.mid = %d",$user['id'])
				->find();
			$where = array();
            $offset = I('get.offset', 0);
            $limit = I('get.limit', 10);
            $status = I('get.status', 1);
            $where['wish.status'] = array("eq" , $status);
            $where['wish.t_id'] = array("eq" , $teacher['id']);
            if($status == 1){
            	$where['unix_timestamp(wish.end_time)'] = array("EGT" , time());
            }
            $rows = M("wish")
            	->field("wish.*,t.name AS t_name,t.mobile AS t_mobile,wu.headimgurl AS wu_headimgurl")
            	->join("student AS t ON wish.s_id = t.id")
            	->join("wx_user AS wu ON t.mid = wu.mid")
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
            $data['rows'] = $rows;
            $where_c = array();
            $where_c['t_id'] = array("eq" , $teacher['id']);
            $where_c['status'] = array("eq" , 1);
            $where_c['shenhe'] = array("eq", 0);
            // $where_c['']
            $data['total_1'] = M("wish")->where($where_c)->count();
            unset($where_c['shenhe']);
            $where_c['status'] = array("eq" , 2);
            $data['total_2'] = M("wish")->where($where_c)->count();
            $where_c['status'] = array("eq" , 3);
            $data['total_3'] = M("wish")->where($where_c)->count();
            $this->ajaxReturn($data);
		}
		$this->display();
	}
}