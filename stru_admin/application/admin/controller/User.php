<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Cache;

class User extends Controller
{
    protected $middleware = [
        'Check' 	=> ['except' 	=> ['checkLogin'] ],
    ];
    public function checkLogin()
    {
    	$input = input();
    	$account = isset($input['account']) ? $input['account'] : '';
    	$password = isset($input['password']) ? $input['password'] : '';
    	$user = db('users')->where('account',$account)->find();
    	if($user && $user['is_use'] == 1){
    		$en_pwd = md5($password.$user['salt']);
    		if($en_pwd == $user['password']){
    			$token = md5($user['id'].$account.time());
    			//存储session
                Cache::set($account,['token' => $token,'time' => time()],3600);
                $role = db('users')
                    ->alias('u')
                    ->field('r.id as role_id')
                    ->leftJoin('role_user ru','u.id=ru.user_id')
                    ->leftJoin('roles r','ru.role_id=r.id')
                    ->where('u.account',$account)
                    ->find();

                if(empty($role)){
                    $role['role_id'] = 2; //默认角色普通用户
                }
                $permission_list = db('permission_role')
                                ->alias('pr')
                                ->field('p.*')
                                ->leftJoin('permissions p','pr.permission_id=p.id')
                                ->where('pr.role_id',$role['role_id'])
                                ->order('p.index','asc')
                                ->select();
                $set = [];
                foreach ($permission_list as $k => $v) {
                    $set[] = $v['id'];
                }
                $list = db('permissions')->order('index','asc')->select();
                $treeArr = $this->getTree($list,0);

                $menu = $this->getMenu($treeArr,$set);
                $update_login['last_login_time'] = date('Y-m-d H:i:s',time());
                db('users')->where('account',$account)->update($update_login);
                $data = ['user_id' => $user['id'],'user_name'=>$user['account'],'token' => $token,'auth_list'=>$set,'menu'=>$menu];
    			return show(600,'登陆成功',$data);
    		}else{
    			return show(601,'密码错误',array());
    		}
    	}else{
    		return show(601,'用户不存在或已禁用',array());
    	}
    }

    /**
     * 获取2级菜单结构
     * @param  [type] $list [description]
     * @param  [type] $set  [description]
     * @return [type]       [description]
     */
    public function getMenu($list,$setArr){
        foreach ($list as $k => $v) {
            if(isset($v['children'])){
                $count = count($v['children']);
                $flag = 0;
                foreach ($v['children'] as $ck => $cv) {
                    if(!in_array($cv['id'], $setArr)){
                        unset($list[$k]['children'][$ck]);
                        $flag += 1;
                    }
                }
                if($flag == $count){
                    unset($list[$k]);
                }
            }else{
                if(!in_array($v['id'], $setArr)){
                    unset($list[$k]);
                }
            }
        }
        return array_values($list);
    }
    /**
     * 获取完整菜单结构
     * @param  [type] $list [description]
     * @param  [type] $pid  [description]
     * @return [type]       [description]
     */
    public function getTree($list,$pid){
        $treeArr = [];
        foreach ($list as $k => $v) {
            if($v['parent_id'] == $pid){
                $children = $this->getTree($list,$v['id']);
                if(!empty($children)){
                    $treeArr[] = [
                        'id' => $v['id'],
                        'parent_id' => $v['parent_id'],
                        'permission_slug' => $v['permission_slug'],
                        'permission_name' => $v['permission_name'],
                        'path' => $v['path'],
                        'icon' => $v['icon'],
                        'children' => $children
                    ];
                }else{
                    $treeArr[] = [
                        'id' => $v['id'],
                        'parent_id' => $v['parent_id'],
                        'permission_slug' => $v['permission_slug'],
                        'permission_name' => $v['permission_name'],
                        'path' => $v['path'],
                        'icon' => $v['icon'],
                    ];
                }
            }
        }
        return $treeArr;
    }

