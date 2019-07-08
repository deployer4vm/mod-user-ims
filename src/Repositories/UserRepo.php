<?php

namespace hpsynapse\moduser\Repositories;

// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Facades\hpsynapse\moduser\Repositories\UserLogRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;

use Carbon\Carbon;

//use semua model yg diperlukan
use hpsynapse\moduser\Models\User;
use hpsynapse\moduser\Models\UserProfile;
use hpsynapse\moduser\Models\PasswordReset;
// use hpsynapse\moduser\Models\UserRole;
use hpsynapse\moduser\Models\Role;
// use hpsynapse\moduser\Models\ApiToken;
use App\Models\Tenant;

use Validator;
use Mail;
use App\Base\BaseRepository;

class UserRepo extends BaseRepository
{
    use ApiTokenTraits,UserMessageTraits;
    
    public $error = '';
    
    protected $userProfileField = [
        'avatar',
        'gender',
        'date_of_birth',
        'socnet_facebook',
        'socnet_instagram',
        'address',
        'postal_code'
    ];

    public function __construct(User $model)
    {
        $this->model = $model;
    }
    /**
     * 
     * @param string $key
     * @param type $value
     * @return boolean : true jika ada, false jiak tidak ada
     */
    public function isUserExist($key,$value=NULL)
    {
        //jika tidak menyertakan value berarti default nya by apps code
        if(is_null($value)){
            $value = $key;
            $key = 'id';
        }
        
        return $this->model->where($key, $value)->exists();
    }
    
    /**
     * cek apakah user tertentu memiliki role tertentu
     * 
     * @param integer $userId user id
     * @param string $roleCode 
     */
    public function isHasRole($userId,$roleCode)
    {
        $role = Role::where('role_code',$roleCode)->first();
        if($role)
        return User::UserRole('user_id',$userId)->where('role','LIKE','%;'.$roleCode.';%')->exists();        
    }
    
    /**
     * get user berdasarkan email dan password nya
     * 
     * @param text $email
     * @param text $password
     * @param integer $tenantId id tenant
     * @return boolean
     */
    public function loginCheck($email, $password, $tenantId=0)
    {
        $userData = User::where('email',$email)->first();
        if($userData==null)return false;
        if(!Hash::check($password, $userData->password))return false;        
        $user = $userData->toArray();
        //jika tidak punya akses all tenant maka cek tenant
        if(!$user['all_tenant']){
            if(Tenant::where('id',$tenantId)->first()==null){
                return false;
            }
        }
        $user['profile'] = $userData->profile?$userData->profile->toArray():[];
        return $user;
        
    }

    public function activateUser($user_id)
    {
        $userData = $this->model->find($user_id);
        
        if(!$userData){
            $this->error = 'User not found.';
            return false;
        }
        
        UserLogRepo::addActivityLog($user_id,'activate');
        
        return $userData->update(['status'=>1]);
    }
    
    /**
     * Hanya digunakan untuk fungsi yang memerlukan Model user, misal auth, notifikasi, dll
     * selain itu tidak boleh.
     * 
     * Digunakan untuk ambil 1 record user berbentuk model elequent, biasanya
     * digunakan untuk keperluan fitur-fitur yg berkaitan dengan user model.
     * 
     * @param type $user_id
     * @return type
     */
    public function getUserModel($user_id)
    {
        return User::find($user_id);
    }
    
    public function listUser($filter=false, $offset=0,$limit=0)
    {
        $data = $this->_getList(
            new User, [
            'filter' => $filter,
            'searchField' => ['name'],
            'hiddeColumn' => ['created_at','updated_at','cached_at']
            ], false, $offset, $limit
        );  
        return $data;
    }

    public function getUser($key,$value=null) 
    {
        return $this->_getOne(new User, $key,$value);
    }
    
    public function getUserProfile($userId) 
    {
        return $this->_getOne(new UserProfile, ['user_id'=>$userId]);
    }
    
