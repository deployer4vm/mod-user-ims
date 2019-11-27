<?php

namespace hpsynapse\moduser\Controllers;

use Illuminate\Http\Request;
use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;
use Facades\hpsynapse\moduser\Services\UserAuth;

use App\Base\BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->forceApiOutput();
    }
    
    /**
     * GET /api/user
     * 
     */
    public function readList(Request $request) {
        
        $orderBy = false;
        $filter = [];

        if($request->input('q', false))
            $filter['q'] = $request->input('q');

        //jika menyertakan status
        if($request->input('status', false))
            $filter[] = ['status', $request->input('status')];
        
        if(UserAuth::isLogin()){
            $filter[] = ['id','!=',UserAuth::user('id')];
            $filter[] = ['level','>',UserAuth::user('level')];
        }     
        
        //jika menyertakan status
        if($request->input('role', false))
            $filter[] = ['role', 'LIKE', '%;'.$request->input('role').';%'];

        //jika multitenant aktif dan bukan dari aplikasi owner maka filter berdasarkan tenant nya
        if (config('AppConfig.system.web_admin.multitenant.active')==1 && config('tenant.id')!=1) {
            $filter['tenant'] = [config('tenant.id')];
        }
        
        //jika menyertakan order by
        if ($request->input('orderBy', false))
            $orderBy = [$request->input('orderBy'), $request->input('orderType', 'ASC')];
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 0);
        
        $this->output['data'] = UserRepo::listUser(
            $filter,
            $limit['offset'],
            $limit['limit'],
            $orderBy
        );

        return $this->done();
    }
    
    /**
     * GET /api/user/{id}
     * 
     * Route Param : 
     *      id : route id
     */
    public function readOne(Request $request)
    {
        $id = $request->route('id');

        $this->output['data'] = UserRepo::getUser($id);
        $this->output['data']['user_role'] = UserRepo::getUserRole($this->output['data']['id']);

        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        } 

        return $this->done();
    }

    /**
     * POST /api/user/
     * 
     * @param Request $request 
     *      name
     *      email
     *      username
     *      password
     *      role_code
     */
    public function create(Request $request)
    {
        $userData = $request->all();//$request->only(['name', 'email', 'password']);

        $validator = [
            'name' => 'required|min:3|max:255',
            'password' => 'required|min:5|max:255'
        ];

        if(isset($userData['username']) && $userData['username']!=''){
            $validator['username'] = 'required|min:3|max:255';
        }

        if(isset($userData['email']) && $userData['email']!=''){
            $validator['email'] = 'required|email|max:255';
        }

        if(!isset($validator['username']) && !isset($validator['email'])){
            $this->setError('Username atau email harus diisi');
            return $this->done();
        }

        $validator = \Validator::make($userData, $validator);

        if ($validator->fails()) {       
            $this->setError('Data keliru',$validator->messages());
            return $this->done();
        }
        
        //jika berhasil
        if ($user = UserRepo::register($userData,false)) {
            $this->setAlert('Data Inserted successfully','success');
        }else{
            $this->setError(UserRepo::error());
        }
        
        return $this->done();
    }

    /**
     * Update user
     */
    public function update(Request $request)
    {
        $id = $request->route('id');

        $input = $request->all();

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }

        if($input['banned_note'] == null){
            unset($input['banned_note']);
        }

        if(UserRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        }

        return $this->done();
    }
    

    public function updateProfile(Request $request)
    {
        
        if(UserAuth::isLogin()){
            $id = UserAuth::user('id');
        }else{
            $this->setError('User belum login');
            return $this->done();
        }

        $input = $request->all();

        if(isset($input['id']))unset($input['id']);
        if(isset($input['created_at']))unset($input['created_at']);
        if(isset($input['updated_at']))unset($input['updated_at']);

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }
        
        if(isset($input['role_code']))unset($input['role_code']);
        if(isset($input['status']))unset($input['status']);
        
        if(UserRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        }

        return $this->done();
    }
    
    /**
     * update password
     * 
     * @param Request $request
     *      password
     *      password_confirmatin
     */
    public function updatePassword(Request $request)
    {
        $id = $request->route('id');
        $userData = $request->only(['password','password_confirmation']);

        $validator = \Validator::make($userData, [
            'password' => 'required|min:8|max:255',
            'password_confirmation' => 'required|min:8|max:255|same:password'
        ]);

        if ($validator->fails()) {
            $this->setError('Input Error :',$validator->messages(),400,true);
            return $this->done();
        }

        $change = UserRepo::updateUser($id, $userData);
        if (!$change) {
            return $this->done();
        }

        $this->setAlert('Password Updated successfully','success');
        return $this->done();
    }

    public function ban(Request $request)
    {
        $id = $request->route('id');
        UserRepo::banUser($id,$request->input('banned_note',''));
        $this->setAlert('User banned successfully','success');
        return $this->done();
    }

    public function unban(Request $request)
    {
        $id = $request->route('id');
        UserRepo::unbanUser($id);
        $this->setAlert('User unbanned successfully','success');
        return $this->done();
    }

    public function delete(Request $request)
    {

        $id = $request->route('id');
           
        if(UserAuth::isLogin() && $id != UserAuth::user('id')){
            $filter[] = ['id', $id];
            $filter[] = ['level','>',UserAuth::user('level')];
            $data = UserRepo::listUser($filter);
            if($data['count']<=0){
                $this->setError('Permission denied');
                return $this->done();;
            }
        }   

        if(!UserRepo::deleteUser($id)){
            $this->setError('Error : '.UserRepo::error());
        }
        return $this->done();
           
    }
    
    public function changeRole(Request $request)
    {
        $roleCode = $request->route('role_code');
        $backLink = $request->input('backlink',false);
        $this->response = $backLink?redirect($backLink):back();

        if(UserAuth::setActiveRole($roleCode)){
            $roleName = UserAuth::role($roleCode)['name'];
            $this->setAlert('Role <b>'.$roleName.'</b> berhasil diaktifkan','success');

        }else{
            $this->setAlert('Role tidak ditemukan','danger');
        }

        return $this->done();
    }
}
