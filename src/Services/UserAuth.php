<?php

namespace hpsynapse\moduser\Services;

use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;
use Illuminate\Support\Facades\Auth;

/**
 * Library untuk akses SSO service
 * 
 * SSOSession structure :
 *      
 */
class UserAuth
{    
    protected $isLogin=false;
    protected $isApiCall=false;
    protected $token;
    protected $userData;
    protected $userRoleList;
    protected $userRole;
    
    public function setInit($isApiCall=false,$userData=false,$userRole=false)
    {
        $this->isApiCall = $isApiCall;
        if($isApiCall){
            if(Auth::check()){
                $userId = Auth::user()->user_id;
                if($userId){
                    $this->userData = UserRepo::getOneWithProfile($userId);
                    $listUserRole = RoleRepo::getRoleByUserId($userId);
                    $this->userRole = array_pop($listUserRole);
                }
            }
        }else{
            $this->userData = $this->getUserSessionData();
            $this->userRole = $this->getUserSessionRole();
        }
    }
    
    /**
     * generate current timestamps
     * 
     * @return type
     */
    public function getCurTimeStamp()
    {
        $now = now();
        $data['lastUpdate'] = $now->toDateTimeString();
        $data['validUntil'] = $now->addHour(config('AppConfig.packageLocal.moduser.session_lifetime'))->toDateTimeString();
        return $data;
    }
    
    public function getToken()
    {
        return session('APPSSession.token');
    }

    public function getUserSessionData($field=false)
    {
        if($field && session('APPSSession.user.'.$field)){
            $data = session('APPSSession.user.'.$field);
        }else{
            $data = session('APPSSession.user');
        }
        return $data;
    }
    
    public function getUserSessionRole($field=false)
    {
        if($field && session('APPSSession.user.'.$field)){
            $data = session('APPSSession.user.'.$field);
        }else{
            $data = session('APPSSession.user');
        }
        return $data;
    }

    /**
     * set session saat login
     * 
     * @param type $userId
     */
    public function setUser($userId,$apiTokenData=false)
    {
        if(!$apiTokenData){
            $apiTokenData = UserRepo::generateToken($userId);
        }
        
        $sessionData = $this->getCurTimeStamp();
        $userData = UserRepo::getOne($userId);
        
        $this->setSession([
            'token' => $apiTokenData,            
            'user' => $userData,
            'role' => RoleRepo::getRoleByUserId($userData['id']),
            'lastUpdate' => $sessionData['lastUpdate'],
            'validUntil' => $sessionData['validUntil']
        ]);
    }
    public function updateSessionId()
    {
        $this->setSession(['sessionId'=>session()->getId()]);
    }
    /**
     * unset session saat logout
     * 
     * @param type $userId
     */
    public function unsetUser()
    {
        $sessionData = $this->getCurTimeStamp();        
        $this->setSession([
            'token' => '',
            'lastUpdate' => $sessionData['lastUpdate'],
            'validUntil' => $sessionData['validUntil'],
            'user' => '',
            'role' => ''
            ]);
    }
    
    /**
     * set session saat pertama kali dapet dari account center
     * @param array $apiData variable session SSO yg akan diubah
     */
    public function setSession(Array $data)
    {
        //$availableKey = ['id', 'lastUpdate','validUntil','appsToken','userToken','userData'];
        foreach ($data as $key => $value) {
            session()->put('APPSSession.'.$key,$value);
        }        
        session()->save();
    }

    /**
     * =============================================================================================
     */
    public function isLogin()
    {
        return Auth::check() && is_array($this->userData);
    }
    
    /**
     * GET data user dari Account Center
     * @param type $field
     * @return type
     */
    public function user($field=false,$default=false)
    {
        if($field==false)return $this->userData;
        return isset($this->userData[$field])?$this->userData[$field]:$default;
    }
    
    
    public function isPhoneVerified()
    {        
        if(isset($this->userData['phone_verified_at']) && $this->userData['phone_verified_at']){
            return true;
        }
        return false;
    }
    
    public function isEmailVerified()
    {        
        if(isset($this->userData['email_verified_at']) && $this->userData['email_verified_at']){
            return true;
        }
        return false;
    }
    
    
    /**
     * ROLE CHECK
     * -------------------------------------------------------------------------
     */
    
    /**
     * cek apakah user yang online memiliki akses role "rolde_code"
     * 
     * @param type $roleCode
     * @return boolean
     */
    public function is($roleCode)
    {
        if(isset($this->userData['role']) && strpos($this->userData['role'],':'.$roleCode.';')){
            return true;
        }
        return false;
    }
    
    public function isMember()
    {
        if(isset($this->roleData['member'])){
            return true;
        }
        return false;
    }
    
    /**
     * cek status reseller apakah posisi tidak aktif / banned
     * 
     * @return boolean
     */
    public function isBanned()
    {
        if($this->localUser['status'] == 0){
            return true;
        }
        return false;
    }
    
    /*
     * -------------------------------------------------------------------------
     */

    public function isGranted($roleCode=false)
    {
        $data = RoleRepo::getRoleByUserId($this->userData['id']);
        
        if(!is_array($roleCode))$roleCode = [$roleCode];
        foreach ($data as $role)
        {
            if(in_array($role['role_code'], $roleCode) && $role['has_auth_grant'] == 1)
                return true;
        }
        return false;
    }
}