    /**
     * get 1 record user beserta profile nya
     * 
     * @param type $userId
     * @return mixed false jika tidak ada
     */
    public function getOneWithProfile($userId)
    {
        $user = User::with(['profile'])->find($userId);
        if($user){
            $result = $user->toArray();
            $result['profile'] = $user->profile->toArray();
            unset($result['profile']['id'],$result['profile']['user_id'],
                $result['profile']['created_at'],$result['profile']['updated_at']);
            if($result['profile']['avatar'])
                $result['profile']['avatar_url'] = Storage::url($result['profile']['avatar'],false,'ac');
            return $result;
        }
        return false;
    }
    
    public function getOneBySocnetId($id, $provider)
    {
        $userData = User::where('socialauth_'.$provider.'_id',$id)->first();
        
        if(!$userData)return false;
        return $userData->toArray();
    }
    
    public function getOneBySocnetIdOrEmail($id,$email,$provider)
    {
        $userData = User::where('email', $email)
            ->orWhere('socialauth_'.$provider.'_id',$id)
            ->first();
        
        if(!$userData)return false;
        return $userData->toArray();
    }
        
    /**
     * rigistrasi user baru
     * 
     * @param array $userData : seluruh field di table user dan :
     *      role_code
     *      apps_id
     *      has_auth_grant
     * @param boolean $generateToken 1 jika generate token, 0 jika tidak
     * 
     * @return array : seluruh field di table user dan :
     *      token : api_token dari table api_tokens yg digenerate saat registrasi
     *      token_id : id dari table api_tokens nhya
     *      
     */
    public function register(Array $userData,$generateToken=true)
    {
        $userData = $this->registerFilter($userData);
        
        if(!$userData){
            return false;
        }
        
//        $userRole = null;
//        foreach ($userData['role_code'] as $key => $value) {
//            $roleData = Role::where('role_code',$value)->first()->toArray();;        
//            if(!$roleData){
//                $this->error = 'Role tidak terdaftar';
//                return false;
//            }
//            $userRole[] = $roleData['id'].':'.$roleData['role_code'];
//        }
//        $userData['role'] = ';'.implode(';', $userRole).';';

        $userData['password']=isset($userData['password'])?Hash::make($userData['password']):'';
        
        $userData['user_idcode'] = $this->generateUserIdcode();
        $userData['tenant_id'] = config('tenant.id')?config('tenant.id'):0;
        
        //role dikosongin dahulu karena insert role di proses selanjutnya
        $role = $userData['role'];
        $userData['role'] = '';
        
        $data = $this->model->create($userData);
        $data = $data->toArray();
                
        if($generateToken){
            $apiTokenData = $this->generateToken($data['id']);
            $data['token'] = $apiTokenData['api_token'];
        }
        
        $profileData = $data;
        $profileData['user_id'] = $data['id'];
        unset($profileData['id']);
        
        $this->registerProfile($profileData);
             
        if(isset($role) && is_array($role)){
            foreach ($role as $key => $value) {
                if(isset($value['role_code'])){
                    RoleRepo::addUserRole(
                        $data['id'],
                        $value['role_code'],
                        isset($value['is_main_role'])&&$value['is_main_role']?1:0,
                        isset($value['has_auth_grant'])&&$value['has_auth_grant']?1:0,
                        false);
                }
            }         
        }
                
        if(isset($userData['email']))
            $this->sendUserActivationEmail($data['id'],$userData['apps_id']);
                
        return $data;
    }
    
    public function registerFilter(Array $userData)
    {
        $validatorRule = [
            'name' => 'required|min:5|max:255',
        ];
        if(isset($userData['password'])){
            $validatorRule['password'] = 'required|min:5|max:255';
        }
        if(isset($userData['email'])){
            $validatorRule['email'] = 'required|email|min:5|max:255';
        }        
        $validator = Validator::make($userData, $validatorRule);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            $err[] = '<ul>';
            foreach ($errors->all() as $message) {
                $err[] = '<li>'.$message.'</li>';
            }
            $err[] = '</ul>';
            $this->error = implode('', $err);
            return false;
        }        
        
