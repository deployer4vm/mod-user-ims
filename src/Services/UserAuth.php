<?php

namespace hpsynapse\moduser\Services;

use hpsynapse\moduser\Facades\UserRepo;
use hpsynapse\moduser\Facades\RoleRepo;
use Illuminate\Support\Facades\Auth;
use hpsynapse\moduser\Models\ApiToken;
use App\Base\Traits\ResCacheTrait;

/**
 * Library untuk akses SSO service
 * 
 * SSOSession structure :
 *      
 */
class UserAuth
{    
    use ResCacheTrait;
    protected $isLogin=false;
    protected $isApiCall=false;
    protected $token='';
    protected $userData;
    protected $userRoleList;
    protected $userRole;
    protected $userRoleCode;
    
    public function setInit($isApiCall=false,$userData=false,$userRole=false)
    {
        $this->cacheActive = true;
        $this->isApiCall = $isApiCall;
        if($isApiCall){
            if(Auth::check()){
                $userId = Auth::user()->user_id;
                if($userId){
                    $this->userData = UserRepo::getUser($userId);
                    $this->userRole = UserRepo::getUserRole($userId);
                    $this->userRoleCode = Auth::user()->active_role_code;
                    $this->token = Auth::user()->api_token;
                }
            }
        }else{
            $this->userData = $this->getUserSessionData();
            $this->userRole = $this->getUserSessionRole();
            $this->userRoleCode = $this->getActiveUserRoleCode();
            $this->token = $this->getSesionToken();
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
        $data['validUntil'] = $now->addMinutes(config('session.lifetime'))->toDateTimeString();
        return $data;
    }
    
    public function getToken()
    {
        return $this->token;
    }

    public function getSesionToken()
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
        if($field && session('APPSSession.role.'.$field)){
            $data = session('APPSSession.role.'.$field);
        }else{
            $data = session('APPSSession.role');
        }
        return $data;
    }

    public function getActiveUserRoleCode()
    {
        return session('APPSSession.role_code');
    }

    public function getSessionLastUpdate()
    {
        return session('APPSSession.lastUpdate');
    }

    public function getSessionValidUntil()
    {
        return session('APPSSession.validUntil');
    }
    
    /**
     * set session saat login
     * 
     * @param type $userId
     */
    public function setUser($userId,$apiTokenData=false)
    {
        
        $sessionData = $this->getCurTimeStamp();
        $userData = UserRepo::getUser($userId);
        $role = UserRepo::getUserRole($userData['id']);
        
        foreach($role as $key => $val) {
            $roleCode = $key;
            if($val['is_main_role']){
                $roleCode = $key;
                break;
            }
        }

        if(!$apiTokenData){
            $apiTokenData = UserRepo::generateToken($userId,$roleCode);
        }
        
        $this->setSession([
            'token' => $apiTokenData,            
            'user' => $userData,
            'role' => $role,
            'role_code' => $roleCode,
            'lastUpdate' => $sessionData['lastUpdate'],
            'validUntil' => $sessionData['validUntil']
        ]);
    }

    /**
     * ganti role user yang sedang login
     */
    public function setActiveRole($roleCode)
    {
        if($this->role($roleCode)){
            if($this->isApiCall){
                Auth::user()->update(['active_role_code'=>$roleCode]);
            }else{
                $this->setSession([
                    'role_code' => $roleCode
                ]);
            }
            return true;
        }
        return false;
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
            'role' => '',
            'role_code' => ''
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
    public function logoutAllExeptMe()
    {
        //delete semua token kecuali yg loign
        if($this->getToken())ApiToken::where('api_token','!=',$this->getToken())->delete();
    }

    public function lockLoginExeptMe()
    {
        $config = [
            'allow_login' => 0,
            'allow_login_exept' => [],
            'allow_login_only' => [$this->user('id')]
        ]; 
        $this->_saveCache('generalconfig','accesss',$config); 
    }

    public function unlockLogin()
    {
        $config = [
            'allow_login' => 1,
            'allow_login_exept' => [],
            'allow_login_only' => []
        ]; 
        $this->_saveCache('generalconfig','accesss',$config); 
    }

    public function getAccessConfig()
    {
        
        if(!($config = $this->_getCache('generalconfig','accesss'))){
            $config = [
                'allow_login' => 1,
                'allow_login_exept' => [],
                'allow_login_only' => []
            ];           
            $this->_saveCache('generalconfig','accesss',$config); 
        }
        return $config;
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
    
    /**
     * GET data list role user yang sedang login
     * @param type $field
     * @return type
     */
    public function role($field=false,$default=false)
    {
        if($field==false)return $this->userRole;
        return isset($this->userRole[$field])?$this->userRole[$field]:$default;
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
     * cek status user apakah posisi tidak aktif / banned
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
    public function hasRole($roleCode)
    {
        if(isset($this->userData['role']) && strpos($this->userData['role'],';'.$roleCode.';')!==false){
            return true;
        }
        return false;
    }
    
    /**
     * cek apakah user yang online adalah sebagai "role_code"
     * 
     * @param type $roleCode
     * @return boolean
     */
    public function is($roleCode)
    {
        if(!empty($this->userRoleCode) && $this->userRoleCode == $roleCode){
            return true;
        }        
        return false;
    }

    /**
     * cek apakah user adalah webdev
     *
     * @return boolean
     */
    public function isWebDev()
    {
        return $this->isLogin() && $this->is('webbdev');
    }
    
    public function hasAccess(
        $key, 
        $subKey = 'has_access', 
        $defaultAccess = true,
        $checkWebDev = false
    ) {
        if ($checkWebDev === true && $this->isWebDev()) {
            return true;
        }
        if(isset($this->userRole[$this->userRoleCode])){
            if(isset($this->userRole[$this->userRoleCode]['rule'][$key][$subKey])){
                return $this->userRole[$this->userRoleCode]['rule'][$key][$subKey] == 1?true:false;
            }
        }
        return $defaultAccess;
    }

    /**
     * cek apakah user yang login memiliki grant access ke role_code
     * 
     * @param string $roleCode role_code yang dicek nya
     */
    public function isGranted($roleCode=false)
    {
        $data = UserRepo::getUserRole($this->userData['id']);
        
        if(!is_array($roleCode))$roleCode = [$roleCode];
        foreach ($data as $role)
        {
            if(in_array($role['role_code'], $roleCode) && $role['has_auth_grant'] == 1)
                return true;
        }
        return false;
    }
}