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

    public function listRole($filter=false, $offset=0,$limit=0)
    {
        $filter['searchField'] = ['name'];
        $filter['hiddeColumn'] = ['created_at','updated_at'];
        $filter['filterFunction'] = function($data, $filter) {
            if (isset($filter['level']) && $filter['level']) {
                $data = $data->where('level', $filter['level']);
            } 
            if (isset($filter['user_level']) && $filter['user_level']) {
                $data = $data->where('level','not like', $filter['user_level']);
            } return $data;
        };

        $data = $this->_list(
            new Role, $filter, false, $offset, $limit
        );  
        return $data;
    }
   
    
}