    /**
     * 修改密码
     */
    public function changPassword(){
        $input = input();
        $user_id = isset($input['user_id']) ? $input['user_id'] : '';
        $old_password = isset($input['old_password']) ? $input['old_password'] : '';
        $new_password = isset($input['new_password']) ? $input['new_password'] : '';
        $user = db('users')->field('*')->where('id',$user_id)->find();
        if(!empty($user)){
            $en_password = md5($old_password.$user['salt']);
            if($en_password == $user['password']){
                $new_en_password = md5($new_password.$user['salt']);
                $data['password'] = $new_en_password;
                $ret = db('users')->where('id',$user_id)->update($data);
                if($ret){
                    return show(600,'修改成功',array());
                }else{
                    return show(601,'修改失败请稍后重试',array());
                }
            }else{
                return show(601,'旧密码错误',array());
            }
        }else{
            return show(601,'参数错误',array());
        }
    }

    /**
     * 获取用户列表
     * @return [type] [description]
     */
    public function list(){
        $input = input();
        $page = isset($input['page']) ? $input['page'] : 1;
        $pageSize = isset($input['pageSize']) ? $input['pageSize'] : 10;
        $list = db('users')
            ->field('*')
            ->page($page,$pageSize)
            ->select();
        $total = db('users')->field('count(id) as total')->find();
        return show(600,'',$list,$total['total']);
    }

    /**
     * 添加用户
     */
    public function add(){
        $input = input();
        $account = isset($input['form']['account']) ? $input['form']['account'] : '';
        $phone = isset($input['form']['phone']) ? $input['form']['phone'] : '';
        $role_id = isset($input['form']['role_id']) ? $input['form']['role_id'] : '';
        if(empty($account) || empty($phone) || empty($role_id)){
            return show(601,'参数错误',[]);
        }
        $is_user = db('users')->field('id')->where('account',$account)->find();
        if($is_user){
            return show(601,'用户已存在',[]);
        }else{
            $data['account'] = $account;
            $data['phone'] = $phone;
            $salt = substr(md5($account),8,16);
            $password = md5($account.$salt);
            $data['password'] = $password;
            $data['salt'] = $salt;
            $data['create_at'] = date('Y-m-d H:i:s',time());
            $data['is_use'] = 1;
            $user_id = db('users')->insertGetId($data);
            if($user_id){
                $role_data['user_id'] = $user_id;
                $role_data['role_id'] = $role_id;
                $role_data['create_at'] = date('Y-m-d H:i:s',time());
                $role_data['update_at'] = date('Y-m-d H:i:s',time());
                $ret = db('role_user')->insert($role_data);
                if($ret){
                    return show(600,"添加成功",[]);
                }else{
                    return show(601,"处理失败，请稍后重试！",[]);
                }
            }else{
                return show(601,"处理失败，请稍后重试！",[]);
            }
        }
    }

    /**
     * y用户启用禁用操作
     * @return [type] [description]
     */
    public function enable(){
        $input = input();
        $id = isset($input['id']) ? $input['id'] : '';
        $is_use = isset($input['is_use']) ? $input['is_use'] : '';
        if(empty($id)){
            return show(601,'参数错误',[]);
        }
        $data['is_use'] = $is_use;
        $ret = db('users')->where('id',$id)->update($data);
        if($ret){
            return show(600,"处理成功",[]);
        }else{
            return show(601,"处理失败，请稍后重试！",[]);
        }
    }

    /**
     * 用户编辑操作
     */
    public function edit(){
        $input = input();
        $id = isset($input['id']) ? $input['id'] : '';
        if(empty($id)){
            return show(601,'参数错误',[]);
        }
        $user = db('users')
            ->alias('u')
            ->field('u.*,ru.role_id')
            ->leftJoin('role_user ru','u.id=ru.user_id')
            ->where('u.id',$id)
            ->find();
        return show(600,'',$user);
    }

}
