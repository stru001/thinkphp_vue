<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Cache;

class permission extends Controller
{
    protected $middleware = [
        'Check'
    ];
    /**
     * 获取权限列表
     * @return [type] [description]
     */
    public function list(){
        $list = db('permissions')
            ->field('*')
            ->order('index','asc')
            ->select();
        return show(600,'',$list);
    }
    /**
     * 添加权限模块
     */
    public function add(){
        $input = input();
        $id = isset($input['form']['id']) ? $input['form']['id'] : '';
        $permission_name = isset($input['form']['permission_name']) ? $input['form']['permission_name'] : '';
        $permission_slug = isset($input['form']['permission_slug']) ? $input['form']['permission_slug'] : '';
        $icon = isset($input['form']['icon']) ? $input['form']['icon'] : '';
        $parent_id = isset($input['form']['parent_id']) ? $input['form']['parent_id'] : '';
        $path = isset($input['form']['path']) ? $input['form']['path'] : '';
        $index = isset($input['form']['index']) ? $input['form']['index'] : '';
        if(empty($permission_name) || empty($permission_slug)){
            return show(601,'参数错误',[]);
        }
        if(empty($id)){
                $has_permission = db('permissions')
                    ->field('id')
                    ->where('permission_slug',$permission_slug)
                    ->whereOr('permission_name',$permission_name)
                    ->find();
                if($has_permission){
                    return show(601,'模块名或标识已存在',[]);
                }
                $data['permission_name'] = $permission_name;
                $data['permission_slug'] = $permission_slug;
                $data['create_at'] = date('Y-m-d H:i:s',time());
                $data['update_at'] = date('Y-m-d H:i:s',time());
                $data['icon'] = $icon;
                $data['parent_id'] = $parent_id;
                $data['path'] = $path;
                $data['index'] = $index;
                $ret = db('permissions')->insert($data);
                if($ret){
                    return show(600,"添加成功",[]);
                }else{
                    return show(601,"添加失败，请稍后重试！",[]);
                }
        }else{
            $p_name['permission_name'] = $permission_name;
            $p_slug['permission_slug'] = $permission_slug;
            $has_permission = db('permissions')
                ->field('id')
                ->where('id','<>',$id)
                ->where(function ($query) use ($p_name,$p_slug) {
                    $query->whereOr($p_name);
                    $query->whereOr($p_slug);
                })
                ->find();
            if($has_permission){
                return show(601,'模块名或标识已存在',[]);
            }
            $data['permission_name'] = $permission_name;
            $data['permission_slug'] = $permission_slug;
            $data['update_at'] = date('Y-m-d H:i:s',time());
            $data['icon'] = $icon;
            $data['parent_id'] = $parent_id;
            $data['path'] = $path;
            $data['index'] = $index;
            $ret = db('permissions')->where('id',$id)->update($data);
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
        $permission = db('permissions')->where('id',$id)->find();
        return show(600,'',$permission);
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
        $ret = db('permissions')->where('id',$id)->delete();
        if($ret){
            return show(600,'删除成功',[]);
        }else{
            return show(601,'删除失败,请稍后重试',[]);
        }
    }

}
