<?php

namespace hpsynapse\moduser\Repositories;

use hpsynapse\moduser\Models\Role;
use hpsynapse\moduser\Models\Rule;
use hpsynapse\moduser\Models\UserRole;

use Facades\hpsynapse\moduser\Repositories\UserRepo;

use App\Base\BaseRepository;

class RoleRepo extends BaseRepository
{
    public $error = '';
    protected $resellerRole = [
        'reseller','flash_reseller','business_strategy','business_partner'
    ];
    protected $adminRole = [
        'admin_super','admin','marketing_cs','operational_cs','operational_warehouse', 'operational_packing',
        'admin_reselle','admin_finance'
    ];
    
    public function __construct(Role $model)
    {        
        $this->model = $model;        
    }
    
    public function getRole($key,$value)
    {
        return $this->_getOne(new Role,$key,$value);
    }
    /**
     * 
     * @param type $userId
     * @return boolean|array format mirip data role di APPSSession
     */
    public function getRoleByUserId($userId,$withoutTime=true)
    {
        $response = [];
        $userRoleData = UserRole::where('user_id',$userId)->get();
        if(!$userRoleData)return false;
        foreach ($userRoleData as $key => $value) {
            $roleData = Role::where('role_code',$value->role_code)->first();
            if($roleData){
                $roleData = $roleData->toArray();
                $roleData['is_main_role'] = $value->is_main_role;
                $roleData['has_auth_grant'] = $value->has_auth_grant;            

                $roleData['rules'] = Rule::get();
                if($withoutTime){
                    unset($roleData['created_at'],$roleData['updated_at']);
                }
                $response[$value->role_code] = $roleData;
            }
        }
        
        return $response;
    }

    public function listRole($filter=false, $offset=0,$limit=0)
    {
        $data = $this->_getList(
            new Role, [
            'filter' => $filter,
            'searchField' => ['name'],
            'hiddeColumn' => ['created_at','updated_at'],
            'filterFunction' => function($data, $filter) {
                if (isset($filter['level']) && $filter['level']) {
                    $data = $data->where('level', $filter['level']);
                } 
                if (isset($filter['user_level']) && $filter['user_level']) {
                    $data = $data->where('level','not like', $filter['user_level']);
                } return $data;
            }
            ], false, $offset, $limit
        );  
        return $data;
    }
    
    /*
     * Manage USER ROLE
     * -------------------------------------------------------------------------
     */
        
    public function getUserRole($userId)
    {
        return UserRole::where('user_id', $userId)->get()->toArray();
    }
    
    public function getUserRoleCode($userId)
    {
        return UserRole::where('user_id', $userId)->pluck('role_code')->toArray();
    }
    /**
     * 
     * @param type $userId
     */
    public function generateUserRole($userId)
    {
        $dataRole = $this->getUserRole($userId);
        if(!$dataRole) return '';
        foreach ($dataRole as $value) {
            $data[] = $value['role_code'];
        }        
        return ';'.implode(';', $data).';';
    }
    
    public function addUserRole($userId,$roleCode,$isMainRole=0,$hasAuthGrant=0,$dispatchUpdater=true)
    {
        $role = $this->getOne(['role_code'=>$roleCode]);
        if(!$role)return false;
                
        //cek pastikan user role belum terdaftar
        $userRole = $this->_getOne(new UserRole,['user_id'=>$userId,'role_code'=>$role['role_code']]);
        if($userRole)return false;
        
        //jika add reseller role code maka pastikan hanya ada 1 role reseller yang terdaftar
        if(in_array($roleCode, $this->resellerRole) && $userRole = $this->_getOne(new UserRole,['user_id'=>$userId,'role_code'=>$this->resellerRole])){
            
            $roleData = $this->_update(new UserRole,$userRole['id'], [
                'role_code' => $role['role_code'],
                'is_main_role' =>$isMainRole,
                'has_auth_grant'=>$hasAuthGrant,
            ]);
        }else{
            $roleData = $this->_create(new UserRole, [
                'user_id' => $userId,
                'role_code' => $role['role_code'],
                'is_main_role' =>$isMainRole,
                'has_auth_grant'=>$hasAuthGrant,
            ]);
            
        }
                        
        //update role di table user
        UserRepo::updateUser($userId, ['role'=> $this->generateUserRole($userId)],$dispatchUpdater);
        return $role;
    }
    
    public function updateUserRole($key,$data,$dispatchUpdater=true)
    {
        //cek pastikan user role sudah terdaftar
        $userRole = $this->_getOne(new UserRole, $key);
        if(!$userRole)return false;
        if(!isset($data['role_code'])){
            $data['role_code'] = $userRole['role_code'];
        }
        
        //jika update reseller role code maka pastikan hanya ada 1 role reseller yang terdaftar
        if(in_array($userRole['role_code'], $this->resellerRole) && !in_array($userRole['role_code'], $this->resellerRole)){
            $userRole = $this->_getOne(new UserRole,['user_id'=>$userRole['user_id'],'role_code'=>$this->resellerRole]);
            $roleData = $this->_update(new UserRole,$userRole['id'], [
                'role_code' => $data['role_code'],
                'is_main_role' =>isset($data['is_main_role'])&&$data['is_main_role']?1:0,
                'has_auth_grant'=>isset($data['has_auth_grant'])&&$data['has_auth_grant']?1:0,
            ]);
        }else{        
            $roleData = $this->_update(new UserRole,['user_id'=>$userRole['user_id'],'role_code'=>$userRole['role_code']], [
                'role_code' => $data['role_code'],
                'is_main_role' =>isset($data['is_main_role'])&&$data['is_main_role']?1:0,
                'has_auth_grant'=>isset($data['has_auth_grant'])&&$data['has_auth_grant']?1:0,
            ]);            
        }
                        
        //update role di table user
        UserRepo::updateUser($userRole['user_id'], ['role'=> $this->generateUserRole($userRole['user_id'])],$dispatchUpdater);
        return $roleData;
    }
    
    public function deleteUserRole($userId,$roleCode,$dispatchUpdater=true)
    {
        $role = $this->getOne(['role_code'=>$roleCode]);
        if(!$role)return false;
        
        //delete role dari user role
        $roleData = $this->_delete(new UserRole,[
            'user_id' => $userId,
            'role_code' => $role['role_code']
        ]);
        
        //delete role di table user
        UserRepo::updateUser($userId, ['role'=> $this->generateUserRole($userId)],$dispatchUpdater);
        
        return $roleData;
    }        
   
    
}