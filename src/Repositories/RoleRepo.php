<?php

namespace hpsynapse\moduser\Repositories;

use hpsynapse\moduser\Models\Role;

use Facades\hpsynapse\moduser\Repositories\UserRepo;

use App\Base\BaseRepository;

class RoleRepo extends BaseRepository
{
    public $error = '';
    
    public function __construct(Role $model)
    {        
        $this->model = $model;        
    }
    
    public function getRole($where)
    {
        return $this->_getOne(new Role,$where);
    }

    public function listRole($filter=false, $offset=0,$limit=0,$orderBy=false)
    {
        if (!$filter) $filter = [];
        $filter['searchField'] = ['name'];
        $filter['hiddenColumn'] = ['created_at','updated_at'];
        $model = new Role;

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
   
    
}