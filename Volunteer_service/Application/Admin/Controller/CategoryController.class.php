<?php
namespace Admin\Controller;

use Common\Common\CommonController;

/**
 * 文章分类
 * 
 *        
 */
class CategoryController extends CommonController
{
    /**
     * 列表
     */
    public function index()
    {
        if (IS_AJAX) {
            $Model = M("category");
            $rows = $Model->select();
            foreach($rows as $k=>$v){
            	$article_num = M("article")->where("cat_id = %d",$v['id'])->count();
            	$rows[$k]['article_num'] = (!empty($article_num))?$article_num:0;
            }
            $this->ajaxReturn($rows);
        }
        $this->display();
    }
    
    /**
     * 添加分类
     */
    public function add(){
    	$Model = M("category");
        if(IS_POST){
            $data = I('post.','','htmlspecialchars');
            $name_rest = $Model->where(array('name'=>$data['name']))->find();
            if(!empty($name_rest)){
                $this->error('已存在该分类！');
            }
            $data['created'] = date('Y-m-d H:i:s');
            $result = $Model->add($data);
            if($result > 0){
                $this->success("添加成功");
            }
            $this->error('添加失败！');
        }
        $this->assign(array('data' => array()));
        $this->display('edit');
    }
    
    /**
     * 编辑
     */
    public function edit($id = 0){
    	$Model = M("category");
        if(IS_POST){
            $data = I('post.','','htmlspecialchars');
            $name_rest = $Model->where(array('name'=>$data['name']))->find();
            if(!empty($name_rest)){
                $this->error('已存在该分类！');
            }
            $result = $Model->save($data);
            if($result >= 0){
                $this->success('已修改！');
            }
            $this->error('修改失败！');
        }
        
        if(!is_numeric($id) || $id <= 0){
        	$this->error('数据ID异常！');
        }
        $data = $Model->find($id);
        if(empty($data)){
            $this->error('数据不存在或已被删除！');
        }
        $this->assign(array('data' => $data ));
        $this->display();
    }
    
    /**
     * 删除分类
     */
	public function delete($id = 0){
		$Model = M("category");
		
		$exist_article = M("article")->where("cat_id in(".$id.")")->select();
		if(!empty($exist_article)){
			$this->error('已选分类下存在文章，不能删除分类');
		}
		
		$result = $Model->query("DELETE FROM category WHERE id in ($id)");
		$data1 = $Model->where("id in(".$id.")")->select();
		if($data1){
			$this->success('删除失败！');
		}
		$this->success('删除成功！');
	}
}

?>