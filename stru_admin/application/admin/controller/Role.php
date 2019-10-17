<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Cache;

class Role extends Controller
{
    protected $middleware = [
        'Check'
    ];
    /**
     * 获取角色列表
     * @return [type] [description]
     */
    public function list(){
        $list = db('roles')
            ->field('*')
            ->select();
        return show(600,'',$list);
    }
    /**
     * 添加角色
     */
    public function add(){
        $input = input();
        $id = isset($input['form']['id']) ? $input['form']['id'] : '';
        $role_name = isset($input['form']['role_name']) ? $input['form']['role_name'] : '';
        $role_slug = isset($input['form']['role_slug']) ? $input['form']['role_slug'] : '';
        if(empty($role_name) || empty($role_slug)){
            return show(601,'参数错误',[]);
        }
        if(empty($id)){
            $has_role = db('roles')
                ->field('id')
                ->where('role_slug',$role_slug)
                ->whereOr('role_name',$role_name)
                ->find();
            if($has_role){
                return show(601,'角色名或已存在',[]);
            }
            $data['role_name'] = $role_name;
            $data['role_slug'] = $role_slug;
            $data['create_at'] = date('Y-m-d H:i:s',time());
            $data['update_at'] = date('Y-m-d H:i:s',time());
            $ret = db('roles')->insert($data);
            if($ret){
                return show(600,"添加成功",[]);
            }else{
                return show(601,"添加失败，请稍后重试！",[]);
            }
        }else{
            $role_name['role_name'] = $role_name;
            $role_slug['role_slug'] = $role_slug;
            $has_role = db('roles')
                ->field('id')
                ->where('id','<>',$id)
                ->where('role_slug',$role_slug)
                ->where(function($query) use ($role_name,$role_slug) {
                    $query->whereOr($role_name);
                    $query->whereOr($role_slug);
                })
                ->find();
            if($has_role){
                return show(601,'角色名或已存在',[]);
            }
            $data['role_name'] = $role_name;
            $data['role_slug'] = $role_slug;
            $data['update_at'] = date('Y-m-d H:i:s',time());
            $ret = db('roles')->where('id',$id)->update($data);
            if($ret){
                    return show(600,"更新成功",[]);
                }else{
                    return show(601,"更新失败，请稍后重试！",[]);
                }
        }
    }

    /**
     * 角色编辑操作
     */
    public function edit(){
        $input = input();
        $id = isset($input['id']) ? $input['id'] : '';
        if(empty($id)){
            return show(601,'参数错误',[]);
        }
        $role = db('roles')->where('id',$id)->find();
        return show(600,'',$role);
    }

    /**
     * 角色删除操作
     */
    public function del(){
        $input = input();
        $id = isset($input['id']) ? $input['id'] : '';
        if(empty($id)){
            return show(601,'参数错误',[]);
        }
        $ret = db('roles')->where('id',$id)->delete();
        if($ret){
            return show(600,'删除成功',[]);
        }else{
            return show(601,'删除失败,请稍后重试',[]);
        }
    }

    /**
     * 获取角色所拥有的权限
     */
    public function rolePermission(){
        $input = input();
        $id = isset($input['id']) ? $input['id'] : '';
        $list = db('permissions')->order('index','asc')->select();
        $set = db('permission_role')
            ->alias('pr')
            ->field('p.id')
            ->leftJoin('permissions p','pr.permission_id=p.id')
            ->where('pr.role_id',$id)
            ->order('p.index','asc')
            ->select();
        $treeArr = $this->getTree($list,0);
        $checkArr = [];
        if(!empty($set)){
            foreach ($set as $k => $v) {
                $checkArr[] = $v['id'];
            }
        }
        return show(600,'',['tree'=>$treeArr,'check'=>$checkArr]);
    }

    public function getTree($list,$pid){
        $treeArr = [];
        foreach ($list as $k => $v) {
            if($v['parent_id'] == $pid){
                $children = $this->getTree($list,$v['id']);
                if(!empty($children)){
                    $treeArr[] = [
                        'id' => $v['id'],
                        'label' => $v['permission_name'],
                        'parent_id' => $v['parent_id'],
                        'permission_slug' => $v['permission_slug'],
                        'children' => $children
                    ];
                }else{
                    $treeArr[] = [
                        'id' => $v['id'],
                        'label' => $v['permission_name'],
                        'parent_id' => $v['parent_id'],
                        'permission_slug' => $v['permission_slug']
                    ];
                }
            }
        }
        return $treeArr;
    }

    public function setPermission(){
        $input = input();
        $role_id = isset($input['role_id']) ? $input['role_id'] : '';
        $check_permission = isset($input['check_permission']) ? $input['check_permission'] : '';
        if(empty($role_id) || empty($check_permission)){
            return show(601,'参数错误',[]);
        }
        //重新设置之前删除之前的设置
        $is_set = db('permission_role')->where('role_id',$role_id)->select();
        if(!empty($is_set)){//已经设置要删除
            $del_ret = db('permission_role')->where('role_id',$role_id)->delete();
            if($del_ret){
                foreach ($check_permission as $k => $v) {
                    $data = [];
                    $data['role_id'] = $role_id;
                    $data['permission_id'] = $v;
                    $data['create_at'] = date('Y-m-d H:i:s',time());
                    $data['update_at'] = date('Y-m-d H:i:s',time());
                    db('permission_role')->insert($data);
                }
                return show(600,'设置成功',[]);
            }else{
                return show(601,'设置失败，请稍后重试',[]);
            }
        }else{
            foreach ($check_permission as $k => $v) {
                $data = [];
                $data['role_id'] = $role_id;
                $data['permission_id'] = $v;
                $data['create_at'] = date('Y-m-d H:i:s',time());
                $data['update_at'] = date('Y-m-d H:i:s',time());
                db('permission_role')->insert($data);
            }
            return show(600,'设置成功',[]);
        }

    }

}
