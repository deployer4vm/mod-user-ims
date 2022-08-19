<?php

namespace hpsynapse\moduser\Repositories;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Exception;
use Validator;
use Mail;
use Carbon\Carbon;

use hpsynapse\moduser\Facades\UserLogRepo;
//use semua model yg diperlukan
use hpsynapse\moduser\Models\User;
use hpsynapse\moduser\Models\UserProfile;
use hpsynapse\moduser\Models\PasswordReset;
use hpsynapse\moduser\Models\UserRole;
use hpsynapse\moduser\Models\Role;
// use hpsynapse\moduser\Models\ApiToken;
use Illuminate\Support\Str;
use hpsynapse\moduser\Repositories\UserRepo;

use hpsynapse\moduser\Models\UserTenant;
use App\Facades\Tenant;

use App\Base\BaseRepository;

class UserSystemRepo extends BaseRepository
{
    use ApiTokenTraits, UserMessageTraits;


    public function __construct(User $model, UserRepo $userRepo)
    {
        $this->model = $model;
        $this->userRepo = $userRepo;
    }

    
    /**
     *  getter operation method
     * ==========================================================================
    */

    /**
     * 
     * @param $filter array
     *     profile
     *     tenant
    */
    public function listUser($filter = false,int $offset = 0,int $limit = 0,array $orderBy=[])
    {
        if (!$filter) $filter = [];
        $filter['searchField'] = ['name','email','username'];
        $filter['hiddenColumn'] = ['created_at', 'updated_at', 'cached_at'];
          $user = User::with(['profile','roles.role','mainRole','apiToken']);
          if(isset($filter['profile'])){
            $user = $user->whereHas('profile', function($q) use ($filter){
                $q = $this->_where($q,$filter['profile']);
            });
            unset($filter['profile']);
        }
          if(isset($filter['tenant'])){
            // $user = $user->where('all_tenant');
            $user = $user->whereHas('userTenant', function($q) use ($filter){
                $q = $this->_where($q,[['tenant_id',$filter['tenant']]]);
            });
            unset($filter['tenant']);
          }
        
        $data = $this->_list(
            $user,
            $filter,
            $offset,
            $limit,
            $orderBy
        );
        $this->dataUserPagination = $this->pagination;
        $data['data'] = array_map([$this,'_formatUser'],$data['data']);
          return $data;
    }

    
    /**
     * get 1 record user beserta profile nya
     * 
     * @param type              $userId
     * @return array|false      jika tidak ada
     */
    public function getUser($where)
    {
        $user = $this->_getOne(User::with(['profile','mainRole','apiToken']),$where);
        if ($user) {          
            return $this->_formatUser($user);
        }
        return false;
    }

    /**
     * 
     * @param String $userId
     * @return boolean|array list role user, format mirip data role di APPSSession
     */
    public function getUserRole($userId,$withoutTime=true)
    {
        $response = [];
        $userRoleData = UserRole::where('user_id',$userId)->get();
        if(!$userRoleData)return false;
        foreach ($userRoleData as $key => $value) {
            $roleData = Role::where('id',$value->role_id)->first();
            if($roleData){
                $roleData = $roleData->toArray();
                $roleData['is_main_role'] = $value->is_main_role;
                $roleData['has_auth_grant'] = $value->has_auth_grant;            

                if($withoutTime){
                    unset($roleData['created_at'],$roleData['updated_at']);
                }
                $response[$roleData['role_code']] = $roleData;
            }
        }
        
        return $response;
    }

    // Create User | Post User | Register
    
    /**
     *  setter operation method
     * ==========================================================================
     */