        if(isset($userData['phone'])){
            $userData['phone'] = $this->phoneFormat($userData['phone']);
        }

        if(!isset($userData['has_auth_grant']))$userData['has_auth_grant']=0;
                
        //jika tidak menyertakan role_id maka set default
        if(!isset($userData['role'])){
            if(config('cur_apps.default_role')){
                 $userData['role'][1] = [
                     'role_code'=>config('cur_apps.default_role'),
                     'is_main_role'=>1,
                     'has_auth_grant'=>0
                     ];
             }else{
                 $userData['role'][1] = [
                     'role_code'=>config('bssystem.default_apps.default_role'),
                     'is_main_role'=>1,
                     'has_auth_grant'=>0
                     ];
             }
        }
        
        //pastikan rule nya ada
        if(is_array($userData['role'])){
            foreach ($userData['role'] as $key => $value) {
                $role = Role::where('role_code',$value['role_code'])->first();
                if(!$role){
                    $this->error = 'Role not defined.';
                    return false;
                }
            }
        }else {
            $role = Role::where('role_code',$userData['role'])->first();
            if(!$role){
                $this->error = 'Role not defined.';
                return false;
            }
            $userData['role'][1] = [
                'role_code'=>$userData['role'],
                'is_main_role'=>1,
                'has_auth_grant'=>0
                ];
        }
        

        if(isset($userData['email']) && $this->isEmailRegistered($userData['email'])){
            $this->error = 'Email already registered.';
            return false;
        }
        
