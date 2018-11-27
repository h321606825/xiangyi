<?php
namespace Admin\Controller;

use Common\Common\CommonController;
/**
 * 用户管理
 * 
 * @author 兰学宝
 *        
 */
class UserController extends CommonController
{

    private $Module;
    private $accessTable;
    private $roleTable;

    function __construct()
    {
        parent::__construct();
        $this->Module = M('users');
        $this->accessTable = C('AUTH_TABLE_ACCESS'); //bs_role_user
        $this->roleTable = C('AUTH_TABLE_ROLE');//bs_role
    }
    
    /**
     * 列表
     */
    public function index()
    {
        if (IS_AJAX) {
            $where = array();
            if (strlen($_GET['nick']) > 0) { $where['users.nick'] = array( 'like', '%' . $_GET['nick'] . '%' ); }
            if (strlen($_GET['username']) > 0) { $where['users.username'] = array( 'like', '%' . $_GET['username'] . '%'); }
            if(is_numeric($_GET['role'])){ $where['role.id'] = $_GET['role'];  }
            if(is_numeric($_GET['shop_id'])){ $where['users.shop_id'] = $_GET['shop_id'];  }
            
            $rows = $this->Module
                         ->alias('users')
                         ->field("users.id, users.nick, users.username, users.status,group_concat(role.name ORDER BY role.id ASC) AS role_name,shop.name AS shop_name")
                         ->join($this->accessTable." AS access ON access.user_id=users.id")
                         ->join($this->roleTable." AS role ON role.id=access.role_id")
                         ->join("shop ON users.shop_id=shop.id")
                         ->where($where)
                         ->group("users.id")
                         ->order("users.shop_id, users.id")
                         ->select();

            $this->ajaxReturn($rows);
        }
        $shop = $this->shops();
        $this->assign(array(
            'role_list'     => $this->roleList(),
            'shop'         => $shop
        ));
        $this->display();
    }
    
    /**
     * 获取角色列表
     */
    private function roleList(){
       return $this->Module->query("SELECT id, `name`, `status` FROM ".$this->roleTable);
    }
    
    /**
     * 添加用户
     */
    public function add(){
        if(IS_POST){
            $data = $_POST['data'];
            $exists = $this->Module->where("username='{$data['username']}'")->count();
            if(!empty($exists)){
                $this->error('该账号已存在！');
            }
            
            $data['password'] = md5($data['password']);
            $result = $this->Module->add($data);
            if($result > 0){
                $this->success('添加成功！');
            }
            $this->error('添加失败！');
        }
        
        $shop = $this->shops();
        $my_shop = $this->user('shop_id');
        
        $this->assign(array(
            'shop'    =>  $shop,
            'my_shop' =>    $my_shop
        ));
        $this->display();
    }
    
    /**
     * 编辑用户
     */
    public function edit($id = 0){
        if(IS_POST){
            $data = $_POST['data'];
            $sid = $_POST['sid'];
            $aid = implode(',', $sid);
            $data['id'] = intval($data['id']);
            $data['association_id'] = $aid;
            $password2 = I('post.password2','');
            
            if($data['id'] <= 0){
                $this->error('数据ID异常！');
            }
            
            $exists = $this->Module->where("username='{$data['username']}' AND id != '{$data['id']}'")->count();
            if(!empty($exists)){
            	$this->error('该账号已存在！');
            }
            
            if(!empty($data['password']) && !empty($password2)){
            	if($data['password'] != $password2){
            		$this->error('两次密码输入不一致！');
            	}
            	$data['password'] = md5($data['password']);
            }else{
            	unset($data['password']);
            }
            
            $result = $this->Module->save($data);
            if($result >= 0){
                $this->success('已修改！');
            }
            $this->error('修改失败！');
        }
        
        $data = $this->Module->find($id);
        if(empty($data)){
            $this->error('数据不存在或已被删除！');
        }
        
        $mySid = M('users')->field('association_id')->find($id);
        $sid = explode(',',$mySid['association_id']);
        $shop = $this->shops();
        $this->assign(array(
            'sid'     => $sid,
            'data'    => $data,
            'shop'    => $shop
            ));
        $this->display();
    }
    
    /**
     * 删除用户
     */
    public function delete($id = 0){
        if(empty($id)){
            $this->error('删除项不能为空！');
        }
        $result = $this->Module->delete($id);
        if($result > 0){
            $this->Module->execute("DELETE FROM ".$this->accessTable." WHERE user_id in (".$id.")");
        }
        $this->success('删除成功！');
    }
    
    /**
     * 授权
     */
    public function role(){
        $user_id = I('get.id/d', 0);
        if(IS_GET){
            if(!is_numeric($user_id) || $user_id < 1){
                $this->error('请选择授权用户！');
            }
        }else{
            $user_id = $_POST['user_id'];
        }
        
        if(IS_POST){
            $sql = "";
            $sqlin = "";
            if(empty($_POST['role_id'])){
                $sql = "DELETE FROM ".$this->accessTable." WHERE user_id='{$user_id}';";
            }else{
                $my_role = $this->myRoleList($user_id);

                // 要删除的
                $delete = array_diff($my_role, $_POST['role_id']);
                if(count($delete) > 0){
                    $sql .= "DELETE FROM ".$this->accessTable." WHERE user_id='{$user_id}' AND role_id IN(".implode(',', $delete).");";
                }
                
                // 要添加的
                $add = array_diff($_POST['role_id'], $my_role);
                if(count($add) > 0){
                    $sqlin .= "INSERT INTO ".$this->accessTable."(user_id, role_id) VALUES";
                    $values = "";
                    foreach ($add as $role_id){
                        $values .= "({$user_id}, {$role_id}),";
                    }
                    $sqlin .= trim($values, ',');
                }
            }

            if($sql != ''){
                $this->Module->execute($sql);
            }
            if($sqlin != ''){
                $this->Module->execute($sqlin);
            }
            $this->success('已保存！');
        }
        
        $my_role = $this->myRoleList($user_id);
        $role_list = $this->Module->query("SELECT id, `name`, `status` FROM ".$this->roleTable);
   
        $this->assign(array('user_id' =>$user_id, 'role_list' => $role_list, 'my_role' => $my_role));
        $this->display();
    }
    
    /**
     * 获取我的角色集合
     */
    private function myRoleList($user_id){
        $my_role = $this->Module->query("SELECT role_id FROM ".$this->accessTable." WHERE user_id=".$user_id);
        $list = array();
        foreach($my_role as $item){
            $list[] = $item['role_id'];
        }
        return $list;
    }
    
    /**
     * 修改密码
     * @param number $user_id
     */
    public function password(){
        $user_id = I('get.id');
        if(IS_POST){
            $password = I('post.password');
            if($password == ''){
                $this->error('新密码不能为空！');
            }
            if($password != $_POST['password2']){
                $this->error('两次密码不一致！');
            }
            
            $data = array();
            $data['id'] = I('post.id/d', 0);
            $data['password'] = md5($password);
            $this->Module->save($data);
            
            $this->success('修改成功！');
        }
        
        $data = $this->Module->field("id, username, nick")->find($user_id);
        if(empty($data)){
            $this->error('用户不存在或已被删除！');
        }
        $this->assign('data', $data);
        $this->display();
    }
}

?>