    /**
     * registrasi user baru
     * 
     * @param Array $userData : seluruh field di table user (kecuali role) dan :
     *      all_tenant : jika tidak disertakan maka dianggap per tenant
     *      role_code : * optional    string role_code, jika tidak dicantumkan akan menggunakan default role_code
     * 
     * @param Boolean $generateToken 1 jika generate token, 0 jika tidak
     * 
     * @return Array seluruh field di table user dan :
     *      token           String      api_token dari table api_tokens yg digenerate saat registrasi, jika generatetoken true
     *      token_id        Number      id dari table api_tokens nhya
     * 
     *      main_role       Array       record role
     *      profile         Array       record user profile
     *      
     */
    public function register(array $userData, $generateToken = true)
    {
        $userData = $this->registerFilter($userData);

        if (!$userData) {
            return false;
        }

        $userData['user_idcode'] = $this->userRepo->generateUserIdcode();
        $userData['username'] = $this->generateUsername();
        $userData['email'] = $this->generateEmail();
        $userData['password'] = $this->generatePassword();
        $userData['system_user'] = true;
        $userData['status'] = 'active';
        
        // if(!isset($userData['tenant_id']))$userData['tenant_id'] = config('tenant.id',0);//jika 0 berarti tanpa tenant atau bisa akses semua tenant

        //role dikosongin dahulu karena insert role di proses selanjutnya
        $roles = $userData['role_code'];//array list role_code (dari registerFilter)
        $mainRole = $userData['main_role'];// dari registerFilter
        unset($userData['role_code'],$userData['main_role']);
        
        $dontHaveTransactionLevel = !Tenant::dbTransactionLevel();
        if($dontHaveTransactionLevel)
            Tenant::dbBeginTransaction();
        
        try{
            $data = $this->model->create($userData);
            $data = $data->toArray();

            //jika menyertakan tenant maka daftarkan user di tenant bersangkutan
            if(!(isset($userData['all_tenant']) && $userData['all_tenant']==1)){
                $userData['all_tenant'] = 0;
                if(config('tenant.id')){
                    UserTenant::create([
                        'tenant_id' => config('tenant.id'),
                        'user_id' => $data['id']
                    ]);
                }
            }

            if ($generateToken) {
                $apiTokenData = $this->generateToken($data['id'],$mainRole['role_code'],1);
                $data['token'] = $apiTokenData['api_token'];;
                $data['token_id'] = $apiTokenData['id'];
            }

            /**
             * add profile
             */
            $userProfile = [];
            if(isset($userData['profile'])) $userProfile = $userData['profile'];   

            $userProfile['user_id'] = $data['id'];
            $data['profile'] = $this->userRepo->registerProfile($userProfile);

            //update role data          
            $this->updateUserRole($data['id'],$roles);
            $data['main_role'] = $mainRole;

            // $this->addUserRole(
            //     $data['id'],
            //     $role['role_code'],
            //     1,
            //     $role['has_auth_grant'],
            //     false
            // );

            // dispatch event on register saat berhasil
            event(new \hpsynapse\moduser\Events\OnUserRegisteredSuccess($data)); 

            if($dontHaveTransactionLevel)
                Tenant::dbCommit();
        
        } catch (Exception  $e) {
            if($dontHaveTransactionLevel)
                Tenant::dbRollback();

            $this->error = $e->getMessage();
            
            Log::info('moduser UserRepo::register() ERROR');
            Log::error($e);

            // jika sedang dalam transaksi dari parent maka teruskan error nya ke parent transaction nya
            if(!$dontHaveTransactionLevel)
                throw $e; 

            return false;
        }
            
        return $data;
    }

    /**
     * 
     * @return Array
     *      role_code       Array   List role
     *      main_role       Array
     *          role_code
     *          level
     */
    public function registerFilter(array $userData)
    {
        $validatorRule = [
            'name' => 'required|min:5|max:255',
        ];
        $validator = Validator::make($userData, $validatorRule);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $err[] = '<ul>';
            foreach ($errors->all() as $message) {
                $err[] = '<li>' . $message . '</li>';
            }
            $err[] = '</ul>';
            $this->error = implode('', $err);
            return false;
        }

        //jika tidak menyertakan role_id maka set default
        if (!isset($userData['role_code'])) {
            if(config('AppConfig.system.web_admin.registration.default_system_role_code')){
                $userData['role_code'] = [config('AppConfig.packageLocal.moduser.registration.default_system_role_code')];
            }else{
                $userData['role_code'] = "system";
            }
            
        }