        if(isset($userData['phone']) && $this->isPhoneRegistered($userData['phone'])){
            $this->error = 'Phone already registered.';
            return false;
        }
        return $userData;
    }
    
    /**
     * 
     * @param array $userData gabungan data users & user_profile
     */
    public function registerProfile(Array $userData)
    {
        $userData = $this->registerProfileFilter($userData);
        
        if(!$userData){
            return false;
        }
        
        UserProfile::create($userData);
    }
    
    /**
     * 
     * @param array $userData
     * @return boolean
     */
    public function registerProfileFilter(Array $userData)
    {
        $validatorRule = [
            'user_id' => 'required',
        ];
        $validator = Validator::make($userData, $validatorRule);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            $err[] = '<ul>';
            foreach ($errors->all() as $message) {
                $err[] = '<li>'.$message.'</li>';
            }
            $err[] = '</ul>';
            $this->error = implode('', $err);
            return false;
        }
        return $userData;
    }

    /**
     * update format nomor telepon menja
     * @param type $phone
     * @return string
     */
    public function phoneFormat($phone)
    {
        $phone = ltrim($phone,'0');
        //jika belum memasukan kode negara maka set indonesia
        if(strpos($phone,'+')===false){
            $phone = '+62'.$phone;
        }
        return $phone;
    }
    
    /**
     * reset password user
     * @param type $userId
     * @param type $newPassword
     */
    public function resetPassword($userId,$newPassword)
    {        
        $user = User::find($userId);
        if(!$user){
            $this->error = __('User tidak ditemukan');
            return false;
            
        }            
        $user->password= Hash::make($newPassword);
        $user->save();
        return true;
    }
    
    /**
     * cek apakah email sudah terdaftar sebelumnya
     * 
     * @param type $email
     * @param type $except
     * @return boolean
     */
    public function isEmailRegistered($email,$except_user_id=false)
    {
        $user = User::where('email',$email);
        
        if($except_user_id){
            $user = $user->where('id','!=',$except_user_id);
        }
        
        if($user->exists()){
            return true;
        }
        
        return false;
    }
    /**
     * cek apakah phone sudah terdaftar sebelumnya
     * 
     * @param string $phone
     * @param type $except
     * @return boolean
     */
    public function isPhoneRegistered($phone,$except_user_id=false)
    {
        $user = User::where('phone',$phone);
        
        if($except_user_id){
            $user = $user->where('id','!=',$except_user_id);
        }
        
        if($user->exists()){
            return true;
        }
                
        return false;
    }
        
    /**
     * do email verification
     * 
     * @param type $email
     * @param type $verifyCode
     * @return boolean
     */
    public function varifyEmail($email,$verifyCode)
    {
        if($this->generateEmailVerfifyCode($email)==$verifyCode){
            $user = User::where('email',$email)->whereNull('email_verified_at')->first();
            if(!$user){
                $this->error = __('auth.emailverify_fail_mailnotfound');
                return false;                
            }            
            $user->{'email_verified_at'} = now()->toDateTimeString();
            $user->save();
            return true;
        }
        $this->error = __('auth.emailverify_fail_varificationcodeinvalid');
        return false;
    }
    
    /**
     * do phone verification with OTP
     * 
     * @param type $phone
     * @param type $otpCode
     * @return boolean
     */
    public function varifyPhone($phone,$otpCode)
    {
        if($this->isOTPValid($phone,$otpCode)){
            $phoneField = 'phone';
            $user = User::where('phone',$phone)->whereNull('phone_verified_at')->first();
            if(!$user){
                $this->error = __('auth.phoneverify_fail_phonenotfound');
                return false;                
            }            
            $user->{'phone_verified_at'} = now()->toDateTimeString();
            $user->save();
            return true;
        }
        $this->error = __('auth.phoneverify_fail_verificationcodeinvalid');
        return false;
    }
    
    
    public function varifyResetPasswordToken($email,$verifyCode,$delete=false)
    {
        //jika match
        if($this->generateEmailVerfifyCode($email)==$verifyCode){
            
            $passwordReset = PasswordReset::where('email',$email)->where('token',$verifyCode)->first();
            if(!$passwordReset){
                $this->error = __('auth.resetpassword_fail_mailnotfound');
                return false;                
            }
            if($delete)$passwordReset->delete();
            return true;
        }
        $this->error = __('auth.resetpassword_fail_verificationcodeinvalid');
        return false;
    }
    /**
     * generate perkiraan user id selanjutanya
     * 
     * @return int perkiraan user id selanjutnya
     */
    public function nextUserId()
    {
        $nextId = $this->model->select('id')->orderBy('id','DESC')->first()->toArray();
        $nextId = $nextId['id'] + 1;
        return $nextId;
    }
    /**
     * generate user_idcode
     * format YYYYMMDD[USER_ID][KARAKTER 0 HINGGA 8 DIGIT][NO URUT PENDAFTARAN HARI INI]
     * 
     * @return int perkiraan user_idcode
     */
    public function generateUserIdcode()
    {
        $count = $this->model->whereDate('created_at', '=', Carbon::today()->toDateString())->count()+1;
        $nextId = $this->nextUserId();
        
        $zero = str_repeat('0',8-strlen($count.$nextId));
        
        $idcode = Carbon::today()->format('Ymd').$nextId.$zero.$count;
        return $idcode;
    }

    /**
     * 
     * @param integer           $userId user id user yang akan diupdate
     * @param array             $userData
     * @param booloean          $dispatchUpdater
     * @return boolean
     */
    public function updateUser($userId, $userData, $dispatchUpdater=true)
    {
        if(isset($userData['role']) && is_array($userData['role'])){
            $userRole = RoleRepo::getUserRoleCode($userId);
            
            $hasIsMainRole = 0;
            foreach ($userData['role'] as $value) {
                //simpan semua nama role yg dipilih untuk keperluan delete role yg tidak dipilih
                $role[] = $value['role_code'];
                if(isset($value['role_code']) && $value['role_code']){
                    //tandai apakah ada is_main_role yg dipilih, jika tidak ada maka
                    //nanti di proses selanjutnya tambahkan is_main_role ke member
                    if(isset($value['is_main_role'])&&$value['is_main_role']){
                        $hasIsMainRole++;
                    }
                    //inputkan role baru yang dipipilih
                    if (!in_array($value['role_code'],$userRole)) {
                        RoleRepo::addUserRole(
                            $userId,
                            $value['role_code'],
                            isset($value['is_main_role'])&&$value['is_main_role']?1:0,
                            isset($value['has_auth_grant'])&&$value['has_auth_grant']?1:0,
                            false);
                    //update role yang memang sebelumnya telah ada
                    }else{
                        
                        RoleRepo::updateUserRole([
                            'user_id'=>$userId,
                            'role_code'=>$value['role_code']
                            ],[
                                'is_main_role'=>isset($value['is_main_role'])&&$value['is_main_role']?1:0,
                                'has_auth_grant'=>isset($value['has_auth_grant'])&&$value['has_auth_grant']?1:0
                            ]);
                    }
                }
            }
            
            foreach ($userRole as $value) {
                //hapus role lama yang tidak dipilih
                if (!in_array($value, $role)) {
                    RoleRepo::deleteUserRole($userId, $value);
                }
            }
            
            //jika tidak memilih main role atau yg dipilih lebih dari 1 maka
            //set members sebagai main role
            if(!$hasIsMainRole || $hasIsMainRole > 1){
                
                RoleRepo::updateUserRole([
                    'user_id'=>$userId,
                    'role_code'=>'member'
                    ],['is_main_role'=>1]);
            }
            unset($userData['role']);
        }

//        if(isset($userData['is_main_role']) && is_array($userData['has_auth_grant'])){
//            foreach ($userData['role_code'] as $key => $value) {
//                if(!in_array($value, $userData['has_auth_grant'])){
//                    UserRole::where(['user_id' => $userId,'role_code' => $value])->update(['has_auth_grant'=>0]);
//                }
//            }
//
//            foreach ($userData['has_auth_grant'] as $item) {
//                if(in_array($item, $userData['role_code'])){
//                    UserRole::where(['user_id' => $userId,'role_code' => $item])->update(['has_auth_grant'=>1]);
//                }
//            }
//
//            UserRole::where('user_id', $userId)->update(['is_main_role'=>0]);
//            UserRole::where(['user_id' => $userId,'role_code' => $userData['is_main_role']])->update(['is_main_role'=>1]);
//        }
                
        if(isset($userData['is_main_role']))unset($userData['is_main_role']);
        if(isset($userData['has_auth_grant']))unset($userData['has_auth_grant']);
        if(isset($userData['role_code']))unset($userData['role_code']);
        if(isset($userData['_token']))unset($userData['_token']);
        if(isset($userData['_method']))unset($userData['_method']);
        
        if(isset($userData['password']))$userData['password']=Hash::make($userData['password']);
        
        $this->_update(new User, $userId, $userData);
                
        if($dispatchUpdater)
            $this->userUpdatedJob($userId);

        return true;
    }

    public function suspendUser($id)
    {
        return User::find($id)->update(['status'=>2]);
    }

    public function updateProfile($userId, $userData)
    {
        $userProfile = $this->_getOne(new UserProfile, $userId);
        if(!$userProfile){
            $this->error = 'User tidak ditemukan';
            return false;
        }
        
        if (isset($userData['avatar']) && !empty($userData['avatar'])) {
            $input['avatar'] = Storage::putFile('images/avatar', $userData['avatar']);
           
            if ($userProfile['avatar'] != null) {
                Storage::delete($userProfile['avatar']);
            }
            unset($userData['avatar']);
        }
        
        foreach ($this->userProfileField as $value) {            
            if(isset($userData[$value]))$input[$value] = $userData[$value];
        }
        
        if(isset($input)){
            $this->_update(new UserProfile, $userId, $input);           
        }
    }

    public function updatePhone($userId, $userData)
    {
        if(isset($userData['phone'])){
            $input = User::find($userId);
            $input->phone = $userData['phone'];
            $input->phone_verified_at = null; 
            $input->update();
        }
        
        return true;
    }

    public function updateEmail($userId, $userData)
    {
        if(isset($userData['email'])){
            $input = User::find($userId);
            $input->email = $userData['email'];
            $input->email_verified_at = null; 
            $input->update();    
        }
        return true;
    }

                    
}