<?php

namespace hpsynapse\moduser\Repositories;

use hpsynapse\moduser\Models\Role;

use hpsynapse\moduser\Facades\UserRepo;

use App\Base\BaseRepository;

class RoleRepo extends BaseRepository
{
    public $error = '';
    
    public function __construct(Role $model)
    {        
        $this->model = $model;        
    }
    
    public function listRole($filter=false,int $offset=0,int $limit=0,array $orderBy=[])
    {
        if (!$filter) $filter = [];
        $filter['searchField'] = ['name'];
        $filter['hiddenColumn'] = ['created_at','updated_at'];
        $model = Role::with(['tenantGroup','tenant']);

        if (isset($filter['level']) && $filter['level']) {
            $model = $model->where('level', $filter['level']);
            unset($filter['level']);
        } 
        if (isset($filter['user_level']) && $filter['user_level']) {
            $model = $model->where('level','not like', $filter['user_level']);
            unset($filter['user_level']);
        }

        $data = $this->_list(
            $model, $filter, $offset, $limit, $orderBy
        );  
        return $data;
    }

    public function getRole($where)
    {
        return $this->_getOne(new Role,$where);
    }

    public function createRole($data)
    {
        // if(isset($data['rule']) && is_array($data['rule']))
        //     $data['rule'] = json_encode($data['rule'],JSON_PRETTY_PRINT);
        if(!isset($data['tenant_id']))
            $data['tenant_id'] = config('tenant.id',0);
        return $this->_create(new Role, $data);
    }

    public function updateRole($where, $data)
    {
        // if(isset($data['rule']) && is_array($data['rule']))
        //     $data['rule'] = json_encode($data['rule'],JSON_PRETTY_PRINT);
        if ($this->checkSystemRole($where)){
            $this->error = 'Cannot update system role in here';
            return false;
        }

        return $this->_update(new Role, $where, $data);
    }
    public function deleteRole($id)
    {
        if ($this->checkSystemRole($id)){
            $this->error = 'System role cannot be deleted in here';
            return false;
        }

        return $this->_delete(new Role, ['id', $id]);
    }

    public function checkSystemRole($id){
        $role = $this->_getOne(new Role,['id',$id]);
        if($role['system_role']){
            return true;
        }
        return false;
    }
   
    
}