        if(!is_array($userData['role_code']))$userData['role_code'] = [$userData['role_code']];
        //pastikan rule nya ada        
        $roles = Role::whereIn('role_code', $userData['role_code'])->orderBy('level','ASC')->get();
        if ($roles->count() <=0 ) {
            $this->error = 'Roles not defined.';
            return false;
        }
        $userData['role_code'] = [];
        $isFirstRow=true;
        foreach ($roles as $val) {  
            $userData['role_code'][] = $val->role_code;
            if($isFirstRow){
                $userData['main_role'] = ['role_code'=>$val->role_code,'level'=>$val->level];
                $isFirstRow=false;
            }
        }

        $userData['level'] = $userData['main_role']['level'];
        
        //pastikan tidak ada parameter yang ksosong
        foreach ($userData as $key => $value) {
            if(empty($value))unset($userData[$key]);
        }

        return $userData;
    }
    
    /**
     * untuk update role user
     */
    public function updateUserRole($userId,$newRoleCode,$hasAuthGrant=0){
        if(!is_array($newRoleCode))$newRoleCode = [$newRoleCode];

        $roleIds = Role::where('system_role', true)->whereIn('role_code', $newRoleCode)->orderBy('level','ASC')->get()->pluck('id');
        
        if (count($roleIds)<=0) {
            $this->error = 'Role not defined.';
            return false;
        }

        //delete semua role yang tidak terpilih
        UserRole::where('user_id',$userId)->whereNotIn('role_id', $roleIds)->delete();

        $roles = Role::whereIn('role_code', $newRoleCode)->orderBy('level','ASC')->get();        
        $first = true;
        $isMainRole = 1;
        foreach ($roles as $role) {

            if(UserRole::where('user_id',$userId)->where('role_id',$role->id)->exists()){
                $this->_update(new UserRole,[['user_id',$userId],['role_id',$role->id]], [
                    'has_auth_grant'=>$hasAuthGrant,
                    'is_main_role'=>$isMainRole,
                ]);
            }else{
                $this->_create(new UserRole, [
                    'user_id'=>$userId,
                    'role_id'=>$role->id,
                    'has_auth_grant'=>$hasAuthGrant,
                    'is_main_role'=>$isMainRole,
                ]);
            } 
            
            if($first){
                $first = false;
                $isMainRole = 0;
            }     
        }
                           
        $roleUser = $this->userRepo->generateUserRole($userId);
        //update role di table user
        // $this->updateUser($userId, [
        //     'role'=> $roleUser,
        //     'level'=>$role->level
        // ],false);
        User::where('id',$userId)->update([
            'role'=> $roleUser,
            'level'=>$role->level
        ]);
    }
    
    // Generate
    public function generateUsername()
    {
        $username = Str::random(10);
        $user = User::where('username', $username)->first();
        if ($user) {
            $this->generateUsername();
        }
        return $username;
    }

    public function generateEmail(){
        $email = Str::random(8) . '@' . Str::random(5) . '.com';
        $user = User::where('email', $email)->first();
        if ($user) {
            $this->generateEmail();
        }
        return $email;
    }

    public function generatePassword(){
        return Hash::make(Str::random(8));
    }

    // Update User | Put User
    
    /**
     * 
     * @param integer           $userId user id user yang akan diupdate
     * @param array             $userData
     *      role_code *optional string role_code, jika disertakan maka akan mengubah role utama
     * 
     * @return boolean
     */
    public function updateUser($userId, $userData, $runEvent=true)
    {
        if(!($oldUser = $this->userRepo->getUser($userId))){
            $this->error = __('lang.data_attribute_not_found',['attribute'=>'User']);
            return false;
        }

        if (!$oldUser['system_user']) {
            $this->error = __('lang.data_attribute_not_found',['attribute'=>'User']);
            return false;
        }

        if (isset($userData['_token'])) unset($userData['_token']);
        if (isset($userData['_method'])) unset($userData['_method']);
        if (isset($userData['created_at'])) unset($userData['created_at']);
        if (isset($userData['updated_at'])) unset($userData['updated_at']);
        if (isset($userData['user_role'])) unset($userData['user_role']);
        if (isset($userData['main_role'])) unset($userData['main_role']);
        if (isset($userData['email'])) unset($userData['email']);
        if (isset($userData['username'])) unset($userData['username']);
        if (isset($userData['password'])) unset($userData['password']);
        
        $dontHaveTransactionLevel = !Tenant::dbTransactionLevel();
        if($dontHaveTransactionLevel)
            Tenant::dbBeginTransaction();
        
        $hasUploadAvatar = false;

        try{

            //jika menyertakan profile, maka proses update table profile
            if(isset($userData['profile'])){
                $this->userRepo->updateProfile($userId, $userData['profile']);
                unset($userData['profile']);
            }
            
            //pastikan tidak ada parameter yang ksosong
            foreach ($userData as $key => $value) {
                if(empty($value))unset($userData[$key]);
            }
            
            //jika update role data
            if (isset($userData['role_code'])){            
                $this->updateUserRole($userId,$userData['role_code']);
                unset($userData['role_code'],$userData['role']);
            }
            
            $this->_update(new User, $userId, $userData);
            
            if($runEvent)
                event(new \hpsynapse\moduser\Events\OnUserUpdatedSuccess($oldUser,$this->userRepo->getUser($userId))); 
                        
            if($dontHaveTransactionLevel)
                Tenant::dbCommit();
            
            return true;

        } catch (Exception  $e) {
            if($dontHaveTransactionLevel)
                Tenant::dbRollback();
            if($hasUploadAvatar)

            $this->error = $e->getMessage();
            
            Log::info('moduser UserRepo::updateUser() ERROR');
            Log::error($e);

            // jika sedang dalam transaksi dari parent maka teruskan error nya ke parent transaction nya
            if(!$dontHaveTransactionLevel)
                throw $e; 

            return false;
        }
    }

    public function deleteUser($userId){
        if(!$this->_exists(new User,[['id',$userId]])){
            $this->error = 'user tidak ditemukan';
            return false;
        }

        if (!$this->userRepo->checkSystemUser($userId)) {
            $this->error = 'user ini bukanlah user system, tidak bisa dihapus disini';
            return false;
        }

        $this->_delete(new User,[['id',$userId]]);
        $this->_delete(new UserProfile,[['user_id',$userId]]);
        $this->_delete(new UserRole,[['user_id',$userId]]);
        return true;
    }

    public function generateTokenApi($id){
        if(!$this->_exists(new User,[['id',$id]])){
            $this->error = 'user tidak ditemukan';
            return false;
        }

        if (!$this->userRepo->checkSystemUser($id)) {
            $this->error = 'user ini bukanlah user system, tidak bisa generate token disini';
            return false;
        }

        $userApi = User::where('id',$id)->with('apiToken','roles')->first();
        $roles = Role::where('id',$userApi->roles[0]->role_id)->first();

        // if($userApi->apiToken){
        //     $this->error = 'user ini sudah memiliki token api'; 
        //     return false;
        // }

        $apiTokenData = $this->generateToken($id,$roles->role_code,1);
        $data['token'] = $apiTokenData['api_token'];;
        $data['token_id'] = $apiTokenData['id'];

        return $data;
    }

    /**
     * helper getter untuk get data user
     * format data user sesuai keperluan
     */
    private function _formatUser(array $user) {
        $user = $this->_filterField($user, config('AppConfig.packageLocal.moduser.users_hidden_field'));

        if (isset($user['avatar']) && $user['avatar'])
            $user['avatar_url'] = Storage::url($user['avatar']);

        if (!empty($user['profile'])) {
            $user['profile'] = $this->_filterField($user['profile'], config('AppConfig.packageLocal.moduser.user_profiles.hide'));
            unset($user['profile']['id'], $user['profile']['user_id'],
                $user['profile']['created_at'], $user['profile']['updated_at']);
                
            // $user = array_merge($user, $user['profile']); // splice in at position 3
        }

        if (!empty($user['main_role']) && !isset($user['main_role']['role_code'])) {
            $role = Role::where('id',$user['main_role']['role_id'])->first()->toArray();
            $user['main_role'] = array_merge($user['main_role'],$role);
        }

        // unset($user['profile']);        
        return $user;
    }

}