<?php

namespace hpsynapse\moduser\Repositories;

use hpsynapse\moduser\Models\Role;

use hpsynapse\moduser\Facades\UserRepo;
use hpsynapse\moduser\Repositories\RoleRepo;

use App\Base\BaseRepository;

class RoleSystemRepo extends BaseRepository
{
    public $error = '';
    
    public function __construct(Role $model, RoleRepo $roleRepo)
    {        
        $this->model = $model;        
        $this->roleRepo = $roleRepo;
    }
    
    public function createRole($data)
    {
        if(isset($data['rule']) && is_array($data['rule']))$data['rule'] = json_encode($data['rule'],JSON_PRETTY_PRINT);
        $data['level'] = 2;
        $data['system_role'] = true;

        if ($this->model->where('role_code', $data['role_code'])->count() > 0) {
            $this->error = 'Role code already exists';
            return false;
        }

        return $this->_create(new Role, $data);
    }
    
    public function updateRole($where, $data)
    {
        if(isset($data['rule']) && is_array($data['rule']))$data['rule'] = json_encode($data['rule'],JSON_PRETTY_PRINT);
        $data['level'] = 2;
        $data['system_role'] = true;

        if ($this->model->where('role_code', $data['role_code'])->count() > 0) {
            $this->error = 'Role code already exists';
            return false;
        }

        if (!$this->roleRepo->checkSystemRole($where)){
            $this->error = 'Cannot update system role in here';
            return false;
        }

        return $this->_update(new Role, $where, $data);
    }

    public function deleteRole($id)
    {
        if (!$this->roleRepo->checkSystemRole($id)){
            $this->error = 'Cannot delete not system role';
            return false;
        }

        return $this->_delete(new Role, ['id', $id]);
